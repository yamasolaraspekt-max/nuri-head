<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Mail\VideoCallInvite as VideoCallInviteMail;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\NewLeads;
use App\Models\User;
use App\Models\VideoCall;
use App\Models\VideoCallInvite;
use App\Services\JitsiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Jitsi-Videocall. Alle Routen (außer Gast) hinter `auth` (F5, kein permission:-Gate im MVP).
 * Bei JITSI_ENABLED=false: 404 für alle Routen, Button unsichtbar (Prüfung in jeder Methode).
 */
class VideoCallController extends Controller
{
    public function __construct(private JitsiService $jitsi)
    {
    }

    /** POST /new-leads/{newLead}/video-calls — Call anlegen + Chat-Systemnachricht (F2). */
    public function store(Request $request, NewLeads $newLead)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        $user = $request->user();
        $call = $this->jitsi->createCall($newLead, $user);

        // F2: defensiv — Chat-Nachricht darf die Call-Erstellung nie abbrechen.
        $this->notifyCustomerGroups(
            $newLead,
            $user,
            'Video-Call gestartet von ' . $this->actorName($user)
                . ' ##VIDEOCALL:' . route('video-calls.show', $call) . '##'
        );

        return redirect()->route('video-calls.show', $call);
    }

    /** GET /video-calls/{videoCall} — Mitarbeiter-Ansicht (Moderator). */
    public function show(Request $request, VideoCall $videoCall)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        if ($videoCall->status === VideoCall::STATUS_CREATED) {
            $videoCall->update([
                'status' => VideoCall::STATUS_ACTIVE,
                'started_at' => $videoCall->started_at ?: now(),
            ]);
        }

        $user = $request->user();
        $jwt = $this->jitsi->buildJwt(
            $videoCall,
            $this->actorName($user),
            $user->email ?? null,
            true // Mitarbeiter = Moderator
        );

        return view('admin.video-calls.show', [
            'videoCall' => $videoCall->load(['customer', 'peer', 'chatGroup']),
            'jwt' => $jwt,
            'displayName' => $this->actorName($user),
            // Gast-Link nur für Kunden-Calls; interne Calls haben keinen Externen-Zugang.
            'guestUrl' => $videoCall->isInternal() ? null : $this->jitsi->guestUrl($videoCall),
        ]);
    }

    /** POST /video-calls/{videoCall}/ende — beenden + Chat-Systemnachricht mit Dauer. */
    public function ende(Request $request, VideoCall $videoCall)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        if ($videoCall->status !== VideoCall::STATUS_ENDED) {
            $videoCall->update([
                'status' => VideoCall::STATUS_ENDED,
                'ended_at' => now(),
            ]);

            $duration = $videoCall->fresh()->durationHuman();
            $this->notifyCustomerGroups(
                $videoCall->customer,
                $request->user(),
                'Video-Call beendet' . ($duration ? ' (Dauer: ' . $duration . ')' : '')
            );
        }

        return response()->json(['ok' => true, 'status' => $videoCall->status]);
    }

    /** POST /video-calls/{videoCall}/einladungen — Phase 3b: Gast-Einladungen per E-Mail. */
    public function einladungen(Request $request, VideoCall $videoCall)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        $data = $request->validate([
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*.email' => ['required', 'email'],
            'recipients.*.name' => ['nullable', 'string', 'max:255'],
        ]);

        $results = [];
        foreach ($data['recipients'] as $recipient) {
            $invite = VideoCallInvite::create([
                'video_call_id' => $videoCall->getKey(),
                'name' => $recipient['name'] ?? null,
                'email' => $recipient['email'],
                'sent_at' => null,
                'created_by' => $request->user()->getKey(),
            ]);

            $guestUrl = $this->jitsi->guestUrl($videoCall, $invite->name);

            // F3: queue.default=sync → Versand synchron. Mail-Fehler darf den Request nicht
            // mit 500 abbrechen; sent_at bleibt null, Fehler wird gemeldet.
            $sent = false;
            $error = null;
            try {
                Mail::to($invite->email)->send(new VideoCallInviteMail($videoCall, $invite, $guestUrl));
                $invite->update(['sent_at' => now()]);
                $sent = true;

                $this->notifyCustomerGroups(
                    $videoCall->customer,
                    $request->user(),
                    'Einladung an ' . $invite->email
                );
            } catch (\Throwable $e) {
                Log::warning('VideoCall-Einladung konnte nicht gesendet werden', [
                    'invite_id' => $invite->getKey(),
                    'error' => $e->getMessage(),
                ]);
                $error = 'Versand fehlgeschlagen.';
            }

            $results[] = [
                'email' => $invite->email,
                'sent' => $sent,
                'error' => $error,
            ];
        }

        $anyFailed = collect($results)->contains(fn ($r) => !$r['sent']);

        return response()->json([
            'ok' => !$anyFailed,
            'results' => $results,
            'message' => $anyFailed
                ? 'Mindestens eine Einladung konnte nicht gesendet werden.'
                : 'Einladungen versendet.',
        ], $anyFailed ? 207 : 200);
    }

    /**
     * POST /video-calls/intern — interner Call (Kollege ↔ Kollege).
     * Payload: genau eines von to_user_id (1:1) oder group_id (Gruppe). auth genügt (F5).
     */
    public function intern(Request $request, JitsiService $jitsi)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        $data = $request->validate([
            'to_user_id' => ['nullable', 'integer', 'exists:users,id', 'required_without:group_id'],
            'group_id' => ['nullable', 'integer', 'exists:chat_groups,id', 'required_without:to_user_id'],
        ]);

        if (!empty($data['to_user_id']) === !empty($data['group_id'])) {
            abort(422, 'Genau ein Ziel angeben: to_user_id ODER group_id.');
        }

        $user = $request->user();
        $peer = !empty($data['to_user_id']) ? User::find($data['to_user_id']) : null;
        $group = !empty($data['group_id']) ? ChatGroup::find($data['group_id']) : null;

        $call = $jitsi->createInternalCall($user, $peer, $group);

        $message = $this->actorName($user) . ' lädt zum Video-Call ein'
            . ' ##VIDEOCALL:' . route('video-calls.show', $call) . '##';

        // Bestehender type='video_call'-Weg (MessageSent/Reverb) — 1:1 ODER Gruppe.
        $this->postChatMessage(
            $user,
            $peer?->getKey(),
            $group?->getKey(),
            $message
        );

        return redirect()->route('video-calls.show', $call);
    }

    /** GET /video-call/gast/{videoCall} [signiert] — Gast-Ansicht ohne Login. */
    public function guest(Request $request, VideoCall $videoCall)
    {
        abort_unless((bool) config('jitsi.enabled'), 404);

        // Interne Calls haben KEINEN Gast-Zugang (kein Externen-Zugang zu internen Gesprächen).
        abort_if($videoCall->isInternal(), 404);

        // F5: signierter Link — manuelle Prüfung, damit wir eine saubere deutsche Fehlerseite
        // rendern können (statt globaler 403-Seite / Stacktrace).
        if (!$request->hasValidSignature()) {
            return response()->view('video-calls.guest-error', ['reason' => 'expired'], 403);
        }

        if (!$videoCall->isJoinable()) {
            return response()->view('video-calls.guest-error', ['reason' => 'ended'], 410);
        }

        $customer = $videoCall->customer;
        $displayName = trim((string) $request->query('name'))
            ?: trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''));
        $displayName = $displayName !== '' ? $displayName : 'Gast';

        $jwt = $this->jitsi->buildJwt($videoCall, $displayName, null, false); // Gast = kein Moderator

        return view('video-calls.guest', [
            'videoCall' => $videoCall,
            'displayName' => $displayName,
            'jwt' => $jwt,
        ]);
    }

    /**
     * F2: In alle Chat-Gruppen des Kunden eine Systemnachricht schreiben + broadcasten.
     * Kein Treffer → nichts tun (kein Fehler, kein Log-Spam).
     */
    private function notifyCustomerGroups(?NewLeads $customer, $actor, string $message): void
    {
        if (!$customer) {
            return;
        }

        foreach (ChatGroup::where('customer_id', $customer->getKey())->get() as $group) {
            $this->postChatMessage($actor, null, $group->getKey(), $message);
        }
    }

    /**
     * Eine type='video_call'-Systemnachricht anlegen + über den bestehenden MessageSent/Reverb-Weg
     * broadcasten. Ziel: 1:1 (toUserId) ODER Gruppe (groupId). Vollständig defensiv — die
     * Hauptaktion (Call) darf nie an Chat/Broadcast scheitern.
     */
    private function postChatMessage($actor, ?int $toUserId, ?int $groupId, string $message): void
    {
        if (!$toUserId && !$groupId) {
            return;
        }

        try {
            $chat = Chat::create([
                'from_user_id' => optional($actor)->getKey(),
                'to_user_id' => $toUserId,
                'group_id' => $groupId,
                'reply_to_id' => null,
                'type' => 'video_call',
                'message' => $message,
                'file_path' => null,
                'status' => 'sent',
                'is_read' => false,
            ]);

            broadcast(new MessageSent($chat));
        } catch (\Throwable $e) {
            Log::warning('VideoCall-Chatnachricht fehlgeschlagen', ['error' => $e->getMessage()]);
        }
    }

    /** Anzeigename des Mitarbeiters (Employee-Name, sonst User-Name). */
    private function actorName($user): string
    {
        $employee = optional($user)->employee;
        $name = trim(((string) optional($employee)->name) . ' ' . ((string) optional($employee)->lastname));

        return $name !== '' ? $name : (string) (optional($user)->name ?? 'Mitarbeiter');
    }
}

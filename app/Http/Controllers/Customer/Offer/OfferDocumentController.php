<?php

namespace App\Http\Controllers\Customer\Offer;


use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Offer;
use App\Models\OfferDetail;
use App\Models\OfferFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use DB;

class OfferDocumentController extends Controller
{
    protected function presenceKey(?int $offerId, ?int $folderId): string
    {
        return 'offer_presence:' . ($folderId ? 'folder_' . $folderId : 'offer_' . $offerId);
    }

    protected function lockKey(?int $offerId, ?int $folderId): string
    {
        return 'offer_lock:' . ($folderId ? 'folder_' . $folderId : 'offer_' . $offerId);
    }

    protected function resolvePresenceUser($user): array
    {
        $employeeId = is_numeric($user->name ?? null) ? (int) $user->name : null;

        $employee = $employeeId
            ? Employee::find($employeeId)
            : null;

        $fullName = $employee
            ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
            : trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));

        if ($fullName === '') {
            $fullName = $employee?->name
                ?: ($user->username ?? null)
                ?: ($user->email ?? null)
                ?: 'Benutzer #' . $user->id;
        }

        $avatar = null;

        if ($employee && !empty($employee->image)) {
            $avatar = asset('images/employee/' . ltrim($employee->image, '/'));
        }

        if (!$avatar) {
            $avatar = asset('images/gender/male.png');
        }

        return [
            'id' => $user->id,
            'employee_id' => $employee?->id,
            'name' => $fullName,
            'avatar' => $avatar,
            'last_seen' => now()->timestamp,
        ];
    }

    protected function cleanUsers(array $users): array
    {
        foreach ($users as $id => $row) {
            if (($row['last_seen'] ?? 0) < now()->subMinutes(2)->timestamp) {
                unset($users[$id]);
            }
        }

        return $users;
    }

  public function load(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');
        
        // Check if the frontend specifically requested the snapshot
        $loadSnapshot = $request->boolean('load_snapshot');

        $detail = OfferDetail::query()
            ->with(['folder']) // 🟢 CRITICAL: Pre-load the folder relationship
            ->when($folderId, fn ($q) => $q->where('offer_folder_id', $folderId))
            ->when(!$folderId && $offerId, fn ($q) => $q->where('offer_id', $offerId))
            ->latest('id')
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => true,
                'found' => false,
                'data' => null,
            ]);
        }

        $detail->load([
            'offer.customer',
            'offer.alternative',
            'offer.product',
            'folder.creator',
        ]);

        // Decide which data to feed the editor
        $sectionsData = $loadSnapshot 
            ? ($detail->angebot_snapshot_sections ?? []) 
            : ($detail->sections ?? []);

        // 🟢 1. GET THE TRUE STATUS FROM THE FOLDER (Fallback to detail if missing)
       $trueDocumentStatus = $detail->folder ? $detail->folder->document_status : $detail->document_status;

        // 🟢 2. FETCH THE DEAL ORDER NUMBER IF IT'S A DEAL OR AUFTRAG
        $deal = null;
        if (in_array($trueDocumentStatus, ['deal', 'auftrag']) && $detail->offer) {
            $deal = \DB::table('deals')
                ->where('customer_id', $detail->offer->customer_id)
                ->where('alternative_id', $detail->offer->alternative_id)
                ->where('product_id', $detail->offer->product_id)
                ->whereNotNull('order_number') // ✅ IGNORE rows where order_number is empty
                ->latest('id')                 // ✅ Grab the newest deal just to be safe
                ->select('order_number') 
                ->first();
        }
  
        
        return response()->json([
            'success' => true,
            'found' => true,
            'data' => [
                'id' => $detail->id,
                'offer_id' => $detail->offer_id,
                'offer_folder_id' => $detail->offer_folder_id,

                // 🟢 3. SEND THE AUFTRAGSNUMMER TO THE FRONTEND
                'order_number' => $deal ? $deal->order_number : null,
                'offer_no' => $detail->offer_no,
                'brand_color' => $detail->brand_color,
                'brand_mode' => $detail->brand_mode,
                'brand_logo_url' => $detail->brand_logo_url,
                'company_name' => $detail->company_name,
                'cover_text' => $detail->cover_text,
                'cover_text_html' => $detail->cover_text_html,
                
                // 🟢 4. SEND THE TRUE DOCUMENT STATUS
                'document_status' => $trueDocumentStatus,
                'is_snapshot' => $loadSnapshot,

                // Output the chosen sections data
                'sections' => $sectionsData,
                
                'placed_images' => $detail->placed_images ?? [],

                'total_net' => $detail->total_net,
                'tax_rate' => $detail->tax_rate,
                'total_gross' => $detail->total_gross,

                'offer' => $detail->offer ? [
                    'id' => $detail->offer->id,
                    'customer_id' => $detail->offer->customer_id,
                    'alternative_id' => $detail->offer->alternative_id,
                    'product_id' => $detail->offer->product_id ?? null,
                    'customer' => $detail->offer->customer ? [
                        'id' => $detail->offer->customer->id,
                        'name' => trim(
                            ($detail->offer->customer->firma ?? '') ?:
                            (($detail->offer->customer->name ?? '') . ' ' . ($detail->offer->customer->lastname ?? ''))
                        ),
                        'display_name' => trim(
                            ($detail->offer->customer->firma ?? '') ?:
                            (($detail->offer->customer->name ?? '') . ' ' . ($detail->offer->customer->lastname ?? ''))
                        ),
                        'customer_no' => $detail->offer->customer->customer_no,
                        'street' => $detail->offer->customer->street,
                        'postcode' => $detail->offer->customer->postcode,
                        'city' => $detail->offer->customer->city,
                        'lastname' => $detail->offer->customer->lastname,
                    ] : null,
                ] : null,
            ],
        ]);
    }

  public function biography(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');

        // 1. Fetch the latest document detail to get the stored action logs
        $detail = OfferDetail::query()
            ->when($folderId, fn ($q) => $q->where('offer_folder_id', $folderId))
            ->when(!$folderId && $offerId, fn ($q) => $q->where('offer_id', $offerId))
            ->latest('id')
            ->first();

        $folder = $folderId ? OfferFolder::with('creator')->find($folderId) : null;
        $offer  = $offerId ? Offer::with('customer')->find($offerId) : null;

        $timeline = [];

        // 2. Add Detailed Action Logs (The notifications you sent via JS)
        // We check if biography_data exists and is an array (thanks to the model cast)
        if ($detail && is_array($detail->biography_data)) {
            foreach ($detail->biography_data as $log) {
                $timeline[] = [
                    'type'    => 'user_action',
                    'title'   => $log['title'] ?? 'Änderung',
                    'text'    => $log['text'] ?? '',
                    'date'    => $log['date'] ?? '',
                    'isLocal' => false // Saved items are rendered as permanent history
                ];
            }
        }

        // 3. Add System Milestones (Original Logic)
        if ($offer) {
            $timeline[] = [
                'type' => 'offer_created',
                'title' => 'Angebot erstellt',
                'text' => 'Das Angebot wurde im System angelegt.',
                'date' => optional($offer->created_at)->format('d.m.Y H:i'),
            ];
        }

        if ($folder) {
            $timeline[] = [
                'type' => 'folder_created',
                'title' => 'Ordner erstellt',
                'text' => 'Projektordner von ' . trim(($folder->creator->name ?? '') . ' ' . ($folder->creator->lastname ?? '')),
                'date' => optional($folder->created_at)->format('d.m.Y H:i'),
            ];
        }

        if ($detail) {
            $timeline[] = [
                'type' => 'document_saved',
                'title' => 'Letzte Speicherung',
                'text' => 'Der aktuelle Stand des Dokuments wurde gesichert.',
                'date' => optional($detail->updated_at)->format('d.m.Y H:i'),
            ];
        }

        // 4. Sort the entire timeline by date (Newest first)
        // We use strtotime to handle different date string formats reliably
        usort($timeline, function ($a, $b) {
            $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
            $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
            return $dateB <=> $dateA;
        });

        return response()->json([
            'success' => true,
            'items'   => $timeline,
        ]);
    }

  public function presenceStatus(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');

        if (!$offerId && !$folderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing offer_id or offer_folder_id',
            ], 422);
        }

        $user = Auth::user();
        $presenceKey = $this->presenceKey($offerId, $folderId);
        $lockKey = $this->lockKey($offerId, $folderId);

        $users = Cache::get($presenceKey, []);
        $users = $this->cleanUsers($users);

        // persist cleaned users back
        Cache::put($presenceKey, $users, now()->addMinutes(3));

        $lock = Cache::get($lockKey);

        // remove stale timeout-based lock
        if ($lock && ($lock['last_seen'] ?? 0) < now()->subMinutes(2)->timestamp) {
            Cache::forget($lockKey);
            $lock = null;
        }

        // remove lock if lock owner is no longer in active users
        if ($lock) {
            $lockUserId = (int) ($lock['id'] ?? 0);

            if (!isset($users[$lockUserId])) {
                Cache::forget($lockKey);
                $lock = null;
            }
        }

        $lockedByOther = $lock && (int) ($lock['id'] ?? 0) !== (int) $user->id;

        return response()->json([
            'success' => true,
            'locked' => (bool) $lock,
            'locked_by_other' => $lockedByOther,
            'lock_user' => $lock ?: null,
            'users' => array_values($users),
            'current_user_id' => $user->id,
        ]);
    }

   public function presencePing(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');

        if (!$offerId && !$folderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing offer_id or offer_folder_id'
            ], 422);
        }

        $user = Auth::user();
        $presenceKey = $this->presenceKey($offerId, $folderId);
        $lockKey = $this->lockKey($offerId, $folderId);

        $resolvedUser = $this->resolvePresenceUser($user);

        $users = Cache::get($presenceKey, []);
        $users = $this->cleanUsers($users);

        $users[$user->id] = $resolvedUser;

        Cache::put($presenceKey, $users, now()->addMinutes(3));

        $lock = Cache::get($lockKey);

        if ($lock && ($lock['last_seen'] ?? 0) < now()->subMinutes(2)->timestamp) {
            Cache::forget($lockKey);
            $lock = null;
        }

        // if current lock owner is no longer online, remove lock
        if ($lock) {
            $lockUserId = (int) ($lock['id'] ?? 0);
            if (!isset($users[$lockUserId])) {
                Cache::forget($lockKey);
                $lock = null;
            }
        }

        if (!$lock) {
            Cache::put($lockKey, $resolvedUser, now()->addMinutes(3));
            $lock = $resolvedUser;
        } elseif ((int) ($lock['id'] ?? 0) === (int) $user->id) {
            Cache::put($lockKey, $resolvedUser, now()->addMinutes(3));
            $lock = $resolvedUser;
        }

        $lockedByOther = $lock && (int) ($lock['id'] ?? 0) !== (int) $user->id;

        return response()->json([
            'success' => true,
            'users' => array_values($users),
            'locked' => (bool) $lock,
            'locked_by_other' => $lockedByOther,
            'lock_user' => $lock ?: null,
            'current_user' => $resolvedUser,
        ]);
    }

   public function onlineUsers(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');

        if (!$offerId && !$folderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing offer_id or offer_folder_id'
            ], 422);
        }

        $presenceKey = $this->presenceKey($offerId, $folderId);
        $lockKey = $this->lockKey($offerId, $folderId);

        $users = Cache::get($presenceKey, []);
        $users = $this->cleanUsers($users);
        Cache::put($presenceKey, $users, now()->addMinutes(3));

        $lock = Cache::get($lockKey);

        if ($lock && ($lock['last_seen'] ?? 0) < now()->subMinutes(2)->timestamp) {
            Cache::forget($lockKey);
            $lock = null;
        }

        if ($lock) {
            $lockUserId = (int) ($lock['id'] ?? 0);
            if (!isset($users[$lockUserId])) {
                Cache::forget($lockKey);
                $lock = null;
            }
        }

        return response()->json([
            'success' => true,
            'users' => array_values($users),
            'locked' => (bool) $lock,
            'lock_user' => $lock ?: null,
        ]);
    }
    public function presenceLeave(Request $request)
    {
        $offerId = $request->integer('offer_id');
        $folderId = $request->integer('offer_folder_id');

        if (!$offerId && !$folderId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing offer_id or offer_folder_id'
            ], 422);
        }

        $user = Auth::user();
        $presenceKey = $this->presenceKey($offerId, $folderId);
        $lockKey = $this->lockKey($offerId, $folderId);

        $users = Cache::get($presenceKey, []);
        unset($users[$user->id]);
        Cache::put($presenceKey, $users, now()->addMinutes(1));

        $lock = Cache::get($lockKey);
        if ($lock && (int) ($lock['id'] ?? 0) === (int) $user->id) {
            Cache::forget($lockKey);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
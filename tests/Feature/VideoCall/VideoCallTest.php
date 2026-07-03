<?php

namespace Tests\Feature\VideoCall;

use App\Models\ChatGroup;
use App\Models\NewLeads;
use App\Models\User;
use App\Models\VideoCall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VideoCallTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Feature aktiv, aber ohne echten Broadcast/JWT im Test.
        config([
            'jitsi.enabled' => true,
            'jitsi.jwt_enabled' => false,
            'broadcasting.default' => 'null',
        ]);
    }

    private function user(): User
    {
        // Klartext-Passwort: umgeht das "hashed"-Cast-Problem im Test-Env
        // (Factory-Default ist bereits gehasht -> verifyConfiguration schlaegt fehl).
        return User::factory()->create(['password' => 'password']);
    }

    private function customer(): NewLeads
    {
        return NewLeads::create([
            'customer_type' => 'private',
            'name' => 'Max',
            'lastname' => 'Muster',
            'email' => 'max@example.com',
        ]);
    }

    /** Call-Erstellung OHNE ChatGroup → kein Fehler, keine Chat-Nachricht. */
    public function test_customer_call_without_chatgroup_creates_call_but_no_message(): void
    {
        $user = $this->user();
        $customer = $this->customer();

        $response = $this->actingAs($user)->post(route('video-calls.store', $customer));

        $this->assertDatabaseHas('video_calls', [
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'status' => VideoCall::STATUS_CREATED,
        ]);
        $call = VideoCall::first();
        $response->assertRedirect(route('video-calls.show', $call));

        // Keine Gruppe → keine Nachricht.
        $this->assertDatabaseCount('chats', 0);
    }

    /** Call-Erstellung MIT ChatGroup → video_call-Nachricht in genau dieser Gruppe. */
    public function test_customer_call_with_chatgroup_writes_message_into_that_group(): void
    {
        $user = $this->user();
        $customer = $this->customer();
        $group = ChatGroup::create([
            'name' => 'Kundengruppe',
            'created_by' => $user->id,
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($user)->post(route('video-calls.store', $customer));

        $this->assertDatabaseHas('chats', [
            'group_id' => $group->id,
            'to_user_id' => null,
            'type' => 'video_call',
        ]);
        $this->assertDatabaseCount('chats', 1);
    }

    /** Gast-Route ohne gültige Signatur → 403 (saubere Fehlerseite). */
    public function test_guest_route_without_valid_signature_is_forbidden(): void
    {
        $user = $this->user();
        $customer = $this->customer();
        $call = VideoCall::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'room_name' => 'sa-' . $customer->id . '-test',
            'status' => VideoCall::STATUS_ACTIVE,
        ]);

        $this->get(route('video-call.guest', $call))->assertStatus(403);
    }

    /** Gast-Route mit gültiger Signatur → 200. */
    public function test_guest_route_with_valid_signature_renders(): void
    {
        $user = $this->user();
        $customer = $this->customer();
        $call = VideoCall::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'room_name' => 'sa-' . $customer->id . '-test2',
            'status' => VideoCall::STATUS_ACTIVE,
        ]);

        $url = URL::temporarySignedRoute('video-call.guest', now()->addMinutes(30), ['videoCall' => $call->id]);
        $this->get($url)->assertStatus(200);
    }

    /** JITSI_ENABLED=false → alle Routen abort(404). */
    public function test_routes_abort_when_feature_disabled(): void
    {
        config(['jitsi.enabled' => false]);
        $user = $this->user();
        $customer = $this->customer();

        $this->actingAs($user)->post(route('video-calls.store', $customer))->assertStatus(404);
    }

    /** F5: Nicht eingeloggt → kein Zugriff (Redirect zum Login). */
    public function test_unauthenticated_cannot_create_call(): void
    {
        $customer = $this->customer();
        $this->post(route('video-calls.store', $customer))->assertStatus(302);
    }

    /** F3: Mail-Fehler → Request endet NICHT in 500; Invite bleibt mit sent_at=null. */
    public function test_invite_mail_failure_does_not_500_and_keeps_sent_at_null(): void
    {
        $user = $this->user();
        $customer = $this->customer();
        $call = VideoCall::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'room_name' => 'sa-' . $customer->id . '-inv',
            'status' => VideoCall::STATUS_ACTIVE,
        ]);

        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $response = $this->actingAs($user)->postJson(route('video-calls.einladungen', $call), [
            'recipients' => [['email' => 'gast@example.com', 'name' => 'Gast']],
        ]);

        $this->assertNotEquals(500, $response->getStatusCode());
        $this->assertDatabaseHas('video_call_invites', [
            'video_call_id' => $call->id,
            'email' => 'gast@example.com',
            'sent_at' => null,
        ]);
    }

    /** Mitarbeiter-Ansicht (show) rendert ohne Fehler (Blade-Render-Nachweis). */
    public function test_show_view_renders_for_authenticated_user(): void
    {
        $user = $this->user();
        $customer = $this->customer();
        $call = VideoCall::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'room_name' => 'sa-' . $customer->id . '-show',
            'status' => VideoCall::STATUS_CREATED,
        ]);

        $this->actingAs($user)
            ->get(route('video-calls.show', $call))
            ->assertStatus(200)
            ->assertSee('Video-Call');
    }

    /** Interner 1:1-Call → video_call-Nachricht im 1:1-Chat (to_user_id=Peer). */
    public function test_internal_direct_call_writes_message_to_peer_chat(): void
    {
        $user = $this->user();
        $peer = $this->user();

        $response = $this->actingAs($user)->post(route('video-calls.intern'), [
            'to_user_id' => $peer->id,
        ]);

        $call = VideoCall::first();
        $response->assertRedirect(route('video-calls.show', $call));
        $this->assertTrue($call->isInternal());
        $this->assertEquals($peer->id, $call->peer_user_id);

        $this->assertDatabaseHas('chats', [
            'from_user_id' => $user->id,
            'to_user_id' => $peer->id,
            'group_id' => null,
            'type' => 'video_call',
        ]);
    }

    /** Interner Gruppen-Call → video_call-Nachricht in der Gruppe. */
    public function test_internal_group_call_writes_message_to_group(): void
    {
        $user = $this->user();
        $group = ChatGroup::create([
            'name' => 'Team',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->post(route('video-calls.intern'), [
            'group_id' => $group->id,
        ]);

        $call = VideoCall::first();
        $this->assertEquals($group->id, $call->chat_group_id);
        $this->assertDatabaseHas('chats', [
            'group_id' => $group->id,
            'type' => 'video_call',
        ]);
    }

    /** Gast-Route für einen INTERNEN Call → 404 (kein Externen-Zugang). */
    public function test_guest_route_for_internal_call_is_not_found(): void
    {
        $user = $this->user();
        $peer = $this->user();
        $call = VideoCall::create([
            'created_by' => $user->id,
            'peer_user_id' => $peer->id,
            'room_name' => 'sa-intern-test',
            'status' => VideoCall::STATUS_ACTIVE,
        ]);

        $url = URL::temporarySignedRoute('video-call.guest', now()->addMinutes(30), ['videoCall' => $call->id]);
        $this->get($url)->assertStatus(404);
    }
}

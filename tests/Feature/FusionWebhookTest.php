<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class FusionWebhookTest extends TestCase
{
    public function test_webhook_returns_entries_with_valid_token()
    {
        // Create dummy data
        $formId = DB::table('wp_fusion_forms')->insertGetId([
            'form_id' => 999,
            'views' => 0,
            'submissions_count' => 1,
            'data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $submissionId = DB::table('wp_fusion_form_submissions')->insertGetId([
            'form_id' => $formId,
            'source_url' => 'https://example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fieldId = DB::table('wp_fusion_form_fields')->insertGetId([
            'form_id' => $formId,
            'field_name' => 'email',
            'field_label' => 'Email',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wp_fusion_form_entries')->insert([
            'submission_id' => $submissionId,
            'form_id' => $formId,
            'field_id' => $fieldId,
            'value' => 'test@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'X-Fusion-Token' => config('services.fusion_forms.token')
        ])->postJson('/api/fusion-form/webhook/entries', [
            'form_id' => $formId
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'form_id',
                     'fields',
                     'entries'
                 ]);
    }

    public function test_webhook_rejects_invalid_token()
    {
        $response = $this->withHeaders([
            'X-Fusion-Token' => 'invalid-token'
        ])->postJson('/api/fusion-form/webhook/entries', [
            'form_id' => 999
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }
}

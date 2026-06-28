<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessFusionEntry;
use Illuminate\Support\Facades\Log;
use App\Models\WpFusionFormEntry;


class FusionWebhookController extends Controller
{
   public function getEntries(Request $request)
    {
        $providedToken = $request->header('X-Fusion-Token');
        $validToken = config('services.fusion_forms.token');

        if (!$providedToken || $providedToken !== $validToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $formId = $request->input('form_id');
        if (!$formId) {
            return response()->json(['error' => 'Missing form_id'], 400);
        }

        $fields = DB::table('wp_fusion_form_fields')
            ->where('form_id', $formId)
            ->select('id', 'field_name', 'field_label')
            ->get();

        $entries = DB::table('wp_fusion_form_entries')
            ->leftJoin('wp_fusion_form_submissions', 'wp_fusion_form_entries.submission_id', '=', 'wp_fusion_form_submissions.id')
            ->leftJoin('wp_fusion_form_fields', 'wp_fusion_form_entries.field_id', '=', 'wp_fusion_form_fields.id')
            ->where('wp_fusion_form_entries.form_id', $formId)
            ->select(
                'wp_fusion_form_entries.submission_id',
                'wp_fusion_form_entries.field_id',
                'wp_fusion_form_entries.value',
                'wp_fusion_form_submissions.source_url',
                'wp_fusion_form_submissions.created_at as submitted_at',
                'wp_fusion_form_fields.field_label'
            )
            ->orderByDesc('wp_fusion_form_submissions.created_at')
            ->get()
            ->groupBy('submission_id');

        return response()->json([
            'form_id' => $formId,
            'fields' => $fields,
            'entries' => $entries,
        ]);
    }



    public function receive(Request $request)
    {
        $entries = $request->input('entries', []);
        $count = 0;

        foreach ($entries as $entry) {
            WpFusionFormEntry::updateOrCreate(
                [
                    'submission_id' => $entry['submission_id'],
                    'field_id' => $entry['field_id'],
                ],
                [
                    'form_id' => $entry['form_id'],
                    'value' => $entry['value'] ?? null,
                    'privacy' => $entry['privacy'] ?? null,
                    'data' => is_array($entry['data']) ? json_encode($entry['data']) : $entry['data'],
                ]
            );
            $count++;
        }

        Log::info("Webhook received $count entries");

        return response()->json(['status' => 'ok', 'imported' => $count]);
    }


    public function webhook(Request $request)
    {
        $token = $request->header('X-Fusion-Token');

        if ($token !== config('services.fusion_forms.token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $entries = $request->input('entries', []);

        foreach ($entries as $entry) {
            ProcessFusionEntry::dispatch($entry);
        }

        return response()->json(['message' => 'Entries accepted']);
    }

     public function handleAjax(Request $request)
    {
        $token = $request->header('X-Fusion-Token');

        if ($token !== config('services.fusion_forms.token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Your sync logic here — this is just an example:
        // e.g., fetch entries from external API and store to DB
        $count = 0; // you can replace this with actual synced count

        return response()->json(['message' => 'Synced successfully', 'count' => $count]);
    }
}

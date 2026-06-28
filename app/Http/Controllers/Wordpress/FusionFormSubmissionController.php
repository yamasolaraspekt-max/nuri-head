<?php

namespace App\Http\Controllers\Wordpress;
use App\Http\Controllers\Controller;

use App\Models\FusionFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WpFusionForm; 
use App\Models\WpFusionFormField;
use App\Models\WpFusionFormEntry;
use App\Models\WpFusionFormSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;

class FusionFormSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 

    public function show($id)
    {
        $submission = FusionFormSubmission::findOrFail($id);
        return view('admin.fusion.show', compact('submission'));
    }



       public function index()
    { 
        return view('admin.fusion.index');
    }

    public function ajaxImportFromGoneo()
    {
        $url = 'https://www.solar-aspekt.de/fusion-api.php?token=3Ho7JAeHMxL8kkjRDx83fhwq';

        $response = Http::timeout(10)->get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Could not fetch leads from Goneo.'], 500);
        }

        // ✅ Return only the decoded array, not wrapped
        return response()->json($response->json());
    }

 
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $token = $request->header('X-Fusion-Form-Token');
        $expectedToken = config('services.fusion_forms.token');

        if (!$token || $token !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Save the full form submission as JSON
        FusionFormSubmission::create([
            'form_data' => $request->all(),
        ]);

        return response()->json(['message' => 'Form saved successfully']);
    }

    public function syncForms()
    {
        $response = Http::timeout(60)->get('https://www.solar-aspekt.de/fusion-api-forms.php?token=3Ho7JAeHMxL8kkjRDx83fhwq');
        $forms = $response->json();
        $count = 0;

        foreach ($forms as $form) {
            $rawData = $form['data'] ?? null;
            $jsonData = is_array($rawData) ? json_encode($rawData) : $rawData;

            WpFusionForm::updateOrCreate(
                ['form_id' => $form['form_id']],
                [
                    'views' => $form['views'] ?? 0,
                    'submissions_count' => $form['submissions_count'] ?? 0,
                    'data' => $jsonData,
                ]
            );
            $count++;
        }

        return response()->json(['status' => 'ok', 'count' => $count]);
    }


public function syncFields()
{
    $response = Http::timeout(60)->get('https://www.solar-aspekt.de/fusion-api-fields.php?token=3Ho7JAeHMxL8kkjRDx83fhwq');

    if ($response->failed()) {
        Log::error('Fusion API failed', ['body' => $response->body()]);
        return response()->json(['status' => 'error']);
    }

    $fields = $response->json();
    if (!is_array($fields)) {
        Log::error('Invalid JSON from fusion-api-fields', ['body' => $response->body()]);
        return response()->json(['status' => 'error']);
    }

    $count = 0;

    foreach ($fields as $field) {
        WpFusionFormField::updateOrCreate(
            ['id' => $field['id']],
            [
                'form_id' => $field['form_id'],
                'field_name' => $field['field_name'],
                'field_label' => $field['field_label'] ?? null,
                'data' => is_array($field['data']) ? json_encode($field['data']) : $field['data'],
            ]
        );
        $count++;
    }

    return response()->json(['status' => 'ok', 'count' => $count]);
}

public function syncEntries()
{
    $response = Http::timeout(120)->get('https://www.solar-aspekt.de/fusion-api-entries.php?token=3Ho7JAeHMxL8kkjRDx83fhwq');

    if ($response->failed()) {
        Log::error('Fusion API (entries) failed', ['body' => $response->body()]);
        return response()->json(['status' => 'error', 'reason' => 'HTTP failed']);
    }

    $entries = $response->json();

    if (!is_array($entries)) {
        Log::error('Invalid JSON from fusion-api-entries', ['body' => $response->body()]);
        return response()->json(['status' => 'error', 'reason' => 'invalid json']);
    }

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

    return response()->json(['status' => 'ok', 'count' => $count]);
}
public function syncSubmissions()
{
    $response = Http::timeout(120)->get('https://www.solar-aspekt.de/fusion-api-submissions.php?token=3Ho7JAeHMxL8kkjRDx83fhwq');

    if ($response->failed() || !is_array($response->json())) {
        Log::error('Fusion API Submissions Failed', ['body' => $response->body()]);
        return response()->json(['status' => 'error', 'reason' => 'invalid or failed response']);
    }

    $submissions = $response->json();
    $count = 0;

    foreach ($submissions as $sub) {
        FusionFormSubmission::updateOrCreate(
            ['id' => $sub['id']],
            [
                'form_id' => $sub['form_id'],
                'time' => $sub['time'],
                'source_url' => $sub['source_url'],
                'post_id' => $sub['post_id'] ?? null,
                'user_id' => $sub['user_id'] ?? null,
                'user_agent' => $sub['user_agent'] ?? null,
                'ip' => $sub['ip'] ?? null,
                'is_read' => $sub['is_read'] ?? null,
                'privacy_scrub_date' => $sub['privacy_scrub_date'] ?? null,
                'on_privacy_scrub' => $sub['on_privacy_scrub'] ?? null,
                'data' => is_array($sub['data']) ? json_encode($sub['data']) : $sub['data'],
            ]
        );
        $count++;
    }

    return response()->json(['status' => 'ok', 'count' => $count]);
}

public function listForms()
{
    // Step 1: Get actual primary keys from wp_fusion_forms
    $formIds = DB::table('wp_fusion_forms')->pluck('id');

    // Step 2: Get submissions for each form
    $submissions = DB::table('wp_fusion_form_submissions')
        ->whereIn('form_id', $formIds)
        ->select('form_id', 'source_url')
        ->groupBy('form_id', 'source_url')
        ->get();

    // Step 3: Get fields
    $fields = DB::table('wp_fusion_form_fields')
        ->whereIn('form_id', $formIds)
        ->select('id', 'form_id', 'field_name', 'field_label')
        ->get();

    // Step 4: Get entries and join correctly, ordered by latest `submissions.created_at`
    $entries = DB::table('wp_fusion_form_entries')
        ->leftJoin('wp_fusion_form_submissions', 'wp_fusion_form_entries.submission_id', '=', 'wp_fusion_form_submissions.id')
        ->leftJoin('wp_fusion_forms', 'wp_fusion_form_entries.form_id', '=', 'wp_fusion_forms.id') // ✅ fixed
        ->leftJoin('wp_fusion_form_fields', 'wp_fusion_form_entries.field_id', '=', 'wp_fusion_form_fields.id')
        ->select(
            'wp_fusion_form_entries.*',
            'wp_fusion_form_submissions.source_url',
            'wp_fusion_form_submissions.created_at as submission_created_at',
            'wp_fusion_form_fields.field_name',
            'wp_fusion_form_fields.field_label'
        )
        ->orderByDesc('wp_fusion_form_submissions.created_at') // ✅ sort by latest submissions
        ->get();

    return response()->json([
        'form_ids' => $formIds,
        'submissions' => $submissions,
        'fields' => $fields,
        'entries' => $entries,
    ]);
}


    public function webhookAjax(Request $request)
    {
        $token = $request->header('X-Fusion-Token');
        if ($token !== config('services.fusion_forms.token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $entries = $request->input('entries', []);

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
        }

        return response()->json(['status' => 'ok', 'count' => count($entries)]);
    }




public function getEntriesByForm(Request $request, $form_id)
{
    $search = $request->get('search', '');
    $perPage = 10;
    $page = max(1, (int) $request->get('page', 1));

    // Get fields
    $fields = WpFusionFormField::where('form_id', $form_id)->get();

    // Get entries grouped by submission_id
    $entries = WpFusionFormEntry::where('form_id', $form_id)->get()->groupBy('submission_id');

    // Optional search
    if (!empty($search)) {
        $entries = $entries->filter(function ($group) use ($search) {
            return $group->contains(fn($entry) => stripos($entry->value, $search) !== false);
        });
    }

    $total = $entries->count();
    $paginated = $entries->forPage($page, $perPage);

    $formatted = $paginated->map(function ($group) {
        return [
            'submission_id' => $group->first()->submission_id,
            'fields' => $group->map(fn($entry) => [
                'field_id' => $entry->field_id,
                'value' => $entry->value
            ])
        ];
    })->values();

    return response()->json([
        'fields' => $fields,
        'entries' => $formatted,
        'currentPage' => $page,
        'totalPages' => ceil($total / $perPage),
    ]);
}


  public function importSingle(Request $request)
    {
        $submissionId = $request->input('submission_id');
        $note = $request->input('note');

        if (!$submissionId) {
            return response()->json(['message' => 'Missing submission_id'], 422);
        }

        // Get submission with URL (for custom logic)
        $submission = WpFusionFormSubmission::find($submissionId);
        if (!$submission) {
            return response()->json(['message' => 'Submission not found'], 404);
        }

        $sourceUrl = $submission->source_url ?? '';

        // Load all form entry values
        $entries = DB::table('wp_fusion_form_entries')
            ->join('wp_fusion_form_fields', 'wp_fusion_form_entries.field_id', '=', 'wp_fusion_form_fields.id')
            ->where('wp_fusion_form_entries.submission_id', $submissionId)
            ->select('wp_fusion_form_fields.field_name', 'wp_fusion_form_entries.value')
            ->get()
            ->pluck('value', 'field_name');

        // Extract values
        $email    = $entries['e-mail'] ?? null;
        $phone    = $entries['telefon'] ?? null;
        $name     = $entries['vorname'] ?? null;
        $lastname = $entries['name'] ?? null;
        $street   = $entries['str__nr'] ?? null;
        $postcode = $entries['plz__ort'] ?? null;
        $firma    = $entries['firma'] ?? null;

        // Validate required fields
        if (!$email && !$phone) {
            return response()->json(['message' => 'Missing email or phone'], 422);
        }

        // Prevent duplicates
        $exists = Inquiry::where('email', $email)->orWhere('phone', $phone)->exists();
        if ($exists) {
            return response()->json(['message' => 'Duplicate - already exists in inquiries'], 409);
        }

        // Build inquiry
        $inquiry = new Inquiry();
        $inquiry->source = 'Website';
        $inquiry->pre_type = 'Lead';
        $inquiry->status = 'Unpublished';
        $inquiry->periority = 'normal';
        $inquiry->contact_person = auth()->user()->name;
        $inquiry->branch_id = 1;

        $inquiry->email    = $email;
        $inquiry->phone    = $phone;
        $inquiry->name     = $name;
        $inquiry->lastname = $lastname;
        $inquiry->firma    = $firma;
        $inquiry->street   = $street;
        $inquiry->postcode = $postcode;

        // Handle note by URL or default
        if ($note) {
            $inquiry->note = $note;
        } elseif (str_contains($sourceUrl, '/jobs')) {
            $inquiry->type = 'Bewerbung';
            $inquiry->source = 'Job Page';
            $inquiry->note = "Bewerbung für: " . ($entries['job_wahlen'] ?? '-');
        } elseif (str_contains($sourceUrl, '/batteriespeicher')) {
            $inquiry->note = "Energieverbrauch: " . ($entries['energieverbrauch_'] ?? '-') . "\n" .
                            "Ich interessiere mich für: " . ($entries['ich_interessiere_mich_fuer'] ?? '-');
        } elseif (str_contains($sourceUrl, '/kontakt')) {
            $inquiry->note = collect([
                'Ich bin' => $entries['ich_bin'] ?? '-',
                'Interesse' => $entries['ich_interessiere_mich_fur'] ?? '-',
                'Dachform' => $entries['dachform'] ?? '-',
                'Stromverbrauch/Jahr' => $entries['ihr_stromverbrauch_pro_jahr'] ?? '-',
                'Größe' => $entries['groesse'] ?? '-',
                'Wechselrichtertyp' => $entries['wechselrichtertyp'] ?? '-',
                'Baujahr' => $entries['baujahr'] ?? '-',
                'Anschluss zur Wärmepumpe' => $entries['anschluss_zur_waermepumpe'] ?? '-',
                'Heizung' => $entries['derzeitige_heizung'] ?? '-',
                'Baujahr Heizung' => $entries['baujahr_heizung'] ?? '-',
                'Baujahr Haus' => $entries['baujahr_haus'] ?? '-',
                'Wohnfläche' => $entries['beheizte_wohnflaeche'] ?? '-',
                'Leistung Heizung' => $entries['leistung_der_heizung_(kwh)'] ?? '-',
                'Heizkörper vorhanden' => $entries['heizkoerper_vorhanden'] ?? '-',
                'Verbrauch Heizung/Jahr' => $entries['verbrauch_der_heizung_pro_jahr'] ?? '-',
                'Energiekosten/Jahr' => $entries['energiekosten_pro_jahr_(euro)'] ?? '-',
                'Wallboxen' => $entries['benoetigte_anzahl_wallbox'] ?? '-',
                'Ladesäulen' => $entries['benoetigte_anzahl_ladesaeule'] ?? '-',
                'Solar Carport Stellplätze' => $entries['solar_carport_anzahl_der_stellplaetze'] ?? '-',
                'E-Mobility Anschluss' => $entries['anschluss_fuer_e_mobility'] ?? '-',
                'Fußbodenheizung' => $entries['fussbodenheizung'] ?? '-',
                'Notstrom' => $entries['notstrom-funktion'] ?? '-',
            ])->map(fn($val, $key) => "$key: $val")->implode("\n");
        } else {
            $inquiry->note = "Ich habe: " . ($entries['ich_habe'] ?? '-') . "\n" .
                            "Stromverbrauch pro Jahr: " . ($entries['ihr_stromverbrauch_pro_jahr'] ?? '-') . "\n" .
                            "Heizart: " . ($entries['heizart'] ?? '-') . "\n" .
                            "Energieverbrauch: " . ($entries['energieverbrauch'] ?? '-');
        }

        $inquiry->save();

        return response()->json(['message' => 'Inquiry created successfully'], 201);
    }


    public function importAll(Request $request)
{
    $formId = $request->input('form_id');
    $submissions = WpFusionFormSubmission::where('form_id', $formId)->get();
    $imported = 0;

    foreach ($submissions as $submission) {
        $submissionId = $submission->id;
        $sourceUrl = $submission->source_url ?? '';

        $entries = DB::table('wp_fusion_form_entries')
            ->join('wp_fusion_form_fields', 'wp_fusion_form_entries.field_id', '=', 'wp_fusion_form_fields.id')
            ->where('wp_fusion_form_entries.submission_id', $submissionId)
            ->select('wp_fusion_form_fields.field_name', 'wp_fusion_form_entries.value')
            ->get()
            ->pluck('value', 'field_name');

        $email    = $entries['e-mail'] ?? null;
        $phone    = $entries['telefon'] ?? null;

        if (!$email && !$phone) continue;

        $exists = Inquiry::where('email', $email)->orWhere('phone', $phone)->exists();
        if ($exists) continue;

        $inquiry = new Inquiry();
        $inquiry->source = 'Website';
        $inquiry->pre_type = 'Lead';
        $inquiry->status = 'Unpublished';
        $inquiry->periority = 'normal';
        $inquiry->contact_person = auth()->user()->name;
        $inquiry->branch_id = 1;

        // Common fields
        $inquiry->email    = $email;
        $inquiry->phone    = $phone;
        $inquiry->name     = $entries['vorname'] ?? null;
        $inquiry->lastname = $entries['name'] ?? null;
        $inquiry->firma    = $entries['firma'] ?? null;
        $inquiry->street   = $entries['str__nr'] ?? null;
        $inquiry->postcode = $entries['plz__ort'] ?? null;

        // Custom logic by source_url
        if (str_contains($sourceUrl, '/jobs')) {
            $inquiry->type = 'Bewerbung';
            $inquiry->source = 'Job Page';
            $inquiry->note = "Bewerbung für: " . ($entries['job_wahlen'] ?? '-');
        } elseif (str_contains($sourceUrl, '/batteriespeicher')) {
            $inquiry->note = "Energieverbrauch: " . ($entries['energieverbrauch_'] ?? '-') . "\n" .
                             "Ich interessiere mich für: " . ($entries['ich_interessiere_mich_fuer'] ?? '-');
        } elseif (str_contains($sourceUrl, '/kontakt')) {
            $inquiry->note = collect([
                'Ich bin' => $entries['ich_bin'] ?? '-',
                'Interesse' => $entries['ich_interessiere_mich_fur'] ?? '-',
                'Dachform' => $entries['dachform'] ?? '-',
                'Stromverbrauch/Jahr' => $entries['ihr_stromverbrauch_pro_jahr'] ?? '-',
                'Größe' => $entries['groesse'] ?? '-',
                'Wechselrichtertyp' => $entries['wechselrichtertyp'] ?? '-',
                'Baujahr' => $entries['baujahr'] ?? '-',
                'Anschluss zur Wärmepumpe' => $entries['anschluss_zur_waermepumpe'] ?? '-',
                'Heizung' => $entries['derzeitige_heizung'] ?? '-',
                'Baujahr Heizung' => $entries['baujahr_heizung'] ?? '-',
                'Baujahr Haus' => $entries['baujahr_haus'] ?? '-',
                'Wohnfläche' => $entries['beheizte_wohnflaeche'] ?? '-',
                'Leistung Heizung' => $entries['leistung_der_heizung_(kwh)'] ?? '-',
                'Heizkörper vorhanden' => $entries['heizkoerper_vorhanden'] ?? '-',
                'Verbrauch Heizung/Jahr' => $entries['verbrauch_der_heizung_pro_jahr'] ?? '-',
                'Energiekosten/Jahr' => $entries['energiekosten_pro_jahr_(euro)'] ?? '-',
                'Wallboxen' => $entries['benoetigte_anzahl_wallbox'] ?? '-',
                'Ladesäulen' => $entries['benoetigte_anzahl_ladesaeule'] ?? '-',
                'Solar Carport Stellplätze' => $entries['solar_carport_anzahl_der_stellplaetze'] ?? '-',
                'E-Mobility Anschluss' => $entries['anschluss_fuer_e_mobility'] ?? '-',
                'Fußbodenheizung' => $entries['fussbodenheizung'] ?? '-',
                'Notstrom' => $entries['notstrom-funktion'] ?? '-',
            ])->map(fn($val, $key) => "$key: $val")->implode("\n");
        } else {
            // Default note format
            $inquiry->note = "Ich habe: " . ($entries['ich_habe'] ?? '-') . "\n" .
                             "Stromverbrauch pro Jahr: " . ($entries['ihr_stromverbrauch_pro_jahr'] ?? '-') . "\n" .
                             "Heizart: " . ($entries['heizart'] ?? '-') . "\n" .
                             "Energieverbrauch: " . ($entries['energieverbrauch'] ?? '-');
        }

        $inquiry->save();
        $imported++;
    }

    return response()->json(['imported' => $imported]);
}

}

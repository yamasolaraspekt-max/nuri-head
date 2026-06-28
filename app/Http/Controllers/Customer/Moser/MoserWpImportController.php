<?php

namespace App\Http\Controllers\Customer\Moser;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;

class MoserWpImportController extends Controller
{
    protected array $csvMapping = [
        'Anrede' => 'title',
        'Name'   => 'full_name',
        'Straße' => 'street',
        'PLZ'    => 'postcode',
        'Ort'    => 'city',
    ];

    public function index()
    {
        return view('admin.leads.moser_wp_import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $delimiter = $this->detectDelimiter($path);
        
        $handle = fopen($path, 'r');
        if (!$handle) return response()->json(['message' => 'File read error'], 422);

        $headers = fgetcsv($handle, 0, $delimiter);
        $rows = [];
        $i = 0;
        $utf8ize = fn($d) => mb_convert_encoding($d, 'UTF-8', 'ISO-8859-1,Windows-1252');

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false && $i < 20) {
            $rows[] = array_map($utf8ize, $data); 
            $i++;
        }
        fclose($handle);

        $headers = array_map(fn($h) => trim($utf8ize($h)), $headers);

        return response()->json([
            'headers'   => $headers,
            'rows'      => $rows,
            'delimiter' => $delimiter
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'    => ['required', 'file', 'mimes:csv,txt'],
            'col_map' => ['required', 'array'], 
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $delimiter = $this->detectDelimiter($path);
        $map = $request->input('col_map'); 

        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, $delimiter); // Skip header

        $stats = ['created' => 0, 'updated' => 0];

        $utf8ize = fn($d) => mb_convert_encoding($d, 'UTF-8', 'ISO-8859-1,Windows-1252');

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                
                // 1. Extract Data
                $data = [];
                foreach ($map as $index => $field) {
                    if ($field === 'ignore' || !isset($row[$index])) continue;
                    $data[$field] = trim($utf8ize($row[$index]));
                }

                if (empty($data['street']) && empty($data['full_name'])) continue;

                // 2. Name Splitting
                $title    = $data['title'] ?? null;
                $fullName = $data['full_name'] ?? '';
                $lastname = '';
                $name     = '';

                if (!empty($fullName)) {
                    $parts = explode(' ', $fullName);
                    if (count($parts) > 1) {
                        $lastname = array_pop($parts);
                        $name     = implode(' ', $parts);
                    } else {
                        $lastname = $fullName;
                    }
                }

                // 3. CHECK IF LEAD EXISTS
                $lead = NewLeads::where('lastname', $lastname)
                    ->where(function($q) use ($data) {
                        if (!empty($data['street']))   $q->where('street', $data['street']);
                        if (!empty($data['postcode'])) $q->orWhere('postcode', $data['postcode']);
                    })
                    ->first();

                $alt = null;

                if ($lead) {
                    // --- EXISTING LEAD Found ---
                    $stats['updated']++;

                    // Find an existing Object (House) for this lead
                    $alt = LeadAlternativeAdd::where('lead_id', $lead->id)->orderBy('main', 'desc')->first();

                    // Fallback: If lead exists but has no object, create one
                    if (!$alt) {
                        $alt = LeadAlternativeAdd::create([
                            'lead_id'      => $lead->id,
                            'object_name'  => 'Privatehaus',
                            'main'         => 1,
                            'address_no'   => 1,
                            'street'       => $data['street'] ?? null,
                            'postcode'     => $data['postcode'] ?? null,
                            'city'         => $data['city'] ?? null,
                            'full_address' => implode(' ', array_filter([$data['street']??'', $data['postcode']??'', $data['city']??''])),
                            'status'       => 'Published',
                            'is_owner'     => 'Ja',
                            'is_living_inside' => 'Ja',
                        ]);
                    }

                } else {
                    // --- NEW LEAD ---
                    $stats['created']++;

                    $lead = NewLeads::create([
                        'customer_type' => 'Privatkunde',
                        'title'         => $title,
                        'name'          => $name,
                        'lastname'      => $lastname,
                        'street'        => $data['street'] ?? null,
                        'postcode'      => $data['postcode'] ?? null,
                        'city'          => $data['city'] ?? null,
                        'full_address'  => implode(' ', array_filter([$data['street']??'', $data['postcode']??'', $data['city']??''])),
                        'source'        => 'Moser',
                        'status'        => 'Published',
                        'contact_person'=> 18, // New leads get assigned to 18
                    ]);

                    $alt = LeadAlternativeAdd::create([
                        'lead_id'      => $lead->id,
                        'object_name'  => 'Privatehaus',
                        'main'         => 1,
                        'address_no'   => 1,
                        'street'       => $data['street'] ?? null,
                        'postcode'     => $data['postcode'] ?? null,
                        'city'         => $data['city'] ?? null,
                        'full_address' => implode(' ', array_filter([$data['street']??'', $data['postcode']??'', $data['city']??''])),
                        'status'       => 'Published',
                        'is_owner'     => 'Ja',
                        'is_living_inside' => 'Ja',
                    ]);
                }

                // 4. CREATE PRODUCT (Happens for BOTH New and Existing)
                $history = [
                    [
                        "stage"       => "archive",
                        "changed_by"  => "7",
                        "changed_at"  => Carbon::now()->format('Y-m-d H:i:s'),
                        "description" => "Die Kunden stammen alle aus Mozer."
                    ]
                ];

                LeadProductList::create([
                    'customer_id'    => $lead->id,
                    'alternative_id' => $alt->id,
                    'product_id'     => 16,
                    'department_id'  => 2,
                    'employee_id'    => 9,  // Innendienst
                    'field_employee' => 12, // Außendienst
                    'status'         => 'archive',
                    'stage'          => 'archive',
                    'service'        => 'complete',
                    'work_status'    => 'playing',
                    'interest'       => 'intent',
                    'stage_history'  => json_encode($history),
                ]);
            }
            
            DB::commit();
            fclose($handle);
            
            return response()->json([
                'message' => 'Import erfolgreich.', 
                'stats' => $stats // Returns created vs updated counts
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) fclose($handle);
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function detectDelimiter($path)
    {
        $handle = fopen($path, 'r');
        $line = fgets($handle);
        fclose($handle);
        if(!$line) return ';';
        return (substr_count($line, ';') >= substr_count($line, ',')) ? ';' : ',';
    }
}
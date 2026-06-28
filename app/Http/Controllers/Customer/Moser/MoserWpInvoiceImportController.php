<?php

namespace App\Http\Controllers\Customer\Moser;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;

class MoserWpInvoiceImportController extends Controller
{
    /**
     * Map CSV Headers => Database Columns / Variables
     */
    protected array $csvMapping = [
        'Anrede'     => 'title',
        'Name'       => 'full_name',
        'Straße'     => 'street',
        'PLZ'        => 'postcode',
        'Ort'        => 'city',
        'Netto'      => 'net_amount',
        'Brutto'     => 'gross_amount',
        'Steuer'     => 'tax_amount',
        'Belegdatum' => 'receipt_date',
        'Beleg'      => 'receipt_reference',
        'Kurztext'   => 'note',
    ];

    public function index()
    {
        return view('admin.leads.moser_wp_invoice_import');
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
        if (!$handle) return response()->json(['message' => 'File error'], 422);

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

        // Robust Money Cleaner Helper
        // Converts "35.595,13" -> 35595.13
        $formatMoney = function($val) {
            if (empty($val)) return 0;
            
            // 1. Remove everything except numbers, dots, commas, and minus
            $val = preg_replace('/[^\d,.-]/', '', $val);
            
            // 2. Handle German Format (e.g. 35.595,13)
            // If comma exists, it is the decimal separator
            if (strpos($val, ',') !== false) {
                $val = str_replace('.', '', $val); // Remove thousands separator (.)
                $val = str_replace(',', '.', $val); // Replace decimal separator (,) with (.)
            }
            
            return (float)$val;
        };

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

                // 3. Find or Create Lead
                $lead = NewLeads::where('lastname', $lastname)
                    ->where(function($q) use ($data) {
                        if (!empty($data['street']))   $q->where('street', $data['street']);
                        if (!empty($data['postcode'])) $q->orWhere('postcode', $data['postcode']);
                    })
                    ->first();

                $alt = null;

                if ($lead) {
                    // --- EXISTING LEAD ---
                    $stats['updated']++;
                    
                    $alt = LeadAlternativeAdd::where('lead_id', $lead->id)->orderBy('main', 'desc')->first();
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
                            'note'         => $data['note'] ?? null,
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
                        'source'        => 'Moser WP', 
                        'status'        => 'Published',
                        'contact_person'=> 18,
                        'info'          => $data['note'] ?? null,
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
                        'note'         => $data['note'] ?? null,
                    ]);
                }

                // 4. Clean Money Values
                $netPrice   = $formatMoney($data['net_amount'] ?? '0');
                $grossPrice = $formatMoney($data['gross_amount'] ?? '0');
                $taxPrice   = $formatMoney($data['tax_amount'] ?? '0');

                // 5. Build Histories
                $stageHistory = [
                    [
                        "stage"       => "archive",
                        "changed_by"  => "7",
                        "changed_at"  => Carbon::now()->format('Y-m-d H:i:s'),
                        "description" => "Importiert aus Schlussrechnung (Moser)."
                    ]
                ];

                $priceHistory = [
                    [
                        "changed_at"     => Carbon::now()->format('Y-m-d H:i:s'),
                        "changed_by"     => "7",
                        "old_price"      => "0",
                        "new_price"      => (string)$netPrice,
                        "customer_id"    => $lead->id,
                        "alternative_id" => $alt->id,
                        "product_id"     => 16
                    ]
                ];

                // 6. Create Product
                LeadProductList::create([
                    'customer_id'    => $lead->id,
                    'alternative_id' => $alt->id,
                    'product_id'     => 16,
                    'department_id'  => 2,
                    'employee_id'    => 9,
                    'field_employee' => 12,
                    'status'         => 'archive',
                    'stage'          => 'archive',
                    'service'        => 'complete',
                    'work_status'    => 'playing',
                    'interest'       => 'intent',
                    
                    // JSON Encode to ensure string format if model doesn't cast
                    'stage_history'  => json_encode($stageHistory),
                    'price_history'  => json_encode($priceHistory),
                    
                    'price'             => $netPrice,
                    'net_amount'        => $netPrice,
                    'gross_amount'      => $grossPrice,
                    'tax_amount'        => $taxPrice,
                    'receipt_date'      => !empty($data['receipt_date']) ? Carbon::parse($data['receipt_date'])->format('Y-m-d') : null,
                    'receipt_reference' => $data['receipt_reference'] ?? null,
                ]);
            }
            
            DB::commit();
            fclose($handle);
            
            return response()->json([
                'message' => 'Import erfolgreich.', 
                'stats' => $stats
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
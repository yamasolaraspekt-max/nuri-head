<?php

namespace App\Http\Controllers;

use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\Distributor;
use App\Models\DistributorDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadImportController extends Controller
{
    /**
     * Map CSV Headers (German) => Database Columns (English)
     */
    protected array $headerToDb = [
        'Typ'               => 'new_leads.customer_type',
        'Konto'             => 'new_leads.moser_id',
        'Matchcode'         => 'new_leads.firma',

        // Raw mapping - we will process these in the loop
        'Name1'             => 'new_leads.title',
        'Name2'             => 'new_leads.lastname', // Temporarily holds Full Name or Company
        'Name3'             => 'new_leads.name',     // Temporarily holds Contact Person

        'Straße'            => 'new_leads.street',
        'PLZ'               => 'new_leads.postcode',
        'Ort'               => 'new_leads.city',
        'Telefon'           => 'new_leads.telephone',
        'Mobiltelefon'      => 'new_leads.phone',
        'Kommunikation'     => 'new_leads.email',
        'Internetadresse'   => 'new_leads.info',
        'Bankname'          => 'new_leads.bank_name',
        'IBAN'              => 'new_leads.iban',
        'BIC'               => 'new_leads.bic',
        'Bankkontoinhaber'  => 'new_leads.bank_holder',
        'Zahlungsart'       => 'new_leads.payment_type',
        'Zahlungsmittel'    => 'new_leads.payment_means',
    ];

    protected array $typeCodeMap = [
        '1'  => 'Kunde',
        '2'  => 'Lieferant',
        '5'  => 'Privatkunde',
        '18' => 'Subunternehmer',
    ];

    public function index()
    {
        $employees = \App\Models\Employee::orderBy('name')->get();
        return view('admin.leads.import', ['employees' => $employees]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file      = $request->file('file');
        $path      = $file->getRealPath();
        $delimiter = $this->detectDelimiter($path);

        $handle = fopen($path, 'r');
        if (!$handle) return response()->json(['message' => 'Error reading file.'], 422);

        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            fclose($handle);
            return response()->json(['message' => 'No headers found.'], 422);
        }
        $headers = array_map(fn ($h) => $this->utf8ize(trim($h)), $headers);

        $rows  = [];
        $limit = 20;
        $count = 0;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false && $count < $limit) {
            $rows[] = array_map(fn ($v) => $this->utf8ize($v), $data);
            $count++;
        }
        fclose($handle);

        $mapping = [];
        foreach ($headers as $header) {
            $key = trim($header);
            $mapping[] = $this->headerToDb[$key] ?? null;
        }

        return response()->json([
            'headers'   => $headers,
            'rows'      => $rows,
            'mapping'   => $mapping,
            'delimiter' => $delimiter,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'file'   => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'config' => ['required', 'string'],
        ]);

        $configArr = json_decode($request->input('config'), true);
        $globalContactPersonId = $request->input('contact_person_id');
        $checkDuplicates       = $request->boolean('check_duplicates', true);

        $indexConfig = [];
        foreach ($configArr as $colConfig) {
            if (isset($colConfig['index'])) {
                $indexConfig[(int)$colConfig['index']] = $colConfig;
            }
        }

        $file      = $request->file('file');
        $path      = $file->getRealPath();
        $delimiter = $this->detectDelimiter($path);
        $handle    = fopen($path, 'r');

        if (!$handle) return response()->json(['message' => 'Error opening file.'], 422);

        fgetcsv($handle, 0, $delimiter); // Skip header

        $stats = ['leads' => 0, 'objects' => 0, 'distributors' => 0, 'duplicates' => 0];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = array_map(fn ($v) => $this->utf8ize($v), $row);
                
                $mappedData = [];
                $rawTyp     = '';

                // 1. Basic Mapping
                foreach ($row as $i => $value) {
                    if (!isset($indexConfig[$i])) continue;
                    $cfg = $indexConfig[$i];

                    if (($cfg['ignore'] ?? false) || empty($cfg['column'])) continue;

                    $val = trim($value);

                    if ($cfg['column'] === 'customer_type') $rawTyp = $val;

                    // Manual replacements from frontend
                    if (!empty($cfg['mapping']) && is_array($cfg['mapping']) && array_key_exists($val, $cfg['mapping'])) {
                        $val = $cfg['mapping'][$val];
                    }

                    // Auto-convert numeric Typ
                    if ($cfg['column'] === 'customer_type') {
                        if (isset($this->typeCodeMap[$val])) {
                            $val = $this->typeCodeMap[$val];
                        }
                    }

                    $mappedData[$cfg['column']] = $val;
                }

                if (empty($mappedData)) continue;

                // =========================================================
                // 2. NAME NORMALIZATION / SPLITTING LOGIC
                // =========================================================
                
                // Grab raw values before we overwrite them
                $rawTitle  = $mappedData['title'] ?? null;    // Name1
                $rawName2  = $mappedData['lastname'] ?? null; // Name2 (Full Name or Company)
                $rawName3  = $mappedData['name'] ?? null;     // Name3 (Contact Person or Info)
                $rawFirma  = $mappedData['firma'] ?? null;    // Matchcode

                // Reset destination fields
                $mappedData['title']    = null;
                $mappedData['lastname'] = null;
                $mappedData['name']     = null;
                // Leave firma (Matchcode) but we might overwrite it if it's empty

                // Detect: Is it a Person or a Company?
                $isPerson = false;
                $salutations = ['Herr', 'Herrn', 'Frau', 'Fr.', 'Mr', 'Mrs', 'Dr.', 'Prof.'];
                
                // If Name1 is a salutation, it's definitely a person
                if (!empty($rawTitle) && in_array(trim($rawTitle), $salutations)) {
                    $isPerson = true;
                }
                // If Type is explicitly Privatkunde, treat as Person
                if (($mappedData['customer_type'] ?? '') === 'Privatkunde') {
                    $isPerson = true;
                }

                if ($isPerson) {
                    // --- PERSON ---
                    $mappedData['title'] = $rawTitle;
                    
                    // Name2 contains "Firstname Lastname" (e.g., "Harald Schäfer")
                    if (!empty($rawName2)) {
                        $parts = explode(' ', trim($rawName2));
                        if (count($parts) > 1) {
                            // Last part is Lastname, rest is Firstname
                            $mappedData['lastname'] = array_pop($parts);
                            $mappedData['name']     = implode(' ', $parts);
                        } else {
                            // Only one word? Treat as Lastname
                            $mappedData['lastname'] = $rawName2;
                        }
                    }
                } else {
                    // --- COMPANY / LIEFERANT ---
                    // Name2 contains Company Name (e.g., "Aeroclub...")
                    if (!empty($rawName2)) {
                        // Use Name2 as Firma if Matchcode was empty
                        if (empty($rawFirma)) {
                            $mappedData['firma'] = $rawName2;
                        } else {
                            // Optional: Ensure Name2 is saved somewhere if Matchcode exists?
                            // For now, if Matchcode is present, we assume it's the correct shortname.
                            if (empty($mappedData['firma'])) $mappedData['firma'] = $rawName2;
                        }
                    }

                    // Name3 contains Contact Person (e.g., "Herrn Friedhelm Schmidt")
                    if (!empty($rawName3)) {
                        $contact = trim($rawName3);
                        $parts   = explode(' ', $contact);
                        $first   = $parts[0] ?? '';

                        // Extract Title if present
                        if (in_array($first, $salutations)) {
                            $mappedData['title'] = array_shift($parts);
                        }

                        // Split Name
                        if (count($parts) > 1) {
                            $mappedData['lastname'] = array_pop($parts);
                            $mappedData['name']     = implode(' ', $parts);
                        } elseif (count($parts) === 1) {
                            $mappedData['lastname'] = $parts[0];
                        }
                    }
                }

                // Force defaults
                $mappedData['source'] = 'Moser';

                // =========================================================
                // 3. CREATE LEAD OR DISTRIBUTOR
                // =========================================================

                $isDistributor = ($rawTyp == '2' || ($mappedData['customer_type'] ?? '') === 'Lieferant');

                if ($isDistributor) {
                    $this->createDistributor($mappedData);
                    $stats['distributors']++;
                } else {
                    if ($this->processLead($mappedData, $globalContactPersonId, $checkDuplicates)) {
                        $stats['leads']++;
                        $stats['objects']++;
                    } else {
                        $stats['duplicates']++;
                    }
                }
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'message'              => 'Import erfolgreich abgeschlossen.',
                'created_leads'        => $stats['leads'],
                'created_objects'      => $stats['objects'],
                'created_distributors' => $stats['distributors'],
                'duplicates_matched'   => $stats['duplicates'],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            if (is_resource($handle)) fclose($handle);
            Log::error('LeadImport Error: ' . $e->getMessage());
            return response()->json(['message' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    protected function processLead(array $data, $contactPersonId, $checkDuplicates): bool
    {
        if (empty($data['customer_type'])) $data['customer_type'] = 'Privatkunde';
        if (empty($data['status']))        $data['status'] = 'Imported';
        if (!empty($contactPersonId))      $data['contact_person'] = $contactPersonId;

        if ($checkDuplicates && $this->findDuplicateLead($data)) {
            return false;
        }

        $lead = NewLeads::create($data);

        $objData = [
            'lead_id'      => $lead->id,
            'object_name'  => 'Privatehaus',
            'main'         => 1,
            'address_no'   => 1,
            'full_address' => $data['full_address'] ?? null,
            'street'       => $data['street'] ?? null,
            'postcode'     => $data['postcode'] ?? null,
            'city'         => $data['city'] ?? null,
            'lat'          => $data['latitude'] ?? null,
            'lon'          => $data['longitude'] ?? null,
        ];
        LeadAlternativeAdd::create($objData);

        return true;
    }

    protected function createDistributor(array $data)
    {
        // 1. Name: Use Firma, else fallback to Contact Person name
        $name = $data['firma'] ?? null;
        if (!$name) {
            $name = trim(($data['lastname'] ?? '') . ' ' . ($data['name'] ?? ''));
        }
        if (!$name) $name = 'Unbekannter Lieferant';

        $addressParts = array_filter([$data['street'] ?? null, $data['postcode'] ?? null, $data['city'] ?? null]);
        $fullAddress = implode(', ', $addressParts);

        $distributor = Distributor::firstOrCreate(
            ['name' => $name],
            ['address' => $fullAddress, 'status'  => 'Published']
        );

        // 2. Contact Person
        $contactName = trim(($data['title'] ?? '') . ' ' . ($data['name'] ?? '') . ' ' . ($data['lastname'] ?? ''));
        if (empty($contactName)) $contactName = 'Zentrale';

        DistributorDepartment::create([
            'd_id'         => $distributor->id,
            'd_department' => 'Verkauf/Zentrale',
            'name'         => $contactName,
            'email'        => $data['email'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'office'       => $data['telephone'] ?? null,
            'status'       => 'Published',
        ]);
    }

    protected function findDuplicateLead(array $data): ?NewLeads
    {
        if (!empty($data['email'])) {
            $existing = NewLeads::where('email', $data['email'])->first();
            if ($existing) return $existing;
        }

        $lastname = $data['lastname'] ?? null;
        if (!$lastname) return null;

        $query = NewLeads::where('lastname', $lastname);
        if (!empty($data['street']) || !empty($data['postcode'])) {
            $query->where(function($q) use ($data) {
                if (!empty($data['street']))   $q->where('street', $data['street']);
                if (!empty($data['postcode'])) $q->orWhere('postcode', $data['postcode']);
            });
            return $query->first();
        }
        return null;
    }

    protected function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');
        $line   = fgets($handle);
        fclose($handle);
        if (!$line) return ';';
        return substr_count($line, ';') >= substr_count($line, ',') ? ';' : ',';
    }

    protected function utf8ize($value)
    {
        if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
            return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1,Windows-1252');
        }
        return $value;
    }
}
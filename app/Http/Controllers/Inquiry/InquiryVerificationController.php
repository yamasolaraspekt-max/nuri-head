<?php

namespace App\Http\Controllers\Inquiry;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\BrandDepartment;
use App\Models\Distributor;
use App\Models\DistributorDepartment;
use App\Models\Employee;
use App\Models\Inquiry;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\NewLeads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InquiryVerificationController extends Controller
{
    public function status(Request $request, Inquiry $inquiry): JsonResponse
    {
        $inquiry->load('products');

        $checks   = $this->buildChecklist($inquiry);
        $existing = $this->findExistingLead($inquiry);

        return response()->json([
            'ok'      => $checks['ok'],
            'checks'  => $checks['checks'],
            'missing' => $checks['missing'],
            'existing_lead' => $existing ? [
                'lead_id'      => $existing->id,
                'name'         => $existing->name,
                'lastname'     => $existing->lastname,
                'street'       => $existing->street,
                'postcode'     => $existing->postcode,
                'city'         => $existing->city,
                'profile_url'  => $this->leadProfileUrl($existing),
            ] : null,
        ]);
    }

    public function confirm(Request $request, Inquiry $inquiry): JsonResponse
    {
        $inquiry->load('products');

        $checks = $this->buildChecklist($inquiry);
        if (!$checks['ok']) {
            return response()->json([
                'errors' => ['Verifizierung' => $checks['missing']],
            ], 422);
        }

        return DB::transaction(function () use ($request, $inquiry) {
                $preType = Str::lower(trim((string) ($inquiry->pre_type ?? '')));
                $existingLead = $this->findExistingLead($inquiry);

                // If Kunde/Lead already exists -> verify inquiry and redirect to existing lead profile
                if ($existingLead && in_array($preType, ['kunde', 'lead'], true)) {
                    $this->markVerified($inquiry);

                    return response()->json([
                        'success'      => true,
                        'exists'       => true,
                        'lead_id'      => $existingLead->id,
                        'redirect_url' => $this->leadProfileUrl($existingLead),
                    ]);
                }

                // ✅ ALWAYS create lead for Kunde/Lead (even when inquiry->type is in inquiry_types)
                if (in_array($preType, ['kunde', 'lead'], true)) {
                    $lead = $this->lead($inquiry);

                    return response()->json([
                        'success'      => true,
                        'exists'       => false,
                        'lead_id'      => $lead->id,
                        'redirect_url' => $this->leadProfileUrl($lead),
                    ]);
                }

                // ---- everything else stays as before ----
                $types = DB::table('inquiry_types')->pluck('id')->toArray();

                $type = DB::table('inquiry_types')
                    ->join('inquiries as inq', 'inq.type', '=', 'inquiry_types.id')
                    ->where('inq.id', $inquiry->id)
                    ->value('inquiry_types.type') ?: 'other';

                if (!in_array($inquiry->type, $types, true)) {
                    $resp = $this->handleSpecialTypes($inquiry, $type, $request);
                    return response()->json(array_merge(['success' => true], $resp));
                }

                if (($request->option ?? null) === 'Lieferant') {
                    $dis = $this->distributor($inquiry);
                    return response()->json([
                        'success'      => true,
                        'redirect_url' => url('distributors') . '?name=' . urlencode($dis->name),
                    ]);
                }

                $brand = $this->brand($inquiry, $type);
                return response()->json([
                    'success'      => true,
                    'redirect_url' => url('brand') . '?search=' . urlencode($brand->name),
                ]);
            });

    }

    // ---------------------------------------------------------
    // Checklist
    // ---------------------------------------------------------
    private function buildChecklist(Inquiry $inquiry): array
{
    $checks  = [];
    $missing = [];

    // ---- CONTACT / PERSON ----
    $preTypeOk = filled($inquiry->pre_type);
    $checks[]  = $this->check(
        'pre_type',
        'Art des Kontakts (pre_type) gewählt',
        $preTypeOk,
        ['selector' => 'select[name="pre_type"]', 'type' => 'select2']
    );
    if (!$preTypeOk) $missing[] = 'Art des Kontakts (pre_type) fehlt.';

    $nameOk = filled($inquiry->name);
    $checks[] = $this->check(
        'name',
        'Vorname vorhanden',
        $nameOk,
        ['selector' => 'input[name="name"]', 'type' => 'focus']
    );
    if (!$nameOk) $missing[] = 'Vorname fehlt.';

    $lastnameOk = filled($inquiry->lastname);
    $checks[] = $this->check(
        'lastname',
        'Nachname vorhanden',
        $lastnameOk,
        ['selector' => 'input[name="lastname"]', 'type' => 'focus']
    );
    if (!$lastnameOk) $missing[] = 'Nachname fehlt.';

    // Optional (recommended): Address check -> gives edit button to jump to address field
    $addressOk = filled($inquiry->full_address) || filled($inquiry->street);
    $checks[] = $this->check(
        'address',
        'Adresse vorhanden',
        $addressOk,
        ['selector' => '#full_address', 'type' => 'focus']
    );
    if (!$addressOk) $missing[] = 'Adresse fehlt.';

    $contactOk = filled($inquiry->phone) || filled($inquiry->telephone) || filled($inquiry->email);
    $checks[] = $this->check(
        'contact',
        'Kontakt vorhanden (Mobil/Festnetz/E-Mail)',
        $contactOk,
        // jump to phone input (because missing means all 3 empty)
        ['selector' => 'input[name="phone"]', 'type' => 'focus']
    );
    if (!$contactOk) $missing[] = 'Kontakt fehlt (Mobil oder Festnetz oder E-Mail).';

    // ---- PRODUCTS ----
    $hasProducts = $inquiry->products && $inquiry->products->isNotEmpty();
    $checks[] = $this->check(
        'products',
        'Mindestens 1 Produktzeile hinzugefügt',
        $hasProducts,
        // when missing: scroll to products table / add button
        ['selector' => '#addRow', 'type' => 'click']
    );
    if (!$hasProducts) $missing[] = 'Produkte fehlen.';

    $productsDetailsOk = true;

    if ($hasProducts) {
        foreach ($inquiry->products->values() as $i => $p) {
            $row = $i + 1;

            // Product (NO EDIT; provide create_url instead)
            $prodOk = !empty($p->product_id);
            $checks[] = $this->check(
                "row.$row.product",
                "Produkt (Zeile {$row})",
                $prodOk,
                null,
                // adjust if your product create URL differs
                url('article_groups/create')
            );
            if (!$prodOk) {
                $missing[] = "Zeile {$row}: Produkt fehlt.";
                $productsDetailsOk = false;
            }

            // Service (EDIT -> select2)
            $srvOk = !empty($p->service_id);
            $checks[] = $this->check(
                "row.$row.service",
                "Dienstleistung (Zeile {$row})",
                $srvOk,
                ['selector' => ".service-select[data-index=\"{$row}\"]", 'type' => 'select2']
            );
            if (!$srvOk) {
                $missing[] = "Zeile {$row}: Dienstleistung fehlt.";
                $productsDetailsOk = false;
            }

            // Department (EDIT -> select2)
            $depOk = !empty($p->department_id);
            $checks[] = $this->check(
                "row.$row.department",
                "Abteilung (Zeile {$row})",
                $depOk,
                ['selector' => ".department-select[data-index=\"{$row}\"]", 'type' => 'select2']
            );
            if (!$depOk) {
                $missing[] = "Zeile {$row}: Abteilung fehlt.";
                $productsDetailsOk = false;
            }

            // Internal employee (EDIT -> select2)
            $empOk = !empty($p->employee_id);
            $checks[] = $this->check(
                "row.$row.employee",
                "Innendienst (Zeile {$row})",
                $empOk,
                ['selector' => ".employee-select[data-index=\"{$row}\"]", 'type' => 'select2']
            );
            if (!$empOk) {
                $missing[] = "Zeile {$row}: Innendienst fehlt.";
                $productsDetailsOk = false;
            }

            // Optional: Appointment (if you want it required, include in ok calc below)
            // $dtOk = !empty($p->appointment_date);
            // $checks[] = $this->check(
            //     "row.$row.termin",
            //     "Termin (Zeile {$row})",
            //     $dtOk,
            //     ['selector' => ".termin-input[data-index=\"{$row}\"]", 'type' => 'focus']
            // );
            // if (!$dtOk) { $missing[] = "Zeile {$row}: Termin fehlt."; $productsDetailsOk = false; }
        }
    } else {
        $productsDetailsOk = false;
    }

    // Keep your summary check (optional, but useful for progress)
    $checks[] = $this->check(
        'products_details',
        'Produkte vollständig (Produkt/Dienstleistung/Abteilung/Innendienst)',
        $productsDetailsOk,
        // if summary fails, point to table area
        ['selector' => '#inquiryProductTable', 'type' => 'focus']
    );

    // IMPORTANT: include addressOk if you added it
    $ok = $preTypeOk && $nameOk && $lastnameOk && $addressOk && $contactOk && $hasProducts && $productsDetailsOk;

    return [
        'ok'      => $ok,
        'checks'  => $checks,
        'missing' => array_values(array_unique($missing)),
    ];
}


    private function check(string $key, string $label, bool $ok, ?array $edit = null, ?string $createUrl = null): array
    {
        $row = ['key' => $key, 'label' => $label, 'ok' => $ok];

        if (!$ok && $edit && !empty($edit['selector'])) {
            $row['edit'] = $edit; // e.g. ['selector'=>'select[name="pre_type"]','type'=>'select2']
        }

        // Only shown when missing and there is no edit (product exception)
        if (!$ok && $createUrl) {
            $row['create_url'] = $createUrl;
        }

        return $row;
    }

    // ---------------------------------------------------------
    // Existing Lead detection
    // ---------------------------------------------------------
    private function findExistingLead(Inquiry $inquiry): ?NewLeads
    {
        $street   = trim(Str::lower((string) ($inquiry->street ?? '')));
        $city     = trim(Str::lower((string) ($inquiry->city ?? '')));
        $postcode = trim((string) ($inquiry->postcode ?? ''));
        $name     = trim(Str::lower((string) ($inquiry->name ?? '')));
        $lastname = trim(Str::lower((string) ($inquiry->lastname ?? '')));

        if ($name === '' || $lastname === '' || $street === '') {
            return null;
        }

        return NewLeads::query()
            ->whereRaw('LOWER(name) = ?', [$name])
            ->whereRaw('LOWER(lastname) = ?', [$lastname])
            ->whereRaw('LOWER(street) = ?', [$street])
            ->when($city !== '', fn ($q) => $q->whereRaw('LOWER(city) = ?', [$city]))
            ->when($postcode !== '', fn ($q) => $q->where('postcode', $postcode))
            ->first();
    }

    private function leadProfileUrl(NewLeads $lead): string
    {
        $addressNo = LeadAlternativeAdd::where('lead_id', $lead->id)
            ->where('main', 1)
            ->value('address_no') ?? 1;

        return url("/new_lead_profile/{$lead->id}");
    }

    // ---------------------------------------------------------
    // Verify routing (pre_type)
    // ---------------------------------------------------------
    private function handleSpecialTypes(Inquiry $data, string $type, Request $request): array
    {
        $pre = trim((string) $data->pre_type);

        switch ($pre) {
            case 'Lieferant': {
                $dis = $this->distributor($data);
                return ['redirect_url' => url('distributors') . '?name=' . urlencode($dis->name)];
            }

            case 'Hersteller': {
                $brand = $this->brand($data, 'brand');
                return ['redirect_url' => url('brand') . '?search=' . urlencode($brand->name)];
            }

            case 'Kunde':
            case 'Lead': {
                $lead = $this->lead($data);
                return ['redirect_url' => $this->leadProfileUrl($lead)];
            }

            case 'Architekt': {
                $brand = $this->brand($data, 'architect');
                return ['redirect_url' => url('brand_architect')];
            }

            case 'Bank': {
                $brand = $this->brand($data, 'bank');
                return ['redirect_url' => url('brand_bank')];
            }

            case 'Nachunternehmer': {
                $brand = $this->brand($data, 'sub_contractor');
                return ['redirect_url' => url('brand_sub_contractor')];
            }

            case 'Versicherung': {
                $brand = $this->brand($data, 'insurance');
                return ['redirect_url' => url('brand_insurance')];
            }

            case 'Bewerber': {
                $this->markVerified($data);
                return ['redirect_url' => back()->getTargetUrl()];
            }

            default: {
                $brand = $this->brand($data, $type ?: 'other');
                return ['redirect_url' => url('brand_others')];
            }
        }
    }

    // ---------------------------------------------------------
    // Distributor / Brand create + departments + verify
    // ---------------------------------------------------------
    private function distributor(Inquiry $data): Distributor
    {
        $dis = Distributor::firstOrCreate(
            ['name' => $data->firma ?? ('Lieferant-' . mt_rand(10000, 99999))],
            [
                'address' => trim(($data->street . ',' . $data->postcode . ',' . $data->city)) ?: 'unknown',
                'status'  => 'Unpublished',
            ]
        );

        $this->createDistributorDepartment($dis->id, $data);
        $this->markVerified($data);

        return $dis;
    }

    private function brand(Inquiry $data, string $brandType): Brand
    {
        $brand = Brand::firstOrCreate(
            ['name' => $data->firma ?? ('Unternehmen-' . mt_rand(10000, 99999))],
            [
                'address' => trim(($data->street . ',' . $data->postcode . ',' . $data->city)) ?: 'unknown',
                'status'  => 'Unpublished',
                'type'    => $brandType,
            ]
        );

        $this->createBrandDepartment($brand->id, $data);
        $this->markVerified($data);

        return $brand;
    }

    private function createDistributorDepartment(int $distributorId, Inquiry $data): void
    {
        $fullName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
        if ($fullName === '') return;

        $exists = DistributorDepartment::where('d_id', $distributorId)
            ->where('name', $fullName)
            ->exists();

        if ($exists) return;

        DistributorDepartment::create([
            'd_id'         => $distributorId,
            'd_department' => 'Abteilung ist nicht definiert',
            'name'         => $fullName,
            'position'     => 'unknown',
            'phone'        => $data->phone,
            'email'        => $data->email,
            'office'       => $data->telephone,
        ]);
    }

    private function createBrandDepartment(int $brandId, Inquiry $data): void
    {
        $fullName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
        if ($fullName === '') return;

        $exists = BrandDepartment::where('brand_id', $brandId)
            ->where('name', $fullName)
            ->exists();

        if ($exists) return;

        BrandDepartment::create([
            'brand_id'         => $brandId,
            'brand_department' => 'Abteilung-' . mt_rand(10000, 99999),
            'name'             => $fullName,
            'position'         => 'unknown',
            'phone'            => $data->phone,
            'email'            => $data->email,
            'office'           => $data->telephone,
            'status'           => 'Unpublished',
        ]);
    }

    // ---------------------------------------------------------
    // Verify mark
    // ---------------------------------------------------------
    private function markVerified(Inquiry $data): void
    {
        $employeeId = $this->authEmployeeId();

        if ($employeeId) {
            $data->verify_by = $employeeId; // FK to employees.id
        }

        $data->verify_date = now()->toDateString();
        $data->status      = 'Published';
        $data->save();
    }

    private function authEmployeeId(): ?int
    {
        $user = auth()->user();
        if (!$user) return null;

        if (!empty($user->name)) {
            return (int) $user->name;
        }

        $empId = Employee::where('user_id', $user->name)->value('id');
        return $empId ? (int) $empId : null;
    }

    // ---------------------------------------------------------
    // Lead creation (ONLY on confirm, NOT on store)
    // ---------------------------------------------------------
    private function lead(Inquiry $data): NewLeads
    {
        $data->loadMissing('products');

        // Extra safe: if lead exists, reuse it
        $existing = $this->findExistingLead($data);
        if ($existing) {
            $this->markVerified($data);
            return $existing;
        }

        $year = date('Y');
        $customerNo = $year . '-' . mt_rand(10000, 999999);

        $employeeId = $data->contact_person ?? null;
        $branchId   = $data->branch_id ?? null;

        // Your form uses "description"
        $note = $data->description ?? $data->note ?? null;

        $lead = NewLeads::create([
            'customer_type'  => $data->pre_type,
            'firma'          => $data->firma,
            'lastname'       => $data->lastname,
            'name'           => $data->name,
            'full_address'   => trim("{$data->street} {$data->postcode} {$data->city}"),
            'street'         => $data->street,
            'postcode'       => $data->postcode,
            'city'           => $data->city,
            'latitude'       => $data->latitude,
            'longitude'      => $data->longitude,
            'elevation'      => $data->elevation,
            'phone'          => $data->phone,
            'telephone'      => $data->telephone,
            'email'          => $data->email,
            'status'         => 'Lead',
            'source'         => 'Anfrage',
            'info'           => $note,
            'contact_person' => $employeeId,
            'branch'         => $branchId,
            'customer_no'    => $customerNo,
        ]);

        $alt = LeadAlternativeAdd::create([
            'lead_id'      => $lead->id,
            'object_name'  => 'Privathaus',
            'full_address' => $lead->full_address,
            'street'       => $lead->street,
            'postcode'     => $lead->postcode,
            'city'         => $lead->city,
            'lat'          => $lead->latitude,
            'lon'          => $lead->longitude,
            'elevation'    => $lead->elevation,
            'note'         => $note,
            'main'         => 1,
            'address_no'   => 1,
            'stage'        => 'lead',
            'periority'    => 'Normal',
        ]);

        foreach ($data->products as $product) {
            LeadProductList::create([
                'customer_id'    => $lead->id,
                'alternative_id' => $alt->id,
                'product_id'     => $product->product_id,
                'service_id'     => $product->service_id ?? null,
                'department_id'  => $product->department_id ?? null,
                'employee_id'    => $product->employee_id ?? null,
                'field_employee' => $product->field_employee ?? null,
            ]);
        }

        $this->markVerified($data);

        return $lead;
    }
}

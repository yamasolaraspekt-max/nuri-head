<?php

namespace App\Http\Controllers\Inquiry;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\BrandDepartment;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\DistributorDepartment;
use App\Models\Employee;
use App\Models\Inquiry;
use App\Models\InquiryProductList;
use App\Models\InquiryType;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\NewLeads;
use App\Notifications\InquiryNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function perPage(): int
    {
        return 19;
    }

    protected function inquirySortMap(): array
    {
        return [
            'id' => 'inquiries.id',
            'inquiries.name' => 'inquiries.name',
            'types.type' => 'types.type',
            'inquiries.firma' => 'inquiries.firma',
            'inquiries.reason' => 'inquiries.reason',
            'inquiries.note' => 'inquiries.note',
            'emp_name' => 'employees.name',
            'direct_name' => 'direct.name',
            'inquiries.periority' => 'inquiries.periority',
            'inquiries.status' => 'inquiries.status',
            'created_at' => 'inquiries.created_at',
            'name' => 'inquiries.name',
            'email' => 'inquiries.email',
            'status' => 'inquiries.status',
            'periority' => 'inquiries.periority',
        ];
    }

    protected function baseInquiryListQuery()
    {
        return Inquiry::query()
            ->leftJoin('inquiry_types as types', 'types.id', '=', 'inquiries.type')
            ->leftJoin('employees', 'employees.id', '=', 'inquiries.contact_person')
            ->leftJoin('employees as direct', 'direct.id', '=', 'inquiries.direct_to')
            ->leftJoin('branches', 'branches.id', '=', 'inquiries.branch_id')
            ->leftJoin('departments', 'departments.id', '=', 'inquiries.department_id')
            ->select([
                'inquiries.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender as emp_gender',
                'direct.name as direct_name',
                'direct.lastname as direct_lastname',
                'direct.image as direct_image',
                'branches.branch',
                'departments.department_name',
                'types.type as type_name',
            ]);
    }

    protected function validateInquiryRequest(Request $request): array
    {
        return $request->validate([
            'type' => 'nullable',
            'pre_type' => 'nullable',

            // ✅ Name fields are NOT required anymore
            'name' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',

            'branch_id' => 'required|exists:branches,id',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'description' => 'nullable',
            'full_address' => 'nullable',
            'street' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'elevation' => 'nullable',
            'postcode' => 'nullable',
            'city' => 'nullable',
            'telephone' => 'nullable',
            'periority' => 'nullable',
            'title' => 'nullable',
            'source' => 'nullable',
            'next_step' => 'nullable',
            'reason' => 'nullable',

            'product_id' => 'array',
            'service_id' => 'array',
            'department_id' => 'array',
            'employee_id' => 'array',
            'field_employee' => 'array',
            'appointment_date' => 'array',

            'employee_id.*' => 'nullable|integer|exists:employees,id',
            'field_employee.*' => 'nullable|integer|exists:employees,id',
            'product_id.*' => 'nullable|integer|exists:article_groups,id',
            'service_id.*' => 'nullable|integer|exists:phase_sections,id',
            'department_id.*' => 'nullable|integer|exists:departments,id',
            'appointment_date.*' => 'nullable|date',
        ]);
    }

    private function applyInquirySearch($query, ?string $search)
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $like = '%' . $search . '%';
        $digits = preg_replace('/\D+/', '', $search);

        $query->where(function ($q) use ($like, $digits) {
            $q->where('inquiries.id', 'LIKE', $like)
                ->orWhere('inquiries.name', 'LIKE', $like)
                ->orWhere('inquiries.lastname', 'LIKE', $like)
                ->orWhereRaw("CONCAT(COALESCE(inquiries.name,''), ' ', COALESCE(inquiries.lastname,'')) LIKE ?", [$like])
                ->orWhere('inquiries.firma', 'LIKE', $like)
                ->orWhere('inquiries.email', 'LIKE', $like)
                ->orWhere('inquiries.phone', 'LIKE', $like)
                ->orWhere('inquiries.telephone', 'LIKE', $like)
                ->orWhere('inquiries.street', 'LIKE', $like)
                ->orWhere('inquiries.postcode', 'LIKE', $like)
                ->orWhere('inquiries.city', 'LIKE', $like)
                ->orWhere('inquiries.full_address', 'LIKE', $like)
                ->orWhere('inquiries.note', 'LIKE', $like)
                ->orWhere('inquiries.reason', 'LIKE', $like)
                ->orWhere('inquiries.source', 'LIKE', $like)
                ->orWhere('inquiries.status', 'LIKE', $like)
                ->orWhere('inquiries.periority', 'LIKE', $like)
                ->orWhere('inquiries.pre_type', 'LIKE', $like)
                ->orWhere('inquiries.junk_reason', 'LIKE', $like)
                ->orWhere('inquiries.junk_note', 'LIKE', $like)
                ->orWhere('types.type', 'LIKE', $like)
                ->orWhere('branches.branch', 'LIKE', $like)
                ->orWhere('departments.department_name', 'LIKE', $like)
                ->orWhere('employees.name', 'LIKE', $like)
                ->orWhere('employees.lastname', 'LIKE', $like)
                ->orWhereRaw("CONCAT(COALESCE(employees.name,''), ' ', COALESCE(employees.lastname,'')) LIKE ?", [$like])
                ->orWhere('direct.name', 'LIKE', $like)
                ->orWhere('direct.lastname', 'LIKE', $like)
                ->orWhereRaw("CONCAT(COALESCE(direct.name,''), ' ', COALESCE(direct.lastname,'')) LIKE ?", [$like])
                ->orWhereRaw("DATE_FORMAT(inquiries.created_at, '%d.%m.%Y') LIKE ?", [$like])
                ->orWhereRaw("DATE_FORMAT(inquiries.created_at, '%d.%m.%y') LIKE ?", [$like])
                ->orWhereRaw("DATE_FORMAT(inquiries.created_at, '%Y-%m-%d') LIKE ?", [$like]);

            if ($digits !== '') {
                $q->orWhereRaw("REGEXP_REPLACE(COALESCE(inquiries.phone,''), '[^0-9]+', '') LIKE ?", ["%{$digits}%"])
                    ->orWhereRaw("REGEXP_REPLACE(COALESCE(inquiries.telephone,''), '[^0-9]+', '') LIKE ?", ["%{$digits}%"]);
            }

            $q->orWhereIn('inquiries.id', function ($sub) use ($like) {
                $sub->select('ipl.inquiry_id')
                    ->from('inquiry_product_lists as ipl')
                    ->leftJoin('article_groups as ag', 'ag.id', '=', 'ipl.product_id')
                    ->leftJoin('phase_sections as ps', 'ps.id', '=', 'ipl.service_id')
                    ->leftJoin('departments as d2', 'd2.id', '=', 'ipl.department_id')
                    ->leftJoin('employees as ie', 'ie.id', '=', 'ipl.employee_id')
                    ->leftJoin('employees as fe2', 'fe2.id', '=', 'ipl.field_employee')
                    ->where(function ($w) use ($like) {
                        $w->where('ag.article_group', 'LIKE', $like)
                            ->orWhere('ps.phase_section', 'LIKE', $like)
                            ->orWhere('d2.department_name', 'LIKE', $like)
                            ->orWhere('ie.name', 'LIKE', $like)
                            ->orWhere('ie.lastname', 'LIKE', $like)
                            ->orWhere('fe2.name', 'LIKE', $like)
                            ->orWhere('fe2.lastname', 'LIKE', $like);
                    });
            });
        });

        return $query;
    }

    private function applyInquiryFilters($query): void
    {
        if ($employeeId = request('employee_id')) {
            $query->where('inquiries.contact_person', $employeeId);
        }

        if ($directTo = request('direct_to')) {
            $query->where('inquiries.direct_to', $directTo);
        }

        if ($typeFilter = request('type_filter')) {
            if ($typeFilter === 'other') {
                $known = [
                    'Lead',
                    'Lieferant',
                    'Hersteller',
                    'Geschäftspartner',
                    'Architekt',
                    'Nachunternehmer',
                    'Bank',
                    'Versicherung',
                    'Bewerber',
                    'Kunde',
                ];

                $query->where(function ($q) use ($known) {
                    $q->whereNull('types.type')
                        ->where(function ($sq) use ($known) {
                            $sq->whereNull('inquiries.pre_type')
                                ->orWhereNotIn('inquiries.pre_type', $known);
                        });
                });
            } else {
                $query->where(function ($q) use ($typeFilter) {
                    $q->where('types.type', $typeFilter)
                        ->orWhere('inquiries.pre_type', $typeFilter);
                });
            }
        }

        if ($statusFilter = request('status_filter')) {
            $query->whereRaw('LOWER(TRIM(inquiries.status)) = ?', [strtolower(trim($statusFilter))]);
        }

        if ($branchFilter = request('branch_filter')) {
            $query->where('inquiries.branch_id', $branchFilter);
        }

        if (request('already_customer') === '1') {
            $query->whereIn('inquiries.id', function ($sub) {
                $sub->select('i.id')
                    ->from('inquiries as i')
                    ->whereNull('i.deleted_at')
                    ->whereExists(function ($exists) {
                        $exists->select(DB::raw(1))
                            ->from('new_leads as nl')
                            ->whereNull('nl.deleted_at')
                            ->where(function ($w) {
                                $w->whereRaw('LOWER(TRIM(COALESCE(nl.email, ""))) = LOWER(TRIM(COALESCE(i.email, "")))')
                                    ->orWhereRaw("
                                    (
                                        LOWER(TRIM(COALESCE(nl.name, ''))) = LOWER(TRIM(COALESCE(i.name, '')))
                                        AND LOWER(TRIM(COALESCE(nl.lastname, ''))) = LOWER(TRIM(COALESCE(i.lastname, '')))
                                        AND COALESCE(nl.postcode, '') = COALESCE(i.postcode, '')
                                    )
                                ")
                                    ->orWhereRaw("
                                    REGEXP_REPLACE(COALESCE(nl.phone,''), '[^0-9]+', '') =
                                    REGEXP_REPLACE(COALESCE(i.phone,''), '[^0-9]+', '')
                                ")
                                    ->orWhereRaw("
                                    REGEXP_REPLACE(COALESCE(nl.telephone,''), '[^0-9]+', '') =
                                    REGEXP_REPLACE(COALESCE(i.telephone,''), '[^0-9]+', '')
                                ");
                            });
                    });
            });
        }

    }

    private function applyLatestFirstOrder($query, ?string $sortFieldKey, ?string $sortDirection, array $sortMap)
    {
        $sortFieldKey = (string) ($sortFieldKey ?? '');
        $sortDirection = strtolower((string) ($sortDirection ?? 'desc'));

        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        if ($sortFieldKey === '' || $sortFieldKey === 'id') {
            return $query
                ->orderByDesc('inquiries.updated_at')
                ->orderByDesc('inquiries.id');
        }

        if (isset($sortMap[$sortFieldKey])) {
            return $query
                ->orderBy($sortMap[$sortFieldKey], $sortDirection)
                ->orderByDesc('inquiries.updated_at')
                ->orderByDesc('inquiries.id');
        }

        return $query
            ->orderByDesc('inquiries.updated_at')
            ->orderByDesc('inquiries.id');
    }

    private function getInquiryPermissions(): array
    {
        $user = auth()->user();
        $isAdmin = (bool) $user->is_admin;

        // FIX P0-09: user_rolls.user_id = users.id (FK), NICHT users.name; Super-Admin-Bypass.
        $roll = DB::table('user_rolls')
            ->where('user_id', $user->id)
            ->where('item_id', 'Customer')
            ->first();

        $isAllowed = function ($value): bool {
            return in_array(strtolower(trim((string) $value)), ['1', 'on', 'yes', 'true'], true);
        };

        return [
            'canUpdate' => $isAdmin || ($roll && $isAllowed($roll->is_update ?? null)),
            'canDelete' => $isAdmin || ($roll && $isAllowed($roll->is_delete ?? null)),
            'canVerify' => true,
        ];
    }

    private function employeeImageUrl(?string $image, ?string $gender = null): string
    {
        if ($image && file_exists(public_path('images/employee/' . $image))) {
            return asset('images/employee/' . $image);
        }

        return asset(
            strtolower((string) $gender) === 'female'
            ? 'images/gender/female.png'
            : 'images/gender/male.png'
        );
    }

    private function sharedListData(array &$data, array $idsOnPage = []): void
    {
        $data['types'] = InquiryType::all();
        $data['permissions'] = $this->getInquiryPermissions();
        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image')->orderBy('name')->get();
        $data['departments'] = Department::where('status', 'published')->orderBy('department_name')->get();
        $data['branches'] = Branch::select('id', 'branch')->orderBy('branch')->get();
        $data['products'] = DB::table('article_groups')->orderBy('article_group')->get();

        $data['serviceList'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->orderBy('phase_section')
            ->get();

        $productQuery = DB::table('inquiry_product_lists as ip')
            ->leftJoin('departments as d', 'd.id', '=', 'ip.department_id')
            ->leftJoin('employees as e', 'e.id', '=', 'ip.employee_id')
            ->leftJoin('employees as fe', 'fe.id', '=', 'ip.field_employee')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'ip.product_id')
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'ip.service_id')
            ->select([
                'ip.*',
                'd.department_name',
                'ag.article_group',
                'ag.initial',
                'ps.phase_section',
                'e.name as ename',
                'e.lastname as elastname',
                'e.image as eimage',
                'e.gender as egender',
                'fe.name as fname',
                'fe.lastname as flastname',
                'fe.image as fimage',
                'fe.gender as fgender',
            ]);

        if (!empty($idsOnPage)) {
            $productQuery->whereIn('ip.inquiry_id', $idsOnPage);
        }

        $data['productList'] = $productQuery
            ->orderBy('ip.inquiry_id')
            ->orderBy('ip.id')
            ->get()
            ->map(function ($row) {
                $row->inside_image_url = $this->employeeImageUrl($row->eimage ?? null, $row->egender ?? null);
                $row->field_image_url = $this->employeeImageUrl($row->fimage ?? null, $row->fgender ?? null);
                return $row;
            });

        $data['productListGrouped'] = $data['productList']->groupBy('inquiry_id');

        $data['existingLeadMatches'] = !empty($idsOnPage)
            ? $this->getExistingLeadMatchesForInquiries($idsOnPage)
            : collect();
    }

    private function normalizeTextValue($value): string
    {
        return Str::lower(trim((string) $value));
    }

    private function normalizePhoneValue($value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    private function findMatchingLeadsForInquiry(
        Inquiry $inquiry,
        string $email,
        string $phone,
        string $telephone,
        string $name,
        string $lastname,
        string $firma,
        string $street,
        string $postcode,
        string $city,
        $inquiryProductIds
    ) {
        $inquiryProductIds = collect($inquiryProductIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $candidateQuery = NewLeads::query()
            ->with([
                'leadProductLists.product:id,article_group,initial',
                'mainAddress',
            ])
            ->whereNull('new_leads.deleted_at')
            ->where(function ($q) use ($email, $phone, $telephone, $name, $lastname, $firma, $street, $postcode, $city, $inquiryProductIds) {
                if ($email !== '') {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(email, ""))) = ?', [$email]);
                }

                if ($phone !== '') {
                    $q->orWhereRaw("REGEXP_REPLACE(COALESCE(phone,''), '[^0-9]+', '') = ?", [$phone])
                        ->orWhereRaw("REGEXP_REPLACE(COALESCE(telephone,''), '[^0-9]+', '') = ?", [$phone]);
                }

                if ($telephone !== '') {
                    $q->orWhereRaw("REGEXP_REPLACE(COALESCE(phone,''), '[^0-9]+', '') = ?", [$telephone])
                        ->orWhereRaw("REGEXP_REPLACE(COALESCE(telephone,''), '[^0-9]+', '') = ?", [$telephone]);
                }

                if ($name !== '' && $lastname !== '') {
                    $q->orWhere(function ($sq) use ($name, $lastname) {
                        $sq->whereRaw('LOWER(TRIM(COALESCE(name, ""))) = ?', [$name])
                            ->whereRaw('LOWER(TRIM(COALESCE(lastname, ""))) = ?', [$lastname]);
                    });
                }

                if ($firma !== '') {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(firma, ""))) = ?', [$firma]);
                }

                if ($street !== '' && $postcode !== '') {
                    $q->orWhere(function ($sq) use ($street, $postcode) {
                        $sq->whereRaw('LOWER(TRIM(COALESCE(street, ""))) = ?', [$street])
                            ->where('postcode', $postcode);
                    });
                }

                if ($postcode !== '' && $city !== '') {
                    $q->orWhere(function ($sq) use ($postcode, $city) {
                        $sq->where('postcode', $postcode)
                            ->whereRaw('LOWER(TRIM(COALESCE(city, ""))) = ?', [$city]);
                    });
                }

                if ($inquiryProductIds->isNotEmpty()) {
                    $q->orWhereHas('leadProductLists', function ($pq) use ($inquiryProductIds) {
                        $pq->whereIn('product_id', $inquiryProductIds->all());
                    });
                }
            })
            ->limit(20);

        return $candidateQuery->get()
            ->toBase()
            ->map(function ($lead) use ($inquiry, $email, $phone, $telephone, $name, $lastname, $firma, $street, $postcode, $city, $inquiryProductIds) {
                $score = 0;
                $reasons = [];

                $leadEmail = $this->normalizeTextValue($lead->email);
                $leadPhone = $this->normalizePhoneValue($lead->phone);
                $leadTelephone = $this->normalizePhoneValue($lead->telephone);
                $leadName = $this->normalizeTextValue($lead->name);
                $leadLastname = $this->normalizeTextValue($lead->lastname);
                $leadFirma = $this->normalizeTextValue($lead->firma);
                $leadStreet = $this->normalizeTextValue($lead->street);
                $leadPostcode = trim((string) $lead->postcode);
                $leadCity = $this->normalizeTextValue($lead->city);

                if ($email !== '' && $email === $leadEmail) {
                    $score += 45;
                    $reasons[] = 'E-Mail identisch';
                }

                if (($phone !== '' && ($phone === $leadPhone || $phone === $leadTelephone)) || ($telephone !== '' && ($telephone === $leadPhone || $telephone === $leadTelephone))) {
                    $score += 40;
                    $reasons[] = 'Telefon identisch';
                }

                if ($name !== '' && $lastname !== '' && $name === $leadName && $lastname === $leadLastname) {
                    $score += 35;
                    $reasons[] = 'Name identisch';
                }

                if ($firma !== '' && $firma === $leadFirma) {
                    $score += 25;
                    $reasons[] = 'Firma identisch';
                }

                if ($street !== '' && $postcode !== '' && $street === $leadStreet && $postcode === $leadPostcode) {
                    $score += 35;
                    $reasons[] = 'Adresse identisch';
                } elseif ($postcode !== '' && $city !== '' && $postcode === $leadPostcode && $city === $leadCity) {
                    $score += 15;
                    $reasons[] = 'PLZ/Ort ähnlich';
                }

                $leadProductIds = collect($lead->leadProductLists ?? [])
                    ->pluck('product_id')
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values();

                $productOverlap = $inquiryProductIds->intersect($leadProductIds)->values();

                if ($productOverlap->isNotEmpty()) {
                    $score += 20;
                    $reasons[] = 'Produkt überschneidet sich';
                }

                $leadProducts = collect($lead->leadProductLists ?? [])
                    ->filter(fn($row) => $productOverlap->contains((int) data_get($row, 'product_id')))
                    ->map(fn($row) => data_get($row, 'product.article_group'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'source_type' => 'lead',
                    'inquiry_id' => $inquiry->id,
                    'lead_id' => $lead->id,
                    'customer_no' => $lead->customer_no,
                    'display_name' => $lead->display_name ?: trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? '')),
                    'firma' => $lead->firma,
                    'name' => trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? '')),
                    'email' => $lead->email,
                    'phone' => $lead->phone ?: $lead->telephone,
                    'street' => $lead->street,
                    'postcode' => $lead->postcode,
                    'city' => $lead->city,
                    'status' => $lead->status,
                    'purchase_status' => $lead->purchase_status,
                    'score' => $score,
                    'reasons' => array_values(array_unique($reasons)),
                    'products' => $leadProducts,
                    'profile_url' => route('new.lead.profile', [
                        $lead->id,
                        $lead->postcode ?? '',
                        optional($lead->mainAddress)->address_no ?? 1,
                    ]),
                ];
            })
            ->filter(fn($match) => (int) data_get($match, 'score', 0) >= 40)
            ->values();
    }

    private function findMatchingDuplicateInquiries(
        Inquiry $inquiry,
        string $email,
        string $phone,
        string $telephone,
        string $name,
        string $lastname,
        string $firma,
        string $street,
        string $postcode,
        string $city,
        $inquiryProductIds
    ) {
        $inquiryProductIds = collect($inquiryProductIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $candidateQuery = Inquiry::query()
            ->whereNull('deleted_at')
            ->where('id', '!=', $inquiry->id)
            ->where(function ($q) use ($email, $phone, $telephone, $name, $lastname, $firma, $street, $postcode, $city, $inquiryProductIds) {
                if ($email !== '') {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(email, ""))) = ?', [$email]);
                }

                if ($phone !== '') {
                    $q->orWhereRaw("REGEXP_REPLACE(COALESCE(phone,''), '[^0-9]+', '') = ?", [$phone])
                        ->orWhereRaw("REGEXP_REPLACE(COALESCE(telephone,''), '[^0-9]+', '') = ?", [$phone]);
                }

                if ($telephone !== '') {
                    $q->orWhereRaw("REGEXP_REPLACE(COALESCE(phone,''), '[^0-9]+', '') = ?", [$telephone])
                        ->orWhereRaw("REGEXP_REPLACE(COALESCE(telephone,''), '[^0-9]+', '') = ?", [$telephone]);
                }

                if ($name !== '' && $lastname !== '') {
                    $q->orWhere(function ($sq) use ($name, $lastname) {
                        $sq->whereRaw('LOWER(TRIM(COALESCE(name, ""))) = ?', [$name])
                            ->whereRaw('LOWER(TRIM(COALESCE(lastname, ""))) = ?', [$lastname]);
                    });
                }

                if ($firma !== '') {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(firma, ""))) = ?', [$firma]);
                }

                if ($street !== '' && $postcode !== '') {
                    $q->orWhere(function ($sq) use ($street, $postcode) {
                        $sq->whereRaw('LOWER(TRIM(COALESCE(street, ""))) = ?', [$street])
                            ->where('postcode', $postcode);
                    });
                }

                if ($postcode !== '' && $city !== '') {
                    $q->orWhere(function ($sq) use ($postcode, $city) {
                        $sq->where('postcode', $postcode)
                            ->whereRaw('LOWER(TRIM(COALESCE(city, ""))) = ?', [$city]);
                    });
                }

                if ($inquiryProductIds->isNotEmpty()) {
                    $q->orWhereIn('id', function ($sub) use ($inquiryProductIds) {
                        $sub->select('inquiry_id')
                            ->from('inquiry_product_lists')
                            ->whereIn('product_id', $inquiryProductIds->all());
                    });
                }
            })
            ->limit(20);

        $duplicates = $candidateQuery->get()->toBase();
        $duplicateIds = $duplicates->pluck('id')->filter()->values()->all();

        $productsByDuplicate = collect();

        if (!empty($duplicateIds)) {
            $productsByDuplicate = DB::table('inquiry_product_lists as ip')
                ->leftJoin('article_groups as ag', 'ag.id', '=', 'ip.product_id')
                ->whereIn('ip.inquiry_id', $duplicateIds)
                ->select([
                    'ip.inquiry_id',
                    'ip.product_id',
                    'ag.article_group',
                ])
                ->get()
                ->groupBy('inquiry_id');
        }

        return $duplicates
            ->map(function ($duplicate) use ($inquiry, $email, $phone, $telephone, $name, $lastname, $firma, $street, $postcode, $city, $inquiryProductIds, $productsByDuplicate) {
                $score = 0;
                $reasons = [];

                $dupEmail = $this->normalizeTextValue($duplicate->email);
                $dupPhone = $this->normalizePhoneValue($duplicate->phone);
                $dupTelephone = $this->normalizePhoneValue($duplicate->telephone);
                $dupName = $this->normalizeTextValue($duplicate->name);
                $dupLastname = $this->normalizeTextValue($duplicate->lastname);
                $dupFirma = $this->normalizeTextValue($duplicate->firma);
                $dupStreet = $this->normalizeTextValue($duplicate->street);
                $dupPostcode = trim((string) $duplicate->postcode);
                $dupCity = $this->normalizeTextValue($duplicate->city);

                if ($email !== '' && $email === $dupEmail) {
                    $score += 45;
                    $reasons[] = 'E-Mail identisch';
                }

                if (($phone !== '' && ($phone === $dupPhone || $phone === $dupTelephone)) || ($telephone !== '' && ($telephone === $dupPhone || $telephone === $dupTelephone))) {
                    $score += 40;
                    $reasons[] = 'Telefon identisch';
                }

                if ($name !== '' && $lastname !== '' && $name === $dupName && $lastname === $dupLastname) {
                    $score += 35;
                    $reasons[] = 'Name identisch';
                }

                if ($firma !== '' && $firma === $dupFirma) {
                    $score += 25;
                    $reasons[] = 'Firma identisch';
                }

                if ($street !== '' && $postcode !== '' && $street === $dupStreet && $postcode === $dupPostcode) {
                    $score += 35;
                    $reasons[] = 'Adresse identisch';
                } elseif ($postcode !== '' && $city !== '' && $postcode === $dupPostcode && $city === $dupCity) {
                    $score += 15;
                    $reasons[] = 'PLZ/Ort ähnlich';
                }

                $duplicateProductRows = collect($productsByDuplicate->get($duplicate->id, collect()));
                $duplicateProductIds = $duplicateProductRows
                    ->pluck('product_id')
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values();

                $productOverlap = $inquiryProductIds->intersect($duplicateProductIds)->values();

                if ($productOverlap->isNotEmpty()) {
                    $score += 20;
                    $reasons[] = 'Produkt überschneidet sich';
                }

                $duplicateProducts = $duplicateProductRows
                    ->filter(fn($row) => $productOverlap->contains((int) $row->product_id))
                    ->pluck('article_group')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'source_type' => 'inquiry',
                    'inquiry_id' => $inquiry->id,
                    'duplicate_id' => $duplicate->id,
                    'customer_no' => null,
                    'display_name' => trim(($duplicate->name ?? '') . ' ' . ($duplicate->lastname ?? '')),
                    'firma' => $duplicate->firma,
                    'name' => trim(($duplicate->name ?? '') . ' ' . ($duplicate->lastname ?? '')),
                    'email' => $duplicate->email,
                    'phone' => $duplicate->phone ?: $duplicate->telephone,
                    'street' => $duplicate->street,
                    'postcode' => $duplicate->postcode,
                    'city' => $duplicate->city,
                    'status' => $duplicate->status,
                    'score' => $score,
                    'reasons' => array_values(array_unique($reasons)),
                    'products' => $duplicateProducts,
                    'profile_url' => route('inquiry.show.profile', $duplicate->id),
                ];
            })
            ->filter(fn($match) => (int) data_get($match, 'score', 0) >= 40)
            ->values();
    }
    private function getExistingLeadMatchesForInquiries(array $inquiryIds)
    {
        $inquiryIds = collect($inquiryIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($inquiryIds)) {
            return collect();
        }

        $inquiries = Inquiry::query()
            ->whereIn('id', $inquiryIds)
            ->get();

        $productIdsByInquiry = DB::table('inquiry_product_lists')
            ->whereIn('inquiry_id', $inquiryIds)
            ->whereNotNull('product_id')
            ->select('inquiry_id', 'product_id')
            ->get()
            ->groupBy('inquiry_id')
            ->map(function ($rows) {
                return $rows
                    ->pluck('product_id')
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values();
            });

        $matches = collect();

        foreach ($inquiries as $inquiry) {
            $email = $this->normalizeTextValue($inquiry->email);
            $phone = $this->normalizePhoneValue($inquiry->phone);
            $telephone = $this->normalizePhoneValue($inquiry->telephone);
            $name = $this->normalizeTextValue($inquiry->name);
            $lastname = $this->normalizeTextValue($inquiry->lastname);
            $firma = $this->normalizeTextValue($inquiry->firma);
            $street = $this->normalizeTextValue($inquiry->street);
            $postcode = trim((string) $inquiry->postcode);
            $city = $this->normalizeTextValue($inquiry->city);

            $inquiryProductIds = collect($productIdsByInquiry->get($inquiry->id, collect()))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $leadMatches = $this->findMatchingLeadsForInquiry(
                $inquiry,
                $email,
                $phone,
                $telephone,
                $name,
                $lastname,
                $firma,
                $street,
                $postcode,
                $city,
                $inquiryProductIds
            );

            $inquiryMatches = $this->findMatchingDuplicateInquiries(
                $inquiry,
                $email,
                $phone,
                $telephone,
                $name,
                $lastname,
                $firma,
                $street,
                $postcode,
                $city,
                $inquiryProductIds
            );

            $allMatches = collect($leadMatches instanceof \Illuminate\Support\Collection ? $leadMatches->values()->all() : $leadMatches)
                ->merge($inquiryMatches instanceof \Illuminate\Support\Collection ? $inquiryMatches->values()->all() : $inquiryMatches)
                ->sortByDesc(fn($match) => (int) data_get($match, 'score', 0))
                ->values();

            if ($allMatches->isNotEmpty()) {
                $matches->put($inquiry->id, $allMatches);
            }
        }

        return $matches;
    }
    public function index()
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = $this->baseInquiryListQuery()
            ->whereNull('inquiries.deleted_at')
            ->whereNotIn(DB::raw('TRIM(LOWER(inquiries.status))'), ['junk', 'published'])
            ->where(function ($q) {
                $q->whereNull('inquiries.pre_type')
                    ->orWhere('inquiries.pre_type', '!=', 'Kunde');
            });

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    public function customer()
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = $this->baseInquiryListQuery()
            ->whereNull('inquiries.deleted_at')
            ->where('inquiries.pre_type', 'Kunde')
            ->whereNotIn(DB::raw('TRIM(LOWER(inquiries.status))'), ['junk', 'published']);

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    public function my_inquiry()
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = $this->baseInquiryListQuery()
            ->whereNull('inquiries.deleted_at')
            ->whereNotIn(DB::raw('TRIM(LOWER(inquiries.status))'), ['junk', 'published'])
            ->where('inquiries.contact_person', auth()->user()->name);

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    public function junk_list()
    {
        return $this->filteredStatusList('junk');
    }

    public function published()
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = $this->baseInquiryListQuery()
            ->whereNull('inquiries.deleted_at')
            ->whereRaw('LOWER(TRIM(inquiries.status)) = ?', ['published']);

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    public function deleted_list()
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = Inquiry::onlyTrashed()
            ->leftJoin('inquiry_types as types', 'types.id', '=', 'inquiries.type')
            ->leftJoin('employees', 'employees.id', '=', 'inquiries.contact_person')
            ->leftJoin('employees as direct', 'direct.id', '=', 'inquiries.direct_to')
            ->leftJoin('branches', 'branches.id', '=', 'inquiries.branch_id')
            ->leftJoin('departments', 'departments.id', '=', 'inquiries.department_id')
            ->select([
                'inquiries.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender as emp_gender',
                'direct.name as direct_name',
                'direct.lastname as direct_lastname',
                'direct.image as direct_image',
                'branches.branch',
                'departments.department_name',
                'types.type as type_name',
            ]);

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    private function filteredStatusList(string $status)
    {
        $search = trim((string) request('search', ''));
        $sortFieldKey = (string) request('sort', '');
        $sortDirection = strtolower((string) request('direction', 'desc'));
        $sortMap = $this->inquirySortMap();

        if ($sortFieldKey !== '' && !isset($sortMap[$sortFieldKey])) {
            $sortFieldKey = '';
        }

        $query = $this->baseInquiryListQuery()
            ->whereNull('inquiries.deleted_at')
            ->whereRaw('LOWER(TRIM(inquiries.status)) = ?', [strtolower(trim($status))]);

        $this->applyInquirySearch($query, $search);
        $this->applyInquiryFilters($query);
        $this->applyLatestFirstOrder($query, $sortFieldKey, $sortDirection, $sortMap);

        $data['data'] = $query->paginate($this->perPage())->appends(request()->query());
        $this->sharedListData($data, $data['data']->pluck('id')->all());

        return view('admin.inquiry.contact_list', $data);
    }

    public function create()
    {
        $data['branches'] = Branch::all();
        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image')->get();
        $data['departments'] = Department::where('status', 'published')->get();
        $data['products'] = DB::table('article_groups')->get();
        $data['services'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section', 'status', 'deleted_at')
            ->get();

        return view('admin.inquiry.contact', $data);
    }

    public function store(Request $request)
    {
        $this->validateInquiryRequest($request);

        $inquiry = new Inquiry();
        $inquiry->type = $request->pre_type === "None" ? $request->type : null;
        $inquiry->pre_type = $request->pre_type === "None" ? null : $request->pre_type;
        $inquiry->name = $request->name;
        $inquiry->lastname = $request->lastname;
        $inquiry->branch_id = $request->branch_id;
        $inquiry->contact_person = auth()->user()->name ?? null;
        $inquiry->title = $request->title;
        $inquiry->email = $request->email;
        $inquiry->phone = $request->phone;
        $inquiry->full_address = trim(($request->street ?? '') . ' ' . ($request->postcode ?? '') . ' ' . ($request->city ?? ''));
        $inquiry->street = $request->street;
        $inquiry->latitude = $request->latitude;
        $inquiry->longitude = $request->longitude;
        $inquiry->elevation = $request->elevation;
        $inquiry->postcode = $request->postcode;
        $inquiry->city = $request->city;
        $inquiry->telephone = $request->telephone;
        $inquiry->note = $request->description;
        $inquiry->periority = $request->periority;
        $inquiry->next_step = $request->next_step;
        $inquiry->reason = $request->reason;
        $inquiry->source = $request->source;
        $inquiry->status = 'Unpublished';
        $inquiry->save();

        $tz = 'Europe/Berlin';
        $rows = is_array($request->product_id) ? count($request->product_id) : 0;

        for ($i = 0; $i < $rows; $i++) {
            InquiryProductList::create([
                'inquiry_id' => $inquiry->id,
                'product_id' => $request->product_id[$i] ?? null,
                'service_id' => $request->service_id[$i] ?? null,
                'department_id' => $request->department_id[$i] ?? null,
                'employee_id' => isset($request->employee_id[$i]) && is_numeric($request->employee_id[$i]) ? (int) $request->employee_id[$i] : null,
                'field_employee' => isset($request->field_employee[$i]) && is_numeric($request->field_employee[$i]) ? (int) $request->field_employee[$i] : null,
                'appointment_date' => $request->appointment_date[$i] ?? null,
                'status' => 'open',
            ]);

            $dtLocal = $request->appointment_date[$i] ?? null;

            if (!$dtLocal) {
                continue;
            }

            $creatorEmpId = null;

            if (!empty($request->employee_id[$i]) && is_numeric($request->employee_id[$i])) {
                $creatorEmpId = (int) $request->employee_id[$i];
            } elseif (!empty($request->field_employee[$i]) && is_numeric($request->field_employee[$i])) {
                $creatorEmpId = (int) $request->field_employee[$i];
            }

            if (!$creatorEmpId) {
                Log::warning('Termin übersprungen: Kein employee_id/field_employee für created_by. Zeile=' . $i);
                continue;
            }

            try {
                $start = Carbon::parse($dtLocal, $tz);
            } catch (\Throwable $e) {
                Log::warning('Ungültiges appointment_date, Termin übersprungen.', ['row' => $i, 'value' => $dtLocal]);
                continue;
            }

            $startDate = $start->toDateString();
            $startTime = $start->format('H:i:s');
            $end = $start->copy()->addHour();
            $endDate = $end->toDateString();
            $endTime = $end->format('H:i:s');

            $serviceLabel = $request->service_id[$i] ?? null;

            $titleParts = [
                trim(($request->name ?? '') . ' ' . ($request->lastname ?? '')),
                $serviceLabel ? "Service #{$serviceLabel}" : null,
            ];

            $apptName = implode(' – ', array_filter($titleParts));
            $link = route('inquiry.show.profile', $inquiry->id);

            $color = '#93c21c';
            if (empty($request->employee_id[$i]) && !empty($request->field_employee[$i])) {
                $color = '#74b2d4';
            }

            $productsJson = null;
            if (!empty($request->product_id[$i]) && is_numeric($request->product_id[$i])) {
                $productsJson = json_encode([(int) $request->product_id[$i]]);
            }

            $appointment = new MainAppointment();
            $appointment->created_by = $creatorEmpId;
            $appointment->name = $apptName ?: 'Termin';
            $appointment->execution_type = null;
            $appointment->appointment_type = 'inquiry';
            $appointment->contact_mode = $request->source ?? null;
            $appointment->note = $request->description ?? null;
            $appointment->color = $color;
            $appointment->start_date = $startDate;
            $appointment->end_date = $endDate;
            $appointment->start_time = $startTime;
            $appointment->end_time = $endTime;
            $appointment->total_time = 60;
            $appointment->date_type = null;
            $appointment->repeat = null;
            $appointment->full_address = $inquiry->full_address;
            $appointment->street = $inquiry->street;
            $appointment->latitude = $inquiry->latitude;
            $appointment->longitude = $inquiry->longitude;
            $appointment->postcode = $inquiry->postcode;
            $appointment->city = $inquiry->city;
            $appointment->phone = $inquiry->phone ?? $inquiry->telephone;
            $appointment->email = $inquiry->email;
            $appointment->status = 'planned';
            $appointment->link = $link;
            $appointment->priority = $request->periority ?? null;
            $appointment->public = 'no';
            $appointment->is_notified = false;
            $appointment->type = 'appointment';
            $appointment->branch_id = $inquiry->branch_id;
            $appointment->customer_id = null;
            $appointment->products = $productsJson;
            $appointment->contact_id = null;
            $appointment->task_id = null;
            $appointment->contact_type = $request->pre_type === "None" ? $request->type : $request->pre_type;
            $appointment->source = $request->source ?? null;
            $appointment->is_report = 'false';
            $appointment->save();

            $toAttach = [];
            if (!empty($request->employee_id[$i]) && is_numeric($request->employee_id[$i])) {
                $toAttach[] = (int) $request->employee_id[$i];
            }
            if (!empty($request->field_employee[$i]) && is_numeric($request->field_employee[$i])) {
                $toAttach[] = (int) $request->field_employee[$i];
            }

            $toAttach = array_values(array_unique($toAttach));

            foreach ($toAttach as $empId) {
                MainAppointmentEmployee::create([
                    'employee_id' => $empId,
                    'appointment_id' => $appointment->id,
                    'status' => 'send',
                    'reason' => null,
                ]);
            }
        }

        $inquiryType = $request->pre_type === "None" ? $request->type : $request->pre_type;
        $id = $inquiry->id;

        Notification::send(auth()->user(), new InquiryNotification([
            'title' => 'Neue Anfrage',
            'message' => "Sie haben eine Anfrage von {$inquiryType} in " . $request->city,
            'lead_id' => $id,
            'type' => 'inquiry',
            'from' => $inquiry->contact_person,
            'contact_type' => $inquiryType,
        ]));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'inquiry_id' => $inquiry->id,
                'redirect_url' => route('inquiry.show.profile', $inquiry->id),
            ]);
        }

        return redirect()
            ->route('inquiry.show.profile', $id)
            ->with('success', 'Anfrage erfolgreich erstellt.');
    }

    public function show($id)
    {
        $inquiry = DB::table('inquiries')
            ->leftJoin('inquiry_types as types', 'types.id', '=', 'inquiries.type')
            ->leftJoin('branches', 'branches.id', '=', 'inquiries.branch_id')
            ->leftJoin('employees', 'employees.id', '=', 'inquiries.contact_person')
            ->select(
                'inquiries.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'branches.branch',
                'types.type as type_name'
            )
            ->where('inquiries.id', $id)
            ->first();

        if (!$inquiry) {
            abort(404, 'Anfrage nicht gefunden.');
        }

        $data['data'] = $inquiry;
        $data['types'] = DB::table('inquiry_types')->orderBy('id', 'desc')->get();
        $data['branches'] = Branch::all();
        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image')->get();
        $data['departments'] = Department::where('status', 'published')->get();
        $data['products'] = DB::table('article_groups')->get();
        $data['serviceList'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section', 'status', 'deleted_at')
            ->get();

        $data['productList'] = DB::table('inquiry_product_lists as ip')
            ->leftJoin('departments as d', 'd.id', '=', 'ip.department_id')
            ->leftJoin('employees as e', 'e.id', '=', 'ip.employee_id')
            ->leftJoin('employees as fe', 'fe.id', '=', 'ip.field_employee')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'ip.product_id')
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'ip.service_id')
            ->select(
                'ip.*',
                'd.department_name',
                'ag.article_group',
                'ag.initial',
                'ag.image as pimage',
                'ps.phase_section',
                'e.name as ename',
                'e.lastname as elastname',
                'e.image as eimage',
                'e.gender as egender',
                'fe.name as fname',
                'fe.lastname as flastname',
                'fe.image as fimage',
                'fe.gender as fgender'
            )
            ->where('ip.inquiry_id', $id)
            ->get();

        return view('admin.inquiry.contact_profile', $data);
    }

    public function showProfile($id)
    {
        return $this->show($id);
    }

    public function edit($id)
    {
        $inquiry = Inquiry::find($id);

        if (!$inquiry) {
            return redirect()->back()->with('error', 'Anfrage wurde nicht gefunden.');
        }

        if ($inquiry->deleted_at !== null) {
            return redirect()->back()->with('error', 'Sie können eine gelöschte Anfrage nicht bearbeiten.');
        }

        $data['branches'] = Branch::all();
        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image')->get();
        $data['departments'] = Department::where('status', 'published')->get();
        $data['types'] = DB::table('inquiry_types')->get();
        $data['data'] = $inquiry;
        $data['products'] = DB::table('article_groups')->get();
        $data['serviceList'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section', 'status', 'deleted_at')
            ->get();

        $data['productList'] = DB::table('inquiry_product_lists')
            ->leftJoin('departments', 'departments.id', '=', 'inquiry_product_lists.department_id')
            ->leftJoin('employees', 'employees.id', '=', 'inquiry_product_lists.employee_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'inquiry_product_lists.product_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'inquiry_product_lists.service_id')
            ->select(
                'departments.department_name',
                'employees.name as ename',
                'employees.lastname as elastname',
                'employees.image as eimage',
                'employees.gender',
                'article_groups.article_group',
                'article_groups.initial',
                'article_groups.image as pimage',
                'phase_sections.phase_section',
                'inquiry_product_lists.*'
            )
            ->where('inquiry_product_lists.inquiry_id', $id)
            ->get();

        return view('admin.inquiry.contact_edit', $data);
    }

    public function update(Request $request)
    {
        abort_unless($this->getInquiryPermissions()['canUpdate'], 403, 'Keine Berechtigung, Anfragen zu bearbeiten.'); // FIX P0-09

        $this->validateInquiryRequest($request);

        $inquiry = Inquiry::findOrFail($request->id);

        $inquiry->type = $request->pre_type === "None" ? $request->type : null;
        $inquiry->pre_type = $request->pre_type === "None" ? null : $request->pre_type;
        $inquiry->name = $request->name;
        $inquiry->lastname = $request->lastname;
        $inquiry->branch_id = $request->branch_id;
        $inquiry->title = $request->title;
        $inquiry->email = $request->email;
        $inquiry->phone = $request->phone;
        $inquiry->full_address = trim(($request->street ?? '') . ' ' . ($request->postcode ?? '') . ' ' . ($request->city ?? ''));
        $inquiry->street = $request->street;
        $inquiry->latitude = $request->latitude;
        $inquiry->longitude = $request->longitude;
        $inquiry->elevation = $request->elevation;
        $inquiry->postcode = $request->postcode;
        $inquiry->city = $request->city;
        $inquiry->telephone = $request->telephone;
        $inquiry->note = $request->description;
        $inquiry->periority = $request->periority;
        $inquiry->source = $request->source;
        // FIX P2-29: Status beim Bearbeiten erhalten. Nur initialisieren, wenn er
        // noch leer oder ein Draft ist; sonst (Published/Junk/Unpublished) beibehalten.
        if (empty($inquiry->status) || $inquiry->status === 'Draft') {
            $inquiry->status = 'Unpublished';
        }
        $inquiry->save();

        InquiryProductList::where('inquiry_id', $inquiry->id)->delete();

        $rows = is_array($request->product_id) ? count($request->product_id) : 0;

        for ($i = 0; $i < $rows; $i++) {
            InquiryProductList::create([
                'inquiry_id' => $inquiry->id,
                'product_id' => $request->product_id[$i] ?? null,
                'service_id' => $request->service_id[$i] ?? null,
                'department_id' => $request->department_id[$i] ?? null,
                'employee_id' => !empty($request->employee_id[$i]) && is_numeric($request->employee_id[$i]) ? (int) $request->employee_id[$i] : null,
                'field_employee' => !empty($request->field_employee[$i]) && is_numeric($request->field_employee[$i]) ? (int) $request->field_employee[$i] : null,
                'appointment_date' => $request->appointment_date[$i] ?? null,
                'status' => 'open',
            ]);
        }

        DB::table('main_appointments')
            ->where('appointment_type', 'inquiry')
            ->where('other_id', $inquiry->id)
            ->delete();

        $tz = 'Europe/Berlin';

        for ($i = 0; $i < $rows; $i++) {
            $dtLocal = $request->appointment_date[$i] ?? null;
            if (!$dtLocal) {
                continue;
            }

            $creatorEmpId = null;

            if (!empty($request->employee_id[$i]) && is_numeric($request->employee_id[$i])) {
                $creatorEmpId = (int) $request->employee_id[$i];
            } elseif (!empty($request->field_employee[$i]) && is_numeric($request->field_employee[$i])) {
                $creatorEmpId = (int) $request->field_employee[$i];
            }

            if (!$creatorEmpId) {
                continue;
            }

            try {
                $start = Carbon::parse($dtLocal, $tz);
            } catch (\Throwable $e) {
                continue;
            }

            $startDate = $start->toDateString();
            $startTime = $start->format('H:i:s');
            $end = $start->copy()->addHour();
            $endDate = $end->toDateString();
            $endTime = $end->format('H:i:s');

            $serviceLabel = $request->service_id[$i] ?? null;
            $apptName = trim(($inquiry->name . ' ' . $inquiry->lastname) . ($serviceLabel ? ' – Service #' . $serviceLabel : ''));

            $color = '#93c21c';
            if (empty($request->employee_id[$i]) && !empty($request->field_employee[$i])) {
                $color = '#74b2d4';
            }

            $productsJson = null;
            if (!empty($request->product_id[$i]) && is_numeric($request->product_id[$i])) {
                $productsJson = json_encode([(int) $request->product_id[$i]]);
            }

            $appointmentId = DB::table('main_appointments')->insertGetId([
                'created_by' => $creatorEmpId,
                'name' => $apptName ?: 'Termin',
                'execution_type' => null,
                'appointment_type' => 'inquiry',
                'contact_mode' => $inquiry->source,
                'note' => $inquiry->note,
                'color' => $color,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_time' => 60,
                'full_address' => $inquiry->full_address,
                'street' => $inquiry->street,
                'latitude' => $inquiry->latitude,
                'longitude' => $inquiry->longitude,
                'postcode' => $inquiry->postcode,
                'city' => $inquiry->city,
                'phone' => $inquiry->phone ?? $inquiry->telephone,
                'email' => $inquiry->email,
                'status' => 'planned',
                'link' => route('inquiry.show.profile', $inquiry->id),
                'priority' => $inquiry->periority,
                'public' => 'no',
                'is_notified' => false,
                'type' => 'appointment',
                'branch_id' => $inquiry->branch_id,
                'customer_id' => null,
                'products' => $productsJson,
                'other_id' => $inquiry->id,
                'contact_id' => null,
                'task_id' => null,
                'contact_type' => ($request->pre_type === "None" ? $request->type : $request->pre_type),
                'source' => $inquiry->source,
                'is_report' => 'false',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $toAttach = [];
            if (!empty($request->employee_id[$i]) && is_numeric($request->employee_id[$i])) {
                $toAttach[] = (int) $request->employee_id[$i];
            }
            if (!empty($request->field_employee[$i]) && is_numeric($request->field_employee[$i])) {
                $toAttach[] = (int) $request->field_employee[$i];
            }

            $toAttach = array_values(array_unique($toAttach));

            foreach ($toAttach as $empId) {
                DB::table('main_appointment_employees')->insert([
                    'employee_id' => $empId,
                    'appointment_id' => $appointmentId,
                    'status' => 'send',
                    'reason' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $inquiryType = $request->pre_type === "None" ? $request->type : $request->pre_type;

        Notification::send(auth()->user(), new InquiryNotification([
            'title' => 'Anfrage aktualisiert',
            'message' => "Die Anfrage von {$inquiryType} in " . $request->city . " wurde aktualisiert.",
            'lead_id' => $inquiry->id,
            'type' => 'inquiry',
            'from' => auth()->user()->name,
            'contact_type' => $inquiryType,
        ]));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('inquiry.show.profile', $inquiry->id),
            ]);
        }

        return redirect()
            ->route('inquiry.show.profile', $inquiry->id)
            ->with('success', 'Anfrage, Zuweisungen und Termine wurden aktualisiert.');
    }

    public function destroy($id)
    {
        abort_unless($this->getInquiryPermissions()['canDelete'], 403, 'Keine Berechtigung, Anfragen zu loeschen.'); // FIX P0-09

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        return back()->with('delete_msg', 'Anfrage erfolgreich gelöscht');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function restore($id)
    {
        abort_unless($this->getInquiryPermissions()['canUpdate'], 403, 'Keine Berechtigung, Anfragen wiederherzustellen.'); // FIX P0-09

        $inquiry = Inquiry::withTrashed()->findOrFail($id);
        $inquiry->restore();

        return back()->with('save_msg', 'Anfrage erfolgreich wiederhergestellt');
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'type' => ['required', 'string', 'max:255'],
        ]);

        $inquiry = Inquiry::findOrFail($id);

        $productRows = InquiryProductList::query()
            ->where('inquiry_id', $inquiry->id)
            ->get();

        if ($productRows->isEmpty()) {
            return response()->json([
                'message' => 'Anfrage hat keine Produkte und kann nicht verifiziert werden.',
            ], 422);
        }

        $missingEmployeeProduct = $productRows->first(function ($p) {
            return empty($p->employee_id);
        });

        if ($missingEmployeeProduct) {
            return response()->json([
                'message' => 'Mindestens ein Produkt hat keinen zugewiesenen Mitarbeiter.',
            ], 422);
        }

        $type = trim((string) $request->input('type'));
        $inquiry->pre_type = $type;
        $inquiry->status = 'Published';
        $inquiry->save();

        $lowerType = Str::lower($type);
        $redirectUrl = null;

        if (in_array($lowerType, ['kunde', 'lead'], true)) {
            $lead = $this->lead($inquiry, $productRows);
            if ($lead && isset($lead->id)) {
                $redirectUrl = route('new.lead.view', ['highlight_id' => $lead->id]);
            }
        } else {
            switch ($type) {
                case 'Lieferant':
                    $this->distributor($inquiry);
                    $redirectUrl = url('/distributor');
                    break;

                case 'Hersteller':
                    $this->brand($inquiry, 'brand');
                    $redirectUrl = url('/brand');
                    break;

                case 'Architekt':
                    $this->brand($inquiry, 'architect');
                    $redirectUrl = url('/brand_architect');
                    break;

                case 'Bank':
                    $this->brand($inquiry, 'bank');
                    $redirectUrl = url('/brand_bank');
                    break;

                case 'Nachunternehmer':
                    $this->brand($inquiry, 'sub_contractor');
                    $redirectUrl = url('/brand_sub_contractor');
                    break;

                case 'Versicherung':
                    $this->brand($inquiry, 'insurance');
                    $redirectUrl = url('/brand_insurance');
                    break;

                case 'Bewerber':
                    $this->updateVerification($inquiry);
                    break;

                default:
                    $this->others($inquiry, $type);
                    $redirectUrl = url('/brand_others');
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Die Anfrage wurde erfolgreich verifiziert.',
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function junk(Request $request, $id)
    {
        $request->validate([
            'junk_reason' => ['required', 'string', 'max:255'],
            'junk_note' => ['nullable', 'string'],
        ]);

        abort_unless($this->getInquiryPermissions()['canDelete'], 403, 'Keine Berechtigung, Anfragen als Junk zu markieren.'); // FIX P0-09

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->status = 'Junk';
        $inquiry->junk_reason = $request->input('junk_reason');
        $inquiry->junk_note = $request->input('junk_note');
        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Anfrage wurde als Junk markiert.',
        ]);
    }

    public function unjunk(Request $request, $id)
    {
        abort_unless($this->getInquiryPermissions()['canUpdate'], 403, 'Keine Berechtigung, Anfragen aus Junk zu holen.'); // FIX P0-09

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->status = 'Unpublished';
        $inquiry->junk_reason = null;
        $inquiry->junk_note = null;
        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Anfrage wurde aus Junk entfernt.',
        ]);
    }

    // FIX P1-19: fehlende Methode fuer Kanban-Drag&Drop (Route inquiry.status).
    // Setzt ausschliesslich inquiries.status auf einen validierten, erlaubten Wert.
    public function updateStatus(Request $request, $id)
    {
        abort_unless($this->getInquiryPermissions()['canUpdate'], 403, 'Keine Berechtigung, den Status zu aendern.'); // FIX P0-09

        $validated = $request->validate([
            'status' => 'required|string|in:Unpublished,progress,verified,junk,Published,Junk,Draft',
        ]);

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->status = $validated['status'];
        $inquiry->save();

        return response()->json([
            'success' => true,
            'status'  => $inquiry->status,
            'message' => 'Status aktualisiert.',
        ]);
    }

    public function departmentEmployees(Request $request)
    {
        $raw = $request->input('products', []);
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        $products = collect($decoded ?: [])
            ->filter(fn($p) => !empty(data_get($p, 'product_id')))
            ->map(fn($p) => (int) data_get($p, 'product_id'))
            ->unique()
            ->values();

        if ($products->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $out = [];

        foreach ($products as $productId) {
            $mappings = DB::table('product_positions as pp')
                ->leftJoin('departments as d', 'd.id', '=', 'pp.department_id')
                ->leftJoin('phase_sections as s', 's.id', '=', 'pp.service_id')
                ->where('pp.article_group_id', $productId)
                ->select([
                    'pp.article_group_id as product_id',
                    'pp.department_id',
                    'pp.service_id',
                    DB::raw('COALESCE(d.department_name, d.name) as department'),
                    DB::raw('COALESCE(s.phase_section, s.title, s.name) as service'),
                ])
                ->get();

            foreach ($mappings as $pp) {
                $innendienst = [];
                $aussendienst = [];

                if (!empty($pp->department_id)) {
                    $innendienst = DB::table('employees as e')
                        ->join('employee_departments as ed', 'ed.employee_id', '=', 'e.id')
                        ->where('ed.department_id', $pp->department_id)
                        ->select('e.id', 'e.name', 'e.lastname')
                        ->orderBy('e.name')
                        ->get();

                    $aussendienst = DB::table('employees as e')
                        ->join('employee_departments as ed', 'ed.employee_id', '=', 'e.id')
                        ->where('ed.department_id', $pp->department_id)
                        ->select('e.id', 'e.name', 'e.lastname')
                        ->orderBy('e.name')
                        ->get();
                }

                $out[] = [
                    'product_id' => (int) $pp->product_id,
                    'product_name' => DB::table('article_groups')->where('id', $pp->product_id)->value('article_group') ?: "Produkt #{$pp->product_id}",
                    'department_id' => $pp->department_id ? (int) $pp->department_id : null,
                    'department' => $pp->department ?: null,
                    'service_id' => $pp->service_id ? (int) $pp->service_id : null,
                    'service' => $pp->service ?: null,
                    'innendienst_employees' => $innendienst,
                    'aussendienst_employees' => $aussendienst,
                ];
            }
        }

        return response()->json(['data' => $out]);
    }

    public function getEmployee(Request $request)
    {
        $productId = $request->input('product_id');
        $departmentId = $request->input('department_id');
        $serviceId = $request->input('service_id');
        $stage = $request->input('stage', 'inquiry');

        if (!$productId) {
            return response()->json([
                'department_id' => $departmentId,
                'service_id' => $serviceId,
                'internal_employees' => [],
                'external_employees' => [],
                'stage' => $stage,
                'used_fallback' => false,
            ]);
        }

        $loadAllActiveEmployees = function () {
            return DB::table('employees')
                ->whereNull('deleted_at')
                ->where('status', 'Active')
                ->select('id', 'name', 'lastname', 'image')
                ->orderBy('name')
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'lastname' => $e->lastname,
                    'image' => $e->image,
                    'positions' => [],
                ])
                ->values()
                ->all();
        };

        $allRows = DB::table('product_positions')
            ->where('article_group_id', $productId)
            ->where('stage', $stage)
            ->get();

        if ($allRows->isEmpty()) {
            $allRows = DB::table('product_positions')
                ->where('article_group_id', $productId)
                ->get();

            if ($allRows->isEmpty()) {
                $fallback = $loadAllActiveEmployees();

                return response()->json([
                    'department_id' => $departmentId,
                    'service_id' => $serviceId,
                    'internal_employees' => $fallback,
                    'external_employees' => $fallback,
                    'stage' => $stage,
                    'used_fallback' => true,
                ]);
            }
        }

        if (empty($departmentId)) {
            $departmentId = $allRows->pluck('department_id')->filter()->unique()->first();
        }

        if (empty($serviceId)) {
            $serviceId = $allRows->pluck('service_id')->filter()->unique()->first();
        }

        $rows = DB::table('product_positions')
            ->where('article_group_id', $productId)
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
            ->when(
                DB::table('product_positions')->where('article_group_id', $productId)->where('stage', $stage)->exists(),
                fn($q) => $q->where('stage', $stage)
            )
            ->get();

        if ($rows->isEmpty()) {
            $fallback = $loadAllActiveEmployees();

            return response()->json([
                'department_id' => $departmentId,
                'service_id' => $serviceId,
                'internal_employees' => $fallback,
                'external_employees' => $fallback,
                'stage' => $stage,
                'used_fallback' => true,
            ]);
        }

        $internalPosIds = [];
        $externalPosIds = [];

        foreach ($rows as $row) {
            if (!$row->position_ids) {
                continue;
            }

            $decoded = json_decode($row->position_ids, true);
            if (!is_array($decoded)) {
                continue;
            }

            if (array_key_exists('internal', $decoded) || array_key_exists('external', $decoded)) {
                $internalPosIds = array_merge($internalPosIds, is_array($decoded['internal'] ?? null) ? $decoded['internal'] : []);
                $externalPosIds = array_merge($externalPosIds, is_array($decoded['external'] ?? null) ? $decoded['external'] : []);
            } else {
                $internalPosIds = array_merge($internalPosIds, $decoded);
            }
        }

        $internalPosIds = array_values(array_unique(array_filter($internalPosIds)));
        $externalPosIds = array_values(array_unique(array_filter($externalPosIds)));

        $fetchEmployees = function (array $positionIds, ?int $deptId) {
            $q = DB::table('department_positions')
                ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                ->whereNull('employees.deleted_at')
                ->where('employees.status', 'Active');

            if ($deptId) {
                $q->where('department_positions.department_id', $deptId);
            }

            if (!empty($positionIds)) {
                $q->whereIn('department_positions.position_id', $positionIds);
            }

            return $q->select(
                'employees.id as employee_id',
                'employees.name',
                'employees.lastname',
                'employees.image',
                'positions.position'
            )->get()
                ->groupBy('employee_id')
                ->map(function ($items) {
                    $first = $items->first();

                    return [
                        'id' => $first->employee_id,
                        'name' => $first->name,
                        'lastname' => $first->lastname,
                        'image' => $first->image,
                        'positions' => $items->pluck('position')->unique()->values()->toArray(),
                    ];
                })
                ->values()
                ->all();
        };

        $internalEmployees = $fetchEmployees($internalPosIds, $departmentId);
        $externalEmployees = $fetchEmployees($externalPosIds, $departmentId);

        if (empty($internalEmployees) && empty($externalEmployees)) {
            $fallback = $loadAllActiveEmployees();
            $internalEmployees = $fallback;
            $externalEmployees = $fallback;
        }

        return response()->json([
            'department_id' => $departmentId,
            'service_id' => $serviceId,
            'internal_employees' => $internalEmployees,
            'external_employees' => $externalEmployees,
            'stage' => $stage,
            'used_fallback' => false,
        ]);
    }

    public function getCustomerProduct($id)
    {
        $data = DB::table('inquiry_product_lists as ip')
            ->leftJoin('employees as e', 'e.id', '=', 'ip.employee_id')
            ->leftJoin('employees as fe', 'fe.id', '=', 'ip.field_employee')
            ->leftJoin('departments as d', 'd.id', '=', 'ip.department_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'ip.product_id')
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'ip.service_id')
            ->select(
                'ip.id',
                'ip.inquiry_id',
                'ip.appointment_date',
                'ip.status',
                'ip.product_id',
                'ip.service_id',
                'ip.department_id',
                'e.name as in_name',
                'e.lastname as in_lastname',
                'e.image as in_image',
                'fe.name as out_name',
                'fe.lastname as out_lastname',
                'fe.image as out_image',
                'd.department_name',
                'ag.article_group',
                'ps.phase_section'
            )
            ->where('ip.inquiry_id', $id)
            ->get();

        return response()->json($data);
    }

    public function saveProduct(Request $request)
    {
        $data = (isset($request[0]) && is_array($request[0])) ? $request[0] : $request->all();

        $validator = Validator::make($data, [
            'inquiry_id' => ['required', 'integer', 'exists:inquiries,id'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'integer', 'exists:article_groups,id'],
            'service_id' => ['required', 'array', 'min:1'],
            'service_id.*' => ['required', 'integer', 'exists:phase_sections,id'],
            'department_id' => ['required', 'array', 'min:1'],
            'department_id.*' => ['required', 'integer', 'exists:departments,id'],
            'employee_id' => ['required', 'array', 'min:1'],
            'employee_id.*' => ['nullable', 'integer', 'exists:employees,id'],
            'field_employee' => ['nullable', 'array'],
            'field_employee.*' => ['nullable', 'integer', 'exists:employees,id'],
            'appointment_date' => ['nullable', 'array'],
            'appointment_date.*' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::transaction(function () use ($data) {
            foreach ($data['product_id'] as $i => $productId) {
                DB::table('inquiry_product_lists')->insert([
                    'inquiry_id' => (int) $data['inquiry_id'],
                    'product_id' => (int) $productId,
                    'service_id' => (int) ($data['service_id'][$i] ?? null),
                    'department_id' => (int) ($data['department_id'][$i] ?? null),
                    'employee_id' => (int) ($data['employee_id'][$i] ?? null),
                    'field_employee' => isset($data['field_employee'][$i]) ? (int) $data['field_employee'][$i] : null,
                    'appointment_date' => $data['appointment_date'][$i] ?? null,
                    'status' => 'open',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json(['message' => 'Produkte wurden erfolgreich gespeichert.']);
    }

    public function deleteProduct(Request $request)
    {
        $deleted = DB::table('inquiry_product_lists')
            ->where('id', $request->id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => 'Das Produkt wurde gelöscht.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Produkt nicht gefunden oder bereits gelöscht.',
        ], 404);
    }


    public function availability(Request $request)
    {
        $tz = 'Europe/Berlin';
        $raw = $request->input('date');

        $normalize = function ($arr) {
            return collect((array) $arr)
                ->filter(fn($v) => $v !== null && $v !== '' && is_numeric($v))
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values();
        };

        $in = $normalize($request->input('internal_ids', []));
        $out = $normalize($request->input('external_ids', []));
        $all = $in->merge($out)->unique()->values();

        try {
            $anchor = $raw ? Carbon::parse($raw, $tz) : Carbon::now($tz);
        } catch (\Throwable $e) {
            $anchor = Carbon::now($tz);
        }

        $weekStart = $anchor->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $anchor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        if ($all->isEmpty()) {
            return response()->json([
                'events' => [],
                'weekStart' => $weekStart->toIso8601String(),
                'weekEnd' => $weekEnd->toIso8601String(),
            ]);
        }

        $rows = DB::table('main_appointments as a')
            ->leftJoin('employees as e', 'e.id', '=', 'a.created_by')
            ->leftJoin('main_appointment_employees as mae', 'mae.appointment_id', '=', 'a.id')
            ->select([
                'a.id',
                'a.name',
                'a.color',
                'a.start_date',
                'a.end_date',
                'a.start_time',
                'a.end_time',
                'a.status',
                'a.created_by',
                'a.full_address',
                'a.street',
                'a.postcode',
                'a.city',
                'a.phone',
                'a.email',
                'a.note',
                'a.link',
                DB::raw("CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,'')) as creator_name"),
                'mae.employee_id as attendee_id',
            ])
            ->whereNull('a.deleted_at')
            ->where(function ($q) use ($all) {
                $q->whereIn('a.created_by', $all)
                    ->orWhereIn('mae.employee_id', $all);
            })
            ->whereRaw("(DATE(a.start_date) <= ?) AND (DATE(COALESCE(a.end_date, a.start_date)) >= ?)", [
                $weekEnd->toDateString(),
                $weekStart->toDateString(),
            ])
            ->distinct()
            ->get();

        $inSet = $in->flip();
        $outSet = $out->flip();

        $events = [];

        foreach ($rows as $r) {
            $sDate = $r->start_date ?: $weekStart->toDateString();
            $eDate = $r->end_date ?: $r->start_date ?: $sDate;

            $sTime = $r->start_time ?: '08:00:00';
            $eTime = $r->end_time ?: Carbon::parse($sTime, $tz)->addHour()->format('H:i:s');

            $start = Carbon::parse("{$sDate} {$sTime}", $tz);
            $end = Carbon::parse("{$eDate} {$eTime}", $tz);

            if ($start->lt($weekStart)) {
                $start = $weekStart->copy();
            }
            if ($end->gt($weekEnd)) {
                $end = $weekEnd->copy();
            }

            $color = $r->color;

            if (isset($inSet[$r->created_by])) {
                $color = '#93c21c';
            }
            if (isset($outSet[$r->created_by])) {
                $color = '#74b2d4';
            }
            if (!$color && isset($inSet[$r->attendee_id])) {
                $color = '#f6c23e';
            }
            if (!$color && isset($outSet[$r->attendee_id])) {
                $color = '#f6c23e';
            }

            $title = trim(($r->creator_name ? "[{$r->creator_name}] " : '') . ($r->name ?? 'Termin'));

            $events[] = [
                'id' => $r->id,
                'title' => $title,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'color' => $color,
                'extendedProps' => [
                    'employee_id' => $r->created_by,
                    'employee_name' => $r->creator_name,
                    'status' => $r->status,
                    'full_address' => $r->full_address,
                    'street' => $r->street,
                    'postcode' => $r->postcode,
                    'city' => $r->city,
                    'phone' => $r->phone,
                    'email' => $r->email,
                    'note' => $r->note,
                    'link' => $r->link,
                ],
            ];
        }

        return response()->json([
            'events' => $events,
            'weekStart' => $weekStart->toIso8601String(),
            'weekEnd' => $weekEnd->toIso8601String(),
        ]);
    }
    public function updateEmployee(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:employee,field_employee',
                'employee_id' => 'required|exists:employees,id',
            ]);

            $product = InquiryProductList::findOrFail($id);
            $product->{$validated['type']} = $validated['employee_id'];
            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => ucfirst(str_replace('_', ' ', $validated['type'])) . ' wurde erfolgreich aktualisiert.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Update error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Aktualisierung fehlgeschlagen.',
            ], 500);
        }
    }

    public function bulkJunk(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $junkReason = trim((string) $request->input('junk_reason', ''));
        $junkNote = trim((string) $request->input('junk_note', ''));

        if (!count($ids)) {
            return response()->json(['junked' => 0, 'message' => 'Keine IDs übergeben.']);
        }

        if ($junkReason === '') {
            return response()->json(['message' => 'Bitte Junk-Grund angeben.'], 422);
        }

        $junked = Inquiry::whereIn('id', $ids)
            ->whereNull('deleted_at')
            ->update([
                'status' => 'Junk',
                'junk_reason' => $junkReason,
                'junk_note' => $junkNote !== '' ? $junkNote : null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'junked' => $junked,
            'message' => $junked . ' Anfrage(n) als Junk markiert.',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (!count($ids)) {
            return response()->json(['deleted' => 0, 'message' => 'Keine IDs übergeben.']);
        }

        $deleted = Inquiry::whereIn('id', $ids)->delete();

        return response()->json([
            'deleted' => $deleted,
            'message' => $deleted . ' Anfrage(n) gelöscht.',
        ]);
    }

    public function bulkVerify(Request $request)
    {
        $ids = collect((array) $request->input('ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $type = trim((string) $request->input('type', ''));

        if (empty($ids) || $type === '') {
            return response()->json([
                'processed_count' => 0,
                'skipped_count' => 0,
                'message' => 'Es wurden keine IDs oder kein Typ übermittelt.',
            ], 422);
        }

        $inquiries = Inquiry::query()
            ->whereIn('id', $ids)
            ->whereNull('deleted_at')
            ->get();

        $productsByInquiry = InquiryProductList::query()
            ->whereIn('inquiry_id', $ids)
            ->get()
            ->groupBy('inquiry_id');

        $processedCount = 0;
        $skipped = [];
        $createdLeadIds = [];

        foreach ($inquiries as $inquiry) {
            $productRows = collect($productsByInquiry->get($inquiry->id, collect()))->values();

            if ($productRows->isEmpty()) {
                $skipped[] = [
                    'id' => $inquiry->id,
                    'reason' => 'Anfrage hat keine Produkte.',
                ];
                continue;
            }

            $missingEmployeeProduct = $productRows->first(function ($p) {
                return empty($p->employee_id);
            });

            if ($missingEmployeeProduct) {
                $skipped[] = [
                    'id' => $inquiry->id,
                    'reason' => 'Mindestens ein Produkt hat keinen zugewiesenen Mitarbeiter.',
                ];
                continue;
            }

            $inquiry->pre_type = $type;
            $inquiry->save();

            $lowerType = Str::lower($type);

            if (in_array($lowerType, ['kunde', 'lead'], true)) {
                $lead = $this->lead($inquiry, $productRows);

                if ($lead && isset($lead->id)) {
                    $createdLeadIds[] = $lead->id;
                }
            } else {
                switch ($type) {
                    case 'Lieferant':
                        $this->distributor($inquiry);
                        break;
                    case 'Hersteller':
                        $this->brand($inquiry, 'brand');
                        break;
                    case 'Architekt':
                        $this->brand($inquiry, 'architect');
                        break;
                    case 'Bank':
                        $this->brand($inquiry, 'bank');
                        break;
                    case 'Nachunternehmer':
                        $this->brand($inquiry, 'sub_contractor');
                        break;
                    case 'Versicherung':
                        $this->brand($inquiry, 'insurance');
                        break;
                    case 'Bewerber':
                        $this->updateVerification($inquiry);
                        break;
                    default:
                        $this->others($inquiry, $type);
                        break;
                }
            }

            $processedCount++;
        }

        return response()->json([
            'success' => true,
            'processed_count' => $processedCount,
            'skipped_count' => count($skipped),
            'skipped' => $skipped,
            'created_lead_ids' => array_values(array_unique($createdLeadIds)),
            'message' => $processedCount . ' Anfrage(n) verifiziert.',
        ]);
    }

    public function startDraft()
    {
        $employeeId = (int) auth()->user()->name;
        $branchId = (int) (Branch::orderBy('id')->value('id') ?? 0);

        if (!$employeeId || !$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Standard-Mitarbeiter oder Niederlassung fehlen.',
            ], 422);
        }

        $inq = new Inquiry();
        $inq->contact_person = $employeeId;
        $inq->branch_id = $branchId;
        $inq->status = 'Draft';

        if (Schema::hasColumn('inquiries', 'is_draft')) {
            $inq->is_draft = true;
            $inq->draft_uuid = (string) Str::uuid();
        }

        $inq->save();

        return response()->json([
            'success' => true,
            'id' => $inq->id,
            'edit_url' => route('inquiry.edit', $inq->id),
        ]);
    }

    public function autosave(Request $request, $id)
    {
        if ((int) $id === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry-ID fehlt. Bitte zuerst einen Entwurf starten.',
            ], 422);
        }

        $inquiry = Inquiry::find($id);

        if (!$inquiry) {
            return response()->json(['success' => false, 'message' => 'Anfrage nicht gefunden'], 404);
        }

        $this->assertCanEditInquiry($inquiry);

        $validated = $request->validate([
            'pre_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'type_extra' => ['sometimes', 'nullable', 'string', 'max:255'],
            'firma' => ['sometimes', 'nullable', 'string', 'max:255'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'lastname' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'street' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postcode' => ['sometimes', 'nullable', 'string', 'max:50'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric'],
            'longitude' => ['sometimes', 'nullable', 'numeric'],
            'elevation' => ['sometimes', 'nullable', 'numeric'],
            'description' => ['sometimes', 'nullable', 'string'],
            'periority' => ['sometimes', 'nullable', 'string', 'max:255'],
            'branch_id' => ['sometimes', 'integer', 'exists:branches,id'],
            'department_id' => ['sometimes', 'nullable', 'integer', 'exists:departments,id'],
            'direct_to' => ['sometimes', 'nullable', 'integer', 'exists:employees,id'],
        ]);

        if (array_key_exists('description', $validated)) {
            $validated['note'] = $validated['description'];
            unset($validated['description']);
        }

        if (
            array_key_exists('street', $validated) ||
            array_key_exists('postcode', $validated) ||
            array_key_exists('city', $validated)
        ) {
            $street = $validated['street'] ?? $inquiry->street;
            $postcode = $validated['postcode'] ?? $inquiry->postcode;
            $city = $validated['city'] ?? $inquiry->city;
            $validated['full_address'] = trim(($street ?? '') . ' ' . ($postcode ?? '') . ' ' . ($city ?? ''));
        }

        $inquiry->fill($validated);

        if ($inquiry->status === 'Draft' || !$this->isDraftColumnPresent()) {
            $inquiry->status = 'Draft';
        }

        if ($this->isDraftColumnPresent()) {
            $inquiry->is_draft = true;
            $inquiry->last_autosaved_at = now();
        }

        $inquiry->save();

        return response()->json([
            'success' => true,
            'id' => $inquiry->id,
            'saved_at' => now()->toDateTimeString(),
        ]);
    }

    public function autosaveProducts(Request $request, Inquiry $inquiry)
    {
        $this->assertCanEditInquiry($inquiry);

        $payload = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['nullable', 'integer', 'exists:inquiry_product_lists,id'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'items.*.service_id' => ['nullable', 'integer', 'exists:phase_sections,id'],
            'items.*.department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'items.*.employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'items.*.field_employee' => ['nullable', 'integer', 'exists:employees,id'],
            'items.*.appointment_date' => ['nullable', 'date'],
            'items.*.status' => ['nullable', 'string', 'max:50'],
        ]);

        $items = collect($payload['items']);

        DB::transaction(function () use ($inquiry, $items) {
            $existingIds = InquiryProductList::where('inquiry_id', $inquiry->id)->pluck('id')->all();
            $keepIds = $items->pluck('id')->filter()->map(fn($v) => (int) $v)->all();
            $toDelete = array_values(array_diff($existingIds, $keepIds));

            if (!empty($toDelete)) {
                InquiryProductList::where('inquiry_id', $inquiry->id)
                    ->whereIn('id', $toDelete)
                    ->delete();
            }

            foreach ($items as $row) {
                $data = [
                    'inquiry_id' => $inquiry->id,
                    'product_id' => $row['product_id'] ?? null,
                    'service_id' => $row['service_id'] ?? null,
                    'department_id' => $row['department_id'] ?? null,
                    'employee_id' => $row['employee_id'] ?? null,
                    'field_employee' => $row['field_employee'] ?? null,
                    'appointment_date' => $row['appointment_date'] ?? null,
                    'status' => $row['status'] ?? 'open',
                ];

                if (!empty($row['id'])) {
                    InquiryProductList::where('inquiry_id', $inquiry->id)
                        ->where('id', (int) $row['id'])
                        ->update($data);
                } else {
                    InquiryProductList::create($data);
                }
            }

            if ($this->isDraftColumnPresent()) {
                $inquiry->last_autosaved_at = now();
                $inquiry->save();
            }
        });

        return response()->json(['success' => true]);
    }

    public function discardDraft(Request $request, Inquiry $inquiry)
    {
        $this->assertCanEditInquiry($inquiry);

        DB::transaction(function () use ($inquiry) {
            InquiryProductList::where('inquiry_id', $inquiry->id)->delete();

            DB::table('main_appointments')
                ->where('appointment_type', 'inquiry')
                ->where('other_id', $inquiry->id)
                ->delete();

            $inquiry->delete();
        });

        return response()->json(['success' => true]);
    }

    public function finalizeDraft(Request $request, Inquiry $inquiry)
    {
        $this->assertCanEditInquiry($inquiry);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'next_step' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $inquiry->fill($validated);

        if ($this->isDraftColumnPresent()) {
            $inquiry->is_draft = false;
        }

        $inquiry->status = 'Unpublished';
        $inquiry->save();

        return response()->json([
            'success' => true,
            'redirect_url' => route('inquiry.show.profile', $inquiry->id),
        ]);
    }

    private function assertCanEditInquiry(Inquiry $inquiry): void
    {
        $employeeId = (int) auth()->user()->name;

        if ((int) $inquiry->contact_person !== $employeeId) {
            abort(403, 'Forbidden');
        }
    }

    private function isDraftColumnPresent(): bool
    {
        return Schema::hasColumn('inquiries', 'is_draft');
    }

    private function distributor(Inquiry $data)
    {
        $dis = Distributor::firstOrCreate(
            ['name' => $data->firma ?? 'Lieferant-' . mt_rand(10000, 99999)],
            ['address' => ($data->street . ',' . $data->postcode . ',' . $data->city) ?? 'unknown', 'status' => 'Unpublished']
        );

        if ($dis && $dis->id) {
            $this->createDistributorDepartment($dis->id, $data);
            $this->updateVerification($data);
        }

        return $dis;
    }

    private function brand(Inquiry $data, string $brandType)
    {
        $brand = Brand::firstOrCreate(
            ['name' => $data->firma ?? 'Unternehmen-' . mt_rand(10000, 99999)],
            ['address' => ($data->street . ',' . $data->postcode . ',' . $data->city) ?? 'unknown', 'status' => 'Unpublished', 'type' => $brandType]
        );

        if ($brand && $brand->id) {
            $this->createBrandDepartment($brand->id, $data);
            $this->updateVerification($data);
        }

        return $brand;
    }

    private function others(Inquiry $data, string $type)
    {
        return $this->brand($data, $type);
    }

    private function createDistributorDepartment(int $distributorId, Inquiry $data)
    {
        $fullName = trim($data->name . ' ' . $data->lastname);

        if (!$fullName) {
            return;
        }

        $exists = DistributorDepartment::where('d_id', $distributorId)
            ->where('name', $fullName)
            ->exists();

        if (!$exists) {
            DistributorDepartment::create([
                'd_id' => $distributorId,
                'd_department' => 'Abteilung ist nicht definiert',
                'name' => $fullName,
                'position' => 'unknown',
                'phone' => $data->phone,
                'email' => $data->email,
                'office' => $data->telephone,
            ]);
        }
    }

    private function createBrandDepartment(int $brandId, Inquiry $data)
    {
        $fullName = trim($data->name . ' ' . $data->lastname);

        if (!$fullName) {
            return;
        }

        $exists = BrandDepartment::where('brand_id', $brandId)
            ->where('name', $fullName)
            ->exists();

        if (!$exists) {
            BrandDepartment::create([
                'brand_id' => $brandId,
                'brand_department' => 'Abteilung-' . mt_rand(10000, 99999),
                'name' => $fullName,
                'position' => 'unknown',
                'phone' => $data->phone,
                'email' => $data->email,
                'office' => $data->telephone,
                'status' => 'Unpublished',
            ]);
        }
    }

    private function updateVerification(Inquiry $data)
    {
        $data->verify_by = auth()->user()->name;
        $data->verify_date = now();
        $data->status = 'Published';
        $data->save();

        return $data;
    }

    private function lead(Inquiry $data, $productRows = null)
    {
        $year = date('Y');
        $customerNo = $year . '-' . mt_rand(10000, 999999);

        $street = trim(Str::lower($data->street ?? ''));
        $city = trim(Str::lower($data->city ?? ''));
        $postcode = trim($data->postcode ?? '');
        $name = trim(Str::lower($data->name ?? ''));
        $lastname = trim(Str::lower($data->lastname ?? ''));

        $existing = NewLeads::whereRaw('LOWER(name) = ?', [$name])
            ->whereRaw('LOWER(lastname) = ?', [$lastname])
            ->whereRaw('LOWER(street) = ?', [$street])
            ->whereRaw('LOWER(city) = ?', [$city])
            ->where('postcode', $postcode)
            ->first();

        if ($existing) {
            $this->updateVerification($data);
            return $existing;
        }

        $employeeId = $data->contact_person ?? null;
        $branchId = $data->branch_id ?? null;

        $lead = NewLeads::create([
            'customer_type' => $data->pre_type,
            'firma' => $data->firma,
            'lastname' => $data->lastname,
            'name' => $data->name,
            'full_address' => trim("{$data->street} {$data->postcode} {$data->city}"),
            'street' => $data->street,
            'postcode' => $data->postcode,
            'city' => $data->city,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'elevation' => $data->elevation,
            'phone' => $data->phone,
            'telephone' => $data->telephone,
            'email' => $data->email,
            'status' => 'Lead',
            'source' => 'Anfrage',
            'info' => $data->note,
            'contact_person' => $employeeId,
            'branch' => $branchId,
            'customer_no' => $customerNo,
        ]);

        $alt = LeadAlternativeAdd::create([
            'lead_id' => $lead->id,
            'object_name' => 'Privathaus',
            'full_address' => $lead->full_address,
            'street' => $lead->street,
            'postcode' => $lead->postcode,
            'city' => $lead->city,
            'lat' => $lead->latitude,
            'lon' => $lead->longitude,
            'elevation' => $lead->elevation,
            'note' => $data->note,
            'main' => 1,
            'address_no' => 1,
            'stage' => 'lead',
            'periority' => 'Normal',
        ]);

        $productRows = collect($productRows ?? InquiryProductList::query()
            ->where('inquiry_id', $data->id)
            ->get())
            ->filter(fn($product) => !empty($product->product_id))
            ->values();

        foreach ($productRows as $product) {
            LeadProductList::create([
                'customer_id' => $lead->id,
                'alternative_id' => $alt->id,
                'product_id' => $product->product_id,
                'service_id' => $product->service_id ?? null,
                'department_id' => $product->department_id ?? null,
                'employee_id' => $product->employee_id ?? null,
                'field_employee' => $product->field_employee ?? null,
            ]);
        }

        $this->updateVerification($data);

        return $lead;
    }

    public function markAsRead($id): JsonResponse
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($notification->read_at) {
            return response()->json(['message' => 'Benachrichtigung bereits als gelesen markiert'], 200);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Benachrichtigung als gelesen markiert'], 200);
    }

    public function getNotification($id)
    {
        $notifications = Auth::user()->notifications()
            ->where('type', InquiryNotification::class)
            ->where('data->lead_id', $id)
            ->latest()
            ->get();

        return view('admin.inquiry.notification', compact('notifications'));
    }

    public function getTaskNotifications(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Nicht autorisiert'], 401);
        }

        $notifications = DatabaseNotification::whereNull('read_at')
            ->where('data->to', (int) $user->name)
            ->where('data->type', 'inquiry')
            ->orderBy('created_at', 'desc')
            ->get();

        $transformed = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Benachrichtigung',
                'message' => $notification->data['message'] ?? '',
                'lead_id' => $notification->data['lead_id'] ?? null,
                'type' => $notification->data['type'] ?? null,
                'to' => $notification->data['to'] ?? null,
                'performed_at' => isset($notification->data['performed_at'])
                    ? Carbon::parse($notification->data['performed_at'])->toDateTimeString()
                    : $notification->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $transformed]);
    }

    public function appointmentSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:240'],
        ]);

        $tz = 'Europe/Berlin';

        $employeeId = (int) $validated['employee_id'];
        $duration = (int) ($validated['duration_minutes'] ?? 60);

        $from = !empty($validated['date_from'])
            ? Carbon::parse($validated['date_from'], $tz)->startOfDay()
            : Carbon::now($tz)->startOfDay();

        $to = !empty($validated['date_to'])
            ? Carbon::parse($validated['date_to'], $tz)->endOfDay()
            : $from->copy()->addDays(13)->endOfDay();

        $busyAppointments = DB::table('main_appointments as a')
            ->leftJoin('main_appointment_employees as mae', 'mae.appointment_id', '=', 'a.id')
            ->select([
                'a.id',
                'a.name',
                'a.start_date',
                'a.end_date',
                'a.start_time',
                'a.end_time',
                'a.status',
                'a.created_by',
                'mae.employee_id as attendee_id',
            ])
            ->whereNull('a.deleted_at')
            ->where(function ($q) use ($employeeId) {
                $q->where('a.created_by', $employeeId)
                    ->orWhere('mae.employee_id', $employeeId);
            })
            ->whereRaw("(DATE(a.start_date) <= ?) AND (DATE(COALESCE(a.end_date, a.start_date)) >= ?)", [
                $to->toDateString(),
                $from->toDateString(),
            ])
            ->distinct()
            ->get()
            ->map(function ($row) use ($tz) {
                $startDate = $row->start_date
                    ? Carbon::parse($row->start_date, $tz)->format('Y-m-d')
                    : null;

                $endDate = $row->end_date
                    ? Carbon::parse($row->end_date, $tz)->format('Y-m-d')
                    : $startDate;

                $startTime = $row->start_time ?: '08:00:00';
                $endTime = $row->end_time ?: Carbon::parse($startTime, $tz)->addHour()->format('H:i:s');

                return [
                    'id' => $row->id,
                    'name' => $row->name ?: 'Termin',
                    'start' => Carbon::parse($startDate . ' ' . $startTime, $tz),
                    'end' => Carbon::parse($endDate . ' ' . $endTime, $tz),
                    'status' => $row->status,
                ];
            });

        $days = [];
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $dayStart = $cursor->copy()->setTime(8, 0);
            $dayEnd = $cursor->copy()->setTime(17, 0);

            // Skip Sunday. Remove this block if Sunday should be available.
            if ($cursor->isSunday()) {
                $days[] = [
                    'date' => $cursor->toDateString(),
                    'label' => $cursor->translatedFormat('l, d.m.Y'),
                    'slots' => [],
                ];

                $cursor->addDay();
                continue;
            }

            $slots = [];
            $slot = $dayStart->copy();

            while ($slot->copy()->addMinutes($duration)->lte($dayEnd)) {
                $slotEnd = $slot->copy()->addMinutes($duration);

                $conflict = $busyAppointments->first(function ($busy) use ($slot, $slotEnd) {
                    return $slot->lt($busy['end']) && $slotEnd->gt($busy['start']);
                });

                $slots[] = [
                    'value' => $slot->format('Y-m-d\TH:i'),
                    'date' => $slot->toDateString(),
                    'time' => $slot->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'available' => !$conflict,
                    'busy_title' => $conflict['name'] ?? null,
                ];

                $slot->addMinutes($duration);
            }

            $days[] = [
                'date' => $cursor->toDateString(),
                'label' => $cursor->translatedFormat('l, d.m.Y'),
                'slots' => $slots,
            ];

            $cursor->addDay();
        }

        $employee = Employee::select('id', 'name', 'lastname')
            ->where('id', $employeeId)
            ->first();

        return response()->json([
            'success' => true,
            'employee' => $employee ? [
                'id' => $employee->id,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
            ] : null,
            'days' => $days,
        ]);
    }
}
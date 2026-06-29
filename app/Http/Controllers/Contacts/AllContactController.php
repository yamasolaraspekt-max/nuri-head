<?php

namespace App\Http\Controllers\Contacts;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Distributor;
use App\Models\Employee;
use App\Models\Inquiry;
use App\Models\MainAppointment;
use App\Models\NewLeads;
use App\Models\PersonalTask;
use App\Models\Problem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AllContactController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim((string) $request->input('search', ''));
        $typeFilter = $request->input('type');
        $sort       = $request->input('sort', 'name');
        $direction  = strtolower((string) $request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage    = 20;

        $all = $this->getAllContacts($request);

        if ($typeFilter) {
            $all = $all->filter(fn ($row) => (string) ($row->type ?? '') === (string) $typeFilter)->values();
        }

        $all = $this->appendCustomerMeta($all);
        $all = $this->sortContacts($all, $sort, $direction);

        $page   = max((int) $request->input('page', 1), 1);
        $total  = $all->count();
        $sliced = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $contacts = new LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.contacts.contacts', compact('contacts', 'search', 'typeFilter', 'sort', 'direction'));
    }

    private function getAllContacts(Request $request): Collection
    {
        $searchTerms = $this->splitSearchTerms($request->input('search'));
        $all = collect();

        $all = $all->merge($this->queryCustomers($searchTerms, false)->get());
        $all = $all->merge($this->queryBrands($searchTerms)->get());
        $all = $all->merge($this->queryDistributors($searchTerms)->get());
        $all = $all->merge($this->queryInquiries($searchTerms, false)->get());
        $all = $all->merge($this->queryEmployees($searchTerms)->get());

        return $all->values();
    }

    public function export(Request $request)
    {
        $filename = 'all_contacts_' . now()->format('Ymd_His') . '.csv';
        $contacts = $this->getAllContacts($request);

        $headers = [
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate',
        ];

        $callback = function () use ($contacts) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Name', 'Nachname', 'Typ', 'Telefon', 'Email', 'Adresse']);

            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->name ?? '',
                    $contact->lastname ?? '',
                    $contact->type ?? '',
                    $contact->phone ?? '',
                    $contact->email ?? '',
                    $contact->address ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function splitSearchTerms(?string $search): array
    {
        $search = trim((string) $search);

        if ($search === '') {
            return [];
        }

        return array_values(array_filter(
            preg_split('/[\s,]+/u', $search, -1, PREG_SPLIT_NO_EMPTY),
            fn ($term) => trim((string) $term) !== ''
        ));
    }

    protected function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    protected function normalizedPhoneSql(string $column): string
    {
        return "
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            COALESCE({$column}, ''),
                                        ' ', ''),
                                    '-', ''),
                                '/', ''),
                            '(', ''),
                        ')', ''),
                    '+', ''),
                '.', ''),
            '\\\\', '')
        ";
    }

    protected function applyFlexibleSearch(
        $query,
        array $searchTerms,
        array $columns = [],
        array $phoneColumns = [],
        ?callable $extraTermCondition = null
    ) {
        if (empty($searchTerms)) {
            return $query;
        }

        foreach ($searchTerms as $term) {
            $term = trim((string) $term);
            $digits = $this->normalizeDigits($term);

            $query->where(function ($q) use ($columns, $phoneColumns, $extraTermCondition, $term, $digits) {
                $hasCondition = false;

                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', '%' . $term . '%');
                    $hasCondition = true;
                }

                if ($digits !== '') {
                    foreach ($phoneColumns as $phoneColumn) {
                        $q->orWhereRaw($this->normalizedPhoneSql($phoneColumn) . ' LIKE ?', ['%' . $digits . '%']);
                        $hasCondition = true;
                    }
                }

                if ($extraTermCondition) {
                    $extraTermCondition($q, $term, $digits);
                    $hasCondition = true;
                }

                if (!$hasCondition) {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        return $query;
    }

    protected function queryCustomers(array $searchTerms, bool $withTrashed = false)
    {
        $query = DB::table('new_leads')
            ->select(
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                DB::raw("'Kunde' as type"),
                'new_leads.phone',
                'new_leads.email',
                'new_leads.full_address as address',
                'new_leads.total_purchase',
                'new_leads.deleted_at'
            );

        if (!$withTrashed) {
            $query->whereNull('new_leads.deleted_at');
        }

        return $this->applyFlexibleSearch(
            $query,
            $searchTerms,
            [
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.email',
                'new_leads.full_address',
            ],
            [
                'new_leads.phone',
            ],
            function ($q, $term, $digits) {
                // lead_alternative_adds only stores address data (no email/phone
                // columns), so match the term against the address-like columns
                // that actually exist to avoid "Unknown column" SQL errors.
                $q->orWhereExists(function ($sub) use ($term) {
                    $sub->select(DB::raw(1))
                        ->from('lead_alternative_adds')
                        ->whereColumn('lead_alternative_adds.lead_id', 'new_leads.id')
                        ->where(function ($sq) use ($term) {
                            $sq->where('lead_alternative_adds.full_address', 'like', '%' . $term . '%')
                               ->orWhere('lead_alternative_adds.street', 'like', '%' . $term . '%')
                               ->orWhere('lead_alternative_adds.city', 'like', '%' . $term . '%')
                               ->orWhere('lead_alternative_adds.object_name', 'like', '%' . $term . '%');
                        });
                });
            }
        );
    }

    protected function queryBrands(array $searchTerms)
    {
        $query = DB::table('brand_departments')
            ->select(
                'brand_departments.id',
                'brand_departments.name',
                DB::raw("'' as lastname"),
                DB::raw("'Hersteller' as type"),
                'brand_departments.phone',
                'brand_departments.email',
                DB::raw("'' as address"),
                DB::raw('NULL as deleted_at')
            );

        return $this->applyFlexibleSearch(
            $query,
            $searchTerms,
            [
                'brand_departments.name',
                'brand_departments.email',
            ],
            [
                'brand_departments.phone',
            ]
        );
    }

    protected function queryDistributors(array $searchTerms)
    {
        $query = DB::table('distributor_departments')
            ->select(
                'distributor_departments.id',
                'distributor_departments.name',
                DB::raw("'' as lastname"),
                DB::raw("'Lieferant' as type"),
                'distributor_departments.phone',
                'distributor_departments.email',
                DB::raw("'' as address"),
                DB::raw('NULL as deleted_at')
            );

        return $this->applyFlexibleSearch(
            $query,
            $searchTerms,
            [
                'distributor_departments.name',
                'distributor_departments.email',
            ],
            [
                'distributor_departments.phone',
            ]
        );
    }

    protected function queryInquiries(array $searchTerms, bool $withTrashed = false)
    {
        $query = DB::table('inquiries')
            ->select(
                'inquiries.id',
                'inquiries.name',
                'inquiries.lastname',
                DB::raw("'Anfrage' as type"),
                'inquiries.phone',
                'inquiries.email',
                'inquiries.full_address as address',
                'inquiries.deleted_at'
            );

        if (!$withTrashed) {
            $query->whereNull('inquiries.deleted_at');
        }

        return $this->applyFlexibleSearch(
            $query,
            $searchTerms,
            [
                'inquiries.name',
                'inquiries.lastname',
                'inquiries.email',
                'inquiries.full_address',
            ],
            [
                'inquiries.phone',
            ]
        );
    }

    protected function queryEmployees(array $searchTerms)
    {
        $query = DB::table('employees')
            ->leftJoin('employee_addresses', 'employees.id', '=', 'employee_addresses.emp_id')
            ->select(
                'employees.id',
                'employees.name',
                'employees.lastname',
                DB::raw("'Mitarbeiter' as type"),
                'employees.phone',
                'employees.email',
                'employee_addresses.address_name as address',
                DB::raw('NULL as deleted_at')
            );

        return $this->applyFlexibleSearch(
            $query,
            $searchTerms,
            [
                'employees.name',
                'employees.lastname',
                'employees.email',
                'employee_addresses.address_name',
            ],
            [
                'employees.phone',
            ]
        );
    }

    protected function appendCustomerMeta(Collection $rows): Collection
    {
        $customerIds = $rows
            ->filter(fn ($row) => in_array(strtolower((string) ($row->type ?? '')), ['kunde', 'customer'], true))
            ->pluck('id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $dealAgg = collect();

        if (!empty($customerIds)) {
            $dealAgg = DB::table('deals')
                ->select(
                    'customer_id',
                    DB::raw("MAX(CASE WHEN LOWER(status) IN ('open','pending') THEN 1 ELSE 0 END) AS has_open"),
                    DB::raw("MAX(CASE WHEN LOWER(status) IN ('junk','canceled','cancel','abgesagt','storniert') THEN 1 ELSE 0 END) AS has_junk"),
                    DB::raw("MAX(CASE WHEN LOWER(status) IN ('won','closed_won') THEN 1 ELSE 0 END) AS has_won")
                )
                ->whereIn('customer_id', $customerIds)
                ->groupBy('customer_id')
                ->get()
                ->keyBy('customer_id');
        }

        return $rows->map(function ($row) use ($dealAgg) {
            $row->purchase_status       = null;
            $row->purchase_status_label = null;
            $row->purchase_status_badge = null;
            $row->tier                  = 'none';
            $row->tier_label            = '—';
            $row->tier_icon             = null;

            if (!in_array(strtolower((string) ($row->type ?? '')), ['kunde', 'customer'], true)) {
                return $row;
            }

            $flags   = $dealAgg->get($row->id);
            $hasOpen = (int) ($flags->has_open ?? 0) === 1;
            $hasJunk = (int) ($flags->has_junk ?? 0) === 1;
            $hasWon  = (int) ($flags->has_won ?? 0) === 1;

            $totalPurchase = (float) ($row->total_purchase ?? 0);

            if ($hasOpen) {
                $row->purchase_status = 'new_customer';
            } elseif ($hasJunk) {
                $row->purchase_status = 'declined';
            } elseif ($totalPurchase > 0 || $hasWon) {
                $row->purchase_status = 'customer';
            } else {
                $row->purchase_status = 'prospect';
            }

            $row->purchase_status_label = match ($row->purchase_status) {
                'new_customer' => 'Neukunde (offen)',
                'declined'     => 'Abgelehnt',
                'customer'     => 'Bestandskunde',
                'prospect'     => 'Interessent',
                default        => '—',
            };

            $row->purchase_status_badge = match ($row->purchase_status) {
                'new_customer' => 'badge-info',
                'declined'     => 'badge-danger',
                'customer'     => 'badge-success',
                'prospect'     => 'badge-warning',
                default        => 'badge-secondary',
            };

            $row->tier = match (true) {
                $totalPurchase < 5000   => 'bronze',
                $totalPurchase < 15000  => 'silver',
                $totalPurchase < 50000  => 'gold',
                $totalPurchase >= 50000 => 'platinum',
                default                 => 'none',
            };

            $row->tier_label = match ($row->tier) {
                'platinum' => 'Platinum',
                'gold'     => 'Gold',
                'silver'   => 'Silber',
                'bronze'   => 'Bronze',
                default    => '—',
            };

            $row->tier_icon = $row->tier !== 'none'
                ? asset('icons/' . $row->tier . '.png')
                : null;

            return $row;
        })->values();
    }

    protected function sortContacts(Collection $rows, string $sort, string $direction): Collection
    {
        return $rows->sortBy(function ($row) use ($sort) {
            return match ($sort) {
                'purchase_status' => (string) ($row->purchase_status ?? ''),
                'tier'            => (string) ($row->tier ?? ''),
                'total_purchase'  => (float) ($row->total_purchase ?? 0),
                'lastname'        => (string) ($row->lastname ?? ''),
                'type'            => (string) ($row->type ?? ''),
                'email'           => (string) ($row->email ?? ''),
                'phone'           => (string) ($row->phone ?? ''),
                'address'         => (string) ($row->address ?? ''),
                default           => (string) ($row->name ?? ''),
            };
        }, SORT_REGULAR, $direction === 'desc')->values();
    }

    public function globalSearch(Request $request)
    {
        $raw = trim((string) $request->input('q', ''));

        if ($raw === '') {
            return response()->json([]);
        }

        $tokens = preg_split('/[\s,]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);

        $typeAliases = [
            'kunde'        => 'Kunde',
            'kunden'       => 'Kunde',
            'anfrage'      => 'Anfrage',
            'anfragen'     => 'Anfrage',
            'lieferant'    => 'Lieferant',
            'lieferanten'  => 'Lieferant',
            'liferant'     => 'Lieferant',
            'liferange'    => 'Lieferant',
            'hersteller'   => 'Hersteller',
            'herstellern'  => 'Hersteller',
            'mitarbeiter'  => 'Mitarbeiter',
            'mitarbeitern' => 'Mitarbeiter',
            'employee'     => 'Mitarbeiter',
            'personal'     => 'Mitarbeiter',
            'termin'       => 'Termin',
            'termine'      => 'Termin',
            'appointment'  => 'Termin',
            'aufgabe'      => 'Aufgabe',
            'aufgaben'     => 'Aufgabe',
            'task'         => 'Aufgabe',
            'ticket'       => 'Ticket',
            'tickets'      => 'Ticket',
            'störung'      => 'Ticket',
            'stoerung'     => 'Ticket',
        ];

        $filters = [];
        $searchTerms = [];

        foreach ($tokens as $token) {
            $key = mb_strtolower(trim((string) $token), 'UTF-8');

            if (isset($typeAliases[$key])) {
                $filters[$typeAliases[$key]] = true;
            } else {
                $searchTerms[] = trim((string) $token);
            }
        }

        $searchTerms = array_values(array_filter($searchTerms, fn ($value) => $value !== ''));

        if (empty($searchTerms)) {
            return response()->json([]);
        }

        $hasExplicitFilters = !empty($filters);

        $want = function (string $type) use ($filters, $hasExplicitFilters): bool {
            // Anfrage should NOT be searched in normal global search.
            // It should only appear when user types: Anfrage / Anfragen
            if ($type === 'Anfrage') {
                return isset($filters['Anfrage']);
            }

            return !$hasExplicitFilters || isset($filters[$type]);
        };

        $results = collect();

        if ($want('Kunde')) {
            $kundenQuery = NewLeads::withTrashed()
                ->with([
                    'contactPerson:id,name,lastname,image',
                    'leadProductLists.employee:id,name,lastname,image',
                    'leadProductLists.fieldEmployee:id,name,lastname,image',
                ])
                ->select('id', 'name', 'lastname', 'firma', 'customer_no', 'phone', 'email', 'full_address', 'deleted_at');

            $this->applyFlexibleSearch(
                $kundenQuery,
                $searchTerms,
                ['name', 'lastname', 'firma', 'customer_no', 'email', 'full_address'],
                ['phone'],
                 
            );

            $kunden = $kundenQuery
                ->limit(20)
                ->get()
                ->map(function (NewLeads $row) {
                    $row->type = 'Kunde';
                    $row->address = $row->full_address ?? null;

                    $employees = collect();

                    if ($row->contactPerson) {
                        $employees->push($row->contactPerson);
                    }

                    foreach ($row->leadProductLists as $leadProductList) {
                        if ($leadProductList->employee) {
                            $employees->push($leadProductList->employee);
                        }

                        if ($leadProductList->fieldEmployee) {
                            $employees->push($leadProductList->fieldEmployee);
                        }
                    }

                    $employees = $employees->unique('id')->values();

                    $row->assigned_employees = $employees
                        ->map(fn (Employee $employee) => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')))
                        ->filter()
                        ->implode(', ');

                    $row->participants = $employees
                        ->map(function (Employee $employee) {
                            return [
                                'id'     => $employee->id,
                                'name'   => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                                'avatar' => $employee->image
                                    ? asset('images/employee/' . $employee->image)
                                    : asset('images/gender/male.png'),
                            ];
                        })
                        ->values();

                    return $row;
                });

            $results = $results->merge($kunden);
        }

        if ($want('Anfrage')) {
            $anfragenQuery = Inquiry::withTrashed()
                ->select('id', 'name', 'lastname', 'phone', 'email', 'full_address', 'deleted_at', 'verify_by', 'verify_date');

            $this->applyFlexibleSearch(
                $anfragenQuery,
                $searchTerms,
                ['name', 'lastname', 'email', 'full_address'],
                ['phone']
            );

            $anfragen = $anfragenQuery
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->type           = 'Anfrage';
                    $row->address        = $row->full_address ?? null;
                    $row->verified_label = ($row->verify_by && $row->verify_date) ? 'Verifiziert' : null;
                    $row->participants   = [];
                    return $row;
                });

            $results = $results->merge($anfragen);
        }

        if ($want('Hersteller')) {
            $herstellerQuery = DB::table('brand_departments')
                ->select(
                    'brand_departments.id',
                    'brand_departments.name',
                    DB::raw("'' as lastname"),
                    'brand_departments.phone',
                    'brand_departments.email',
                    DB::raw("'' as address"),
                    DB::raw('NULL as deleted_at'),
                    DB::raw('NULL as verify_by'),
                    DB::raw('NULL as verify_date')
                );

            $this->applyFlexibleSearch(
                $herstellerQuery,
                $searchTerms,
                ['brand_departments.name', 'brand_departments.email'],
                ['brand_departments.phone']
            );

            $hersteller = $herstellerQuery
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->type         = 'Hersteller';
                    $row->participants = [];
                    return $row;
                });

            $results = $results->merge($hersteller);
        }

        if ($want('Lieferant')) {
            $lieferantenQuery = DB::table('distributor_departments')
                ->select(
                    'distributor_departments.id',
                    'distributor_departments.name',
                    DB::raw("'' as lastname"),
                    'distributor_departments.phone',
                    'distributor_departments.email',
                    DB::raw("'' as address"),
                    DB::raw('NULL as deleted_at'),
                    DB::raw('NULL as verify_by'),
                    DB::raw('NULL as verify_date')
                );

            $this->applyFlexibleSearch(
                $lieferantenQuery,
                $searchTerms,
                ['distributor_departments.name', 'distributor_departments.email'],
                ['distributor_departments.phone']
            );

            $lieferanten = $lieferantenQuery
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->type         = 'Lieferant';
                    $row->participants = [];
                    return $row;
                });

            $results = $results->merge($lieferanten);
        }

        if ($want('Mitarbeiter')) {
            $mitarbeiterQuery = DB::table('employees')
                ->leftJoin('employee_addresses', 'employees.id', '=', 'employee_addresses.emp_id')
                ->select(
                    'employees.id',
                    'employees.name',
                    'employees.lastname',
                    'employees.phone',
                    'employees.email',
                    'employees.image',
                    'employee_addresses.address_name as address',
                    DB::raw('NULL as deleted_at'),
                    DB::raw('NULL as verify_by'),
                    DB::raw('NULL as verify_date')
                );

            $this->applyFlexibleSearch(
                $mitarbeiterQuery,
                $searchTerms,
                ['employees.name', 'employees.lastname', 'employees.email', 'employee_addresses.address_name'],
                ['employees.phone']
            );

            $mitarbeiter = $mitarbeiterQuery
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->type         = 'Mitarbeiter';
                    $row->participants = [];
                    return $row;
                });

            $results = $results->merge($mitarbeiter);
        }

        if ($want('Termin')) {
            $appointmentsQuery = MainAppointment::with([
                'employees:id,name,lastname,image',
                'createdBy:id,name,lastname',
                'customer:id,name,lastname,firma,phone,email',
            ])->select('id', 'name', 'priority', 'start_date', 'end_date');

            foreach ($searchTerms as $term) {
                $digits = $this->normalizeDigits($term);

                $appointmentsQuery->where(function ($q) use ($term, $digits) {
                    $q->where('name', 'like', '%' . $term . '%')
                        ->orWhereHas('customer', function ($cq) use ($term, $digits) {
                            $cq->where('name', 'like', '%' . $term . '%')
                                ->orWhere('lastname', 'like', '%' . $term . '%')
                                ->orWhere('firma', 'like', '%' . $term . '%')
                                ->orWhere('email', 'like', '%' . $term . '%');

                            if ($digits !== '') {
                                $cq->orWhereRaw($this->normalizedPhoneSql('phone') . ' LIKE ?', ['%' . $digits . '%']);
                            }
                        });
                });
            }

            $appointments = $appointmentsQuery
                ->limit(20)
                ->get()
                ->map(function (MainAppointment $row) {
                    $row->type = 'Termin';

                    $employees = ($row->employees ?? collect())->unique('id')->values();

                    $row->assigned_employees = $employees
                        ->map(fn (Employee $employee) => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')))
                        ->filter()
                        ->implode(', ');

                    $row->participants = $employees
                        ->map(function (Employee $employee) {
                            return [
                                'id'     => $employee->id,
                                'name'   => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                                'avatar' => $employee->image
                                    ? asset('images/employee/' . $employee->image)
                                    : asset('images/gender/male.png'),
                            ];
                        })
                        ->values();

                    $row->assigned_by_name = $row->createdBy
                        ? trim(($row->createdBy->name ?? '') . ' ' . ($row->createdBy->lastname ?? ''))
                        : null;

                    $row->start_date_label = optional($row->start_date)->format('d.m.Y H:i');
                    $row->end_date_label   = optional($row->end_date)->format('d.m.Y H:i');
                    $row->phone            = $row->customer->phone ?? null;
                    $row->email            = $row->customer->email ?? null;
                    $row->address          = $row->customer->full_address ?? null;
                    $row->deleted_at       = null;

                    return $row;
                });

            $results = $results->merge($appointments);
        }

        if ($want('Aufgabe')) {
            $tasksQuery = PersonalTask::with([
                'employees:id,name,lastname,image',
                'assignedBy:id,name,lastname',
                'customer:id,name,lastname,firma,phone,email',
            ])->select(
                'id',
                DB::raw('task_title as name'),
                DB::raw("'' as lastname"),
                'priority',
                'start_date',
                'due_date'
            );

            foreach ($searchTerms as $term) {
                $digits = $this->normalizeDigits($term);

                $tasksQuery->where(function ($q) use ($term, $digits) {
                    $q->where('task_title', 'like', '%' . $term . '%')
                        ->orWhereHas('customer', function ($cq) use ($term, $digits) {
                            $cq->where('name', 'like', '%' . $term . '%')
                                ->orWhere('lastname', 'like', '%' . $term . '%')
                                ->orWhere('firma', 'like', '%' . $term . '%')
                                ->orWhere('email', 'like', '%' . $term . '%');

                            if ($digits !== '') {
                                $cq->orWhereRaw($this->normalizedPhoneSql('phone') . ' LIKE ?', ['%' . $digits . '%']);
                            }
                        });
                });
            }

            $tasks = $tasksQuery
                ->limit(20)
                ->get()
                ->map(function (PersonalTask $row) {
                    $row->type = 'Aufgabe';

                    $employees = ($row->employees ?? collect())->unique('id')->values();

                    $row->assigned_employees = $employees
                        ->map(fn (Employee $employee) => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')))
                        ->filter()
                        ->implode(', ');

                    $row->participants = $employees
                        ->map(function (Employee $employee) {
                            return [
                                'id'     => $employee->id,
                                'name'   => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                                'avatar' => $employee->image
                                    ? asset('images/employee/' . $employee->image)
                                    : asset('images/gender/male.png'),
                            ];
                        })
                        ->values();

                    $row->assigned_by_name = $row->assignedBy
                        ? trim(($row->assignedBy->name ?? '') . ' ' . ($row->assignedBy->lastname ?? ''))
                        : null;

                    $row->start_date_label = optional($row->start_date)->format('d.m.Y');
                    $row->end_date_label   = optional($row->due_date)->format('d.m.Y');
                    $row->phone            = $row->customer->phone ?? null;
                    $row->email            = $row->customer->email ?? null;
                    $row->address          = $row->customer->full_address ?? null;
                    $row->deleted_at       = null;

                    return $row;
                });

            $results = $results->merge($tasks);
        }

        if ($want('Ticket')) {
            $ticketsQuery = Problem::with([
                'employees:id,name,lastname,image',
                'customer:id,name,lastname,firma,phone,email',
            ])->select('id', 'ticket_no', 'priority', 'date', 'end_date');

            foreach ($searchTerms as $term) {
                $digits = $this->normalizeDigits($term);

                $ticketsQuery->where(function ($q) use ($term, $digits) {
                    $q->where('ticket_no', 'like', '%' . $term . '%')
                        ->orWhere('article_name', 'like', '%' . $term . '%')
                        ->orWhereHas('customer', function ($cq) use ($term, $digits) {
                            $cq->where('name', 'like', '%' . $term . '%')
                                ->orWhere('lastname', 'like', '%' . $term . '%')
                                ->orWhere('firma', 'like', '%' . $term . '%')
                                ->orWhere('email', 'like', '%' . $term . '%');

                            if ($digits !== '') {
                                $cq->orWhereRaw($this->normalizedPhoneSql('phone') . ' LIKE ?', ['%' . $digits . '%']);
                            }
                        });
                });
            }

            $tickets = $ticketsQuery
                ->limit(20)
                ->get()
                ->map(function (Problem $row) {
                    $row->type = 'Ticket';
                    $row->name = 'Ticket #' . $row->ticket_no;
                    $row->lastname = '';

                    $employees = ($row->employees ?? collect())->unique('id')->values();

                    $row->assigned_employees = $employees
                        ->map(fn (Employee $employee) => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')))
                        ->filter()
                        ->implode(', ');

                    $row->participants = $employees
                        ->map(function (Employee $employee) {
                            return [
                                'id'     => $employee->id,
                                'name'   => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                                'avatar' => $employee->image
                                    ? asset('images/employee/' . $employee->image)
                                    : asset('images/gender/male.png'),
                            ];
                        })
                        ->values();

                    $row->phone            = $row->customer->phone ?? null;
                    $row->email            = $row->customer->email ?? null;
                    $row->address          = $row->customer->full_address ?? null;
                    $row->start_date_label = optional($row->date)->format('d.m.Y');
                    $row->end_date_label   = optional($row->end_date)->format('d.m.Y');
                    $row->deleted_at       = null;

                    return $row;
                });

            $results = $results->merge($tickets);
        }

        $results = $results->map(function ($item) {
            if (!isset($item->participants) || !is_array($item->participants)) {
                $item->participants = [];
            }

            $item->icon = $item->icon ?? match ($item->type) {
                'Kunde'       => 'feather icon-user white',
                'Hersteller'  => 'feather icon-briefcase white',
                'Lieferant'   => 'feather icon-truck white',
                'Anfrage'     => 'feather icon-message-square white',
                'Mitarbeiter' => 'feather icon-users white',
                'Termin'      => 'feather icon-calendar white',
                'Aufgabe'     => 'feather icon-check-square white',
                'Ticket'      => 'feather icon-alert-triangle white',
                default       => 'feather icon-user white',
            };

            if (!isset($item->avatar) || !$item->avatar) {
                $firstAvatar = !empty($item->participants) ? ($item->participants[0]['avatar'] ?? null) : null;
                $item->avatar = $firstAvatar ?: asset('images/gender/male.png');
            }

            $item->url = $item->url ?? match ($item->type) {
                'Kunde'       => route('new.lead.profile', $item->id),
                'Hersteller'  => route('brand.index', $item->id),
                'Lieferant'   => route('distributor_department.legacy', $item->id),
                'Anfrage'     => route('inquiry.show.profile', $item->id),
                'Mitarbeiter' => route('emp.profile', $item->id),
                'Termin'      => url('customer/appointments/' . $item->id),
                'Aufgabe'     => url('personal_task_details/' . $item->id),
                'Ticket'      => url('ticket_details/' . $item->id),
                default       => '#',
            };

            $item->deleted_at     = $item->deleted_at ?? null;
            $item->verified_label = $item->verified_label ?? null;
            $item->address        = $item->address ?? null;

            return $item;
        });

        $unique = $results
            ->unique(fn ($row) => ($row->type ?? '') . '#' . ($row->id ?? ''))
            ->values()
            ->take(20);

        return response()->json($unique);
    }

    public function customer(Request $request)
    {
        NewLeads::withTrashed()->where('id', $request->id)->restore();

        return response()->json(['success' => true]);
    }

    public function brand(Request $request)
    {
        $this->restoreIfSoftDeletable(Brand::class, $request->id);

        return response()->json(['success' => true]);
    }

    public function distributor(Request $request)
    {
        $this->restoreIfSoftDeletable(Distributor::class, $request->id);

        return response()->json(['success' => true]);
    }

    /**
     * Restore a soft-deleted record only if the model actually supports
     * soft deletes. Models without the SoftDeletes trait (e.g. Brand,
     * Distributor) have no "deleted_at" column, so calling withTrashed()
     * on them throws a BadMethodCallException (HTTP 500). In that case the
     * record can never be soft-deleted, so there is nothing to restore.
     */
    protected function restoreIfSoftDeletable(string $modelClass, $id): void
    {
        if (!$id) {
            return;
        }

        if (!in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            return;
        }

        $modelClass::withTrashed()->where('id', $id)->restore();
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
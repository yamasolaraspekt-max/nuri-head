<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class LeadActivityLogsController extends Controller
{
    public function recentLiveActivities(Request $request)
    {
        $filterCustomers = array_filter((array) $request->input('customers', []));
        $filterEmployees = array_filter((array) $request->input('employees', []));
        $filterProducts  = array_filter((array) $request->input('products', []));
        $search          = trim((string) $request->input('q', ''));
        $perPage         = max(1, min((int) $request->input('per_page', 20), 50));
        $page            = max(1, (int) $request->input('page', 1));
        $mode            = $request->input('mode', 'active') === 'archive' ? 'archive' : 'active';
        $userId          = auth()->id();

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        /*
        |--------------------------------------------------------------------------
        | Step 1: paginate ONLY the activity table first
        | IMPORTANT: sort by ID instead of created_at to avoid MySQL sort memory
        |--------------------------------------------------------------------------
        */
        $baseQuery = DB::table('lead_activity_logs')
            ->select([
                'id',
                'new_leads_id',
                'alternative_id',
                'product_id',
                'user_id',
                'user_name',
                'event_type',
                'model_type',
                'model_id',
                'changes',
                'created_at',
                'updated_at',
            ]);

            if ($mode === 'archive') {
                $baseQuery->whereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('lead_activity_log_reads')
                        ->whereColumn('lead_activity_log_reads.lead_activity_log_id', 'lead_activity_logs.id')
                        ->where('lead_activity_log_reads.user_id', $userId);
                });
            } else {
                $baseQuery->whereNotExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('lead_activity_log_reads')
                        ->whereColumn('lead_activity_log_reads.lead_activity_log_id', 'lead_activity_logs.id')
                        ->where('lead_activity_log_reads.user_id', $userId);
                });
            }

        if (!empty($filterCustomers)) {
            $baseQuery->whereIn('new_leads_id', $filterCustomers);
        }

        if (!empty($filterEmployees)) {
            // In your current system user_name contains employee id
            $baseQuery->whereIn('user_name', $filterEmployees);
        }

        if (!empty($filterProducts)) {
            $baseQuery->whereIn('product_id', $filterProducts);
        }

        $paginator = $baseQuery
            ->orderByDesc('id')
            ->simplePaginate($perPage);

        $pageRows = collect($paginator->items());

        if ($pageRows->isEmpty()) {
            return response()->json([
                'data' => [],
                'pagination' => [
                    'current_page'   => $page,
                    'per_page'       => $perPage,
                    'next_page_url'  => $paginator->nextPageUrl(),
                    'prev_page_url'  => $paginator->previousPageUrl(),
                    'has_more_pages' => $paginator->hasMorePages(),
                ],
            ]);
        }

        $ids = $pageRows->pluck('id')->values()->all();

        /*
        |--------------------------------------------------------------------------
        | Step 2: join only the paginated rows
        |--------------------------------------------------------------------------
        */
        $joinedRows = DB::table('lead_activity_logs')
            ->leftJoin('employees', 'lead_activity_logs.user_name', '=', 'employees.id')
            ->leftJoin('new_leads', 'lead_activity_logs.new_leads_id', '=', 'new_leads.id')
            ->leftJoin('article_groups', 'lead_activity_logs.product_id', '=', 'article_groups.id')
            ->whereIn('lead_activity_logs.id', $ids)
            ->select(
                'lead_activity_logs.*',
                'employees.name as emp_first',
                'employees.lastname as emp_last',
                'new_leads.name as lead_name',
                'new_leads.lastname as lead_lastname',
                'new_leads.firma as lead_firma',
                'article_groups.article_group as product_name'
            )
            ->get()
            ->keyBy('id');

        $logs = $pageRows
            ->map(function ($row) use ($joinedRows) {
                return $joinedRows[$row->id] ?? $row;
            })
            ->map(function ($log) {
                $classNameRaw = class_basename($log->model_type);

                $classNameDe = match ($classNameRaw) {
                    'CustomerNote'       => 'Notizen',
                    'NewLeads'           => 'Kunde',
                    'LeadAlternativeAdd' => 'Objekt / Adresse',
                    'LeadProductList'    => 'Prozess',
                    'Problem'            => 'Ticket',
                    'Appointment'        => 'Termin',
                    default              => $classNameRaw,
                };

                $notificationType = match ($classNameRaw) {
                    'CustomerNote'       => 'notes',
                    'LeadProductList'    => 'process',
                    'Problem'            => 'ticket',
                    'Appointment'        => 'appointment',
                    'NewLeads'           => 'customer',
                    'LeadAlternativeAdd' => 'address',
                    default              => 'general',
                };

                $notificationTypeLabel = match ($notificationType) {
                    'notes'       => 'Notizen',
                    'process'     => 'Prozess',
                    'ticket'      => 'Ticket',
                    'appointment' => 'Termin',
                    'customer'    => 'Kunde',
                    'address'     => 'Objekt / Adresse',
                    default       => 'Allgemein',
                };


                $empName = trim(($log->emp_first ?? '') . ' ' . ($log->emp_last ?? ''));
                if ($empName === '') {
                    if (!empty($log->user_name) && !is_numeric($log->user_name)) {
                        $empName = $log->user_name;
                    } else {
                        $empName = $log->user_name ? 'Mitarbeiter #' . $log->user_name : 'System';
                    }
                }

                $customerName = trim(($log->lead_name ?? '') . ' ' . ($log->lead_lastname ?? ''));
                if ($customerName === '') {
                    $customerName = $log->lead_firma ?: ('Kunde #' . ($log->new_leads_id ?? ''));
                }
                if ($customerName === 'Kunde #' || $customerName === '') {
                    $customerName = 'Unbekannter Kunde';
                }

                $productName = $log->product_name ?: 'Allgemein';

                $actionDe = match ($log->event_type) {
                    'created' => 'erstellt',
                    'updated' => 'aktualisiert',
                    'deleted' => 'gelöscht',
                    default   => $log->event_type,
                };

                $stageDE = [
                    'open'      => 'Offen',
                    'lead'      => 'Lead',
                    'offer'     => 'Verkauf',
                    'deal'      => 'Auftrag',
                    'project'   => 'Montage',
                    'completed' => 'Abschluss',
                    'archive'   => 'Archiv',
                    'junk'      => 'Junk',
                    'cancel'    => 'Abgebrochen',
                    'reject'    => 'Junk',
                ];

                $workStatusDE = [
                    'playing' => 'Läuft',
                    'paused'  => 'Pausiert',
                    'stopped' => 'Gestoppt',
                ];

                $detailText = "Eintrag {$actionDe}";
                $changesDecoded = null;

                if (!empty($log->changes)) {
                    $changesDecoded = json_decode($log->changes, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($changesDecoded)) {
                        if ($classNameRaw === 'CustomerNote') {
                            $noteText = $changesDecoded['attributes']['description']
                                ?? $changesDecoded['description']['to']
                                ?? 'Kein Text';

                            if (mb_strlen($noteText) > 60) {
                                $noteText = mb_substr($noteText, 0, 60) . '...';
                            }

                            $detailText = $noteText;
                        } elseif ($classNameRaw === 'LeadProductList') {
                            if (isset($changesDecoded['stage'])) {
                                $fromRaw = strtolower($changesDecoded['stage']['from'] ?? '');
                                $toRaw   = strtolower($changesDecoded['stage']['to'] ?? '');

                                $from = $stageDE[$fromRaw] ?? ucfirst($fromRaw ?: 'Unbekannt');
                                $to   = $stageDE[$toRaw] ?? ucfirst($toRaw ?: 'Unbekannt');

                                $detailText = "Status: '{$from}' ➞ '{$to}'";
                            } elseif (isset($changesDecoded['work_status'])) {
                                $fromRaw = strtolower($changesDecoded['work_status']['from'] ?? '');
                                $toRaw   = strtolower($changesDecoded['work_status']['to'] ?? '');

                                $from = $workStatusDE[$fromRaw] ?? ucfirst($fromRaw ?: 'Unbekannt');
                                $to   = $workStatusDE[$toRaw] ?? ucfirst($toRaw ?: 'Unbekannt');

                                $detailText = "Arbeitsstatus: '{$from}' ➞ '{$to}'";
                            } elseif (isset($changesDecoded['info'])) {
                                $detailText = $changesDecoded['info'];
                            }
                        }
                    }
                }

                return [
                    'id'                      => $log->id,
                    'customer_id'             => $log->new_leads_id,
                    'product_id'              => $log->product_id,
                    'employee_id'             => $log->user_name,

                    'action'                  => $log->event_type,
                    'action_de'               => $actionDe,

                    'model'                   => $classNameRaw,
                    'model_de'                => $classNameDe,

                    'notification_type'       => $notificationType,
                    'notification_type_label' => $notificationTypeLabel,

                    'customer_name'           => $customerName,
                    'product_name'            => $productName,
                    'creator_name'            => $empName,

                    'detail_text'             => $detailText,
                    'time'                    => Carbon::parse($log->created_at)->format('H:i'),
                    'date'                    => Carbon::parse($log->created_at)->format('d.m.Y H:i'),

                    'changes'                 => $changesDecoded,
                    'created_at'              => $log->created_at,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Step 3: sidebar search on transformed rows
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $needle = mb_strtolower($search);

            $logs = $logs->filter(function ($log) use ($needle) {
                $haystack = mb_strtolower(implode(' | ', [
                    $log['customer_name'] ?? '',
                    $log['product_name'] ?? '',
                    $log['creator_name'] ?? '',
                    $log['model_de'] ?? '',
                    $log['detail_text'] ?? '',
                    $log['action_de'] ?? '',
                ]));

                return str_contains($haystack, $needle);
            })->values();
        } else {
            $logs = $logs->values();
        }

        return response()->json([
            'data' => $logs,
            'pagination' => [
                'current_page'   => $page,
                'per_page'       => $perPage,
                'next_page_url'  => $paginator->nextPageUrl(),
                'prev_page_url'  => $paginator->previousPageUrl(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function saveLiveActivityFilters(Request $request)
    {
        $user = auth()->user();

        $user->activityFilter()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'customer_ids'       => $request->input('customers', []),
                'employee_ids'       => $request->input('employees', []),
                'product_ids'        => $request->input('products', []),
                'notification_types' => $request->input('types', []),
                'is_muted'           => $request->boolean('is_muted'),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Nicht authentifiziert.',
            ], 401);
        }

        $exists = DB::table('lead_activity_logs')
            ->where('id', $id)
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivität wurde nicht gefunden.',
            ], 404);
        }

        DB::table('lead_activity_log_reads')->updateOrInsert(
            [
                'lead_activity_log_id' => $id,
                'user_id'              => $userId,
            ],
            [
                'read_at'    => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde archiviert.',
        ]);
    }

}
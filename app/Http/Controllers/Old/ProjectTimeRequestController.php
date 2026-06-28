<?php
namespace App\Http\Controllers;

use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\ArticleGroup;
use App\Models\LeadProductList;
use App\Models\CustomerHistory;
use App\Models\ProjectTimeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectTimeRequestController extends Controller
{
    protected function timeToMinutes(?string $time): int
    {
        if (!$time) {
            return 0;
        }
        [$h, $m, $s] = array_pad(explode(':', $time), 3, 0);
        return ((int)$h) * 60 + (int)$m + (int)round(((int)$s) / 60);
    }

    protected function fmtHM(int $minutes): string
    {
        $sign = $minutes < 0 ? '-' : '';
        $minutes = abs($minutes);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%s%02d:%02d', $sign, $h, $m);
    }

    /**
     * AJAX: show project time summary + timeline for a single product.
     */
    public function show(Request $request)
    {
        $customerId    = $request->get('customer_id');
        $alternativeId = $request->get('alternative_id');
        $productId     = $request->get('product_id');
        $sectionId     = $request->get('section_id'); // optional

        $customer    = NewLeads::findOrFail($customerId);
        $alternative = LeadAlternativeAdd::findOrFail($alternativeId);
        $product     = ArticleGroup::findOrFail($productId);

        // lead_product_lists row (single “project”)
        $projectRow = LeadProductList::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->firstOrFail();

        // 1) Base project minutes
        $baseMinutes = $projectRow->project_minutes
            ?? $customer->default_project_minutes
            ?? 600; // fallback 10h

        // 2) Approved extra minutes
        $approvedExtraMinutes = ProjectTimeRequest::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->sum('extra_minutes');

        $totalBudgetMinutes   = $baseMinutes + (int) $approvedExtraMinutes;

        // 3) History entries (for used time + timeline)
        $historyQuery = CustomerHistory::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        if ($sectionId) {
            $historyQuery->where('section_id', $sectionId);
        }

        $histories = $historyQuery
            ->orderBy('done_date')
            ->orderBy('created_at')
            ->get();

        $totalUsedMinutes = 0;
        $startDate        = null;
        $endDate          = null;

        $timeline = [];

        foreach ($histories as $row) {
            // Real used time: prefer is_time, fallback plan_time, fallback d_time
            $minutes = $this->timeToMinutes(
                $row->is_time
                ?? $row->plan_time
                ?? $row->d_time
            );

            $totalUsedMinutes += $minutes;

            $date = $row->done_date ?: $row->created_at?->toDateString();

            if ($date) {
                if (!$startDate || $date < $startDate) {
                    $startDate = $date;
                }
                if (!$endDate || $date > $endDate) {
                    $endDate = $date;
                }
            }

            $timeline[] = [
                'date'        => $date,
                'minutes'     => $minutes,
                'hm'          => $this->fmtHM($minutes),
                'notes'       => $row->notes,
                'is_done'     => $row->is_done,
                'phase_id'    => $row->phase_id,
                'activity_id' => $row->activity_id,
            ];
        }

        $remainingMinutes = max(0, $totalBudgetMinutes - $totalUsedMinutes);

        // Duration between first and last entry (days)
        $durationDays = null;
        if ($startDate && $endDate) {
            $durationDays = \Carbon\Carbon::parse($startDate)
                ->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        }

        // Requests history
        $requests = ProjectTimeRequest::with(['requester', 'approver'])
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (ProjectTimeRequest $req) {
                return [
                    'id'           => $req->id,
                    'extra_minutes'=> $req->extra_minutes,
                    'extra_hm'     => $this->fmtHM($req->extra_minutes),
                    'status'       => $req->status,
                    'reason'       => $req->reason,
                    'answer'       => $req->answer,
                    'requested_by' => $req->requester
                        ? $req->requester->name.' '.$req->requester->lastname
                        : null,
                    'approved_by'  => $req->approver
                        ? $req->approver->name.' '.$req->approver->lastname
                        : null,
                    'created_at'   => $req->created_at?->format('d.m.Y H:i'),
                    'approved_at'  => $req->approved_at?->format('d.m.Y H:i'),
                ];
            });

        return response()->json([
            'project' => [
                'customer'         => $customer->name.' '.$customer->lastname,
                'alternative'      => $alternative->object_name,
                'product'          => $product->article_group,
                'base_minutes'     => $baseMinutes,
                'base_hm'          => $this->fmtHM($baseMinutes),
                'extra_minutes'    => (int) $approvedExtraMinutes,
                'extra_hm'         => $this->fmtHM((int)$approvedExtraMinutes),
                'total_budget'     => $totalBudgetMinutes,
                'total_budget_hm'  => $this->fmtHM($totalBudgetMinutes),
                'used_minutes'     => $totalUsedMinutes,
                'used_hm'          => $this->fmtHM($totalUsedMinutes),
                'remaining_minutes'=> $remainingMinutes,
                'remaining_hm'     => $this->fmtHM($remainingMinutes),
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'duration_days'    => $durationDays,
            ],
            'timeline' => $timeline,
            'requests' => $requests,
        ]);
    }

    /**
     * Store a new time extension request (called from drawer form).
     */
    public function requestMoreTime(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'product_id'     => 'required|exists:article_groups,id',
            'extra_hours'    => 'nullable|integer|min:0',
            'extra_minutes'  => 'nullable|integer|min:0|max:59',
            'reason'         => 'nullable|string|max:5000',
        ]);

        $employeeId = (int) auth()->user()->name; // you store employee_id in users.name

        $hours   = (int) $request->get('extra_hours', 0);
        $minutes = (int) $request->get('extra_minutes', 0);
        $totalExtraMinutes = $hours * 60 + $minutes;

        if ($totalExtraMinutes <= 0) {
            return response()->json([
                'ok'      => false,
                'message' => 'Bitte eine zusätzliche Zeit größer als 0 eingeben.',
            ], 422);
        }

        $ptr = ProjectTimeRequest::create([
            'customer_id'    => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'product_id'     => $request->product_id,
            'section_id'     => $request->section_id,
            'requested_by'   => $employeeId,
            'extra_minutes'  => $totalExtraMinutes,
            'status'         => 'pending',
            'reason'         => $request->reason,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Zeit-Erweiterung wurde angefragt.',
            'request' => $ptr->id,
        ]);
    }

    /**
     * Admin: list all time requests.
     */
    public function index()
    {
        $requests = ProjectTimeRequest::with(['customer', 'alternative', 'product', 'requester', 'approver'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.project_time.index', compact('requests'));
    }

    /**
     * Admin: approve a request and add minutes to project_minutes.
     */
    public function approve(Request $request, ProjectTimeRequest $timeRequest)
    {
        if ($timeRequest->status !== 'pending') {
            return back()->with('error', 'Anfrage ist bereits bearbeitet.');
        }

        $employeeId = (int) auth()->user()->name;

        DB::transaction(function () use ($timeRequest, $employeeId, $request) {
            // mark as approved
            $timeRequest->status      = 'approved';
            $timeRequest->approved_by = $employeeId;
            $timeRequest->approved_at = now();
            $timeRequest->answer      = $request->get('answer');
            $timeRequest->save();

            // update lead_product_lists.project_minutes
            $projectRow = LeadProductList::where('customer_id', $timeRequest->customer_id)
                ->where('alternative_id', $timeRequest->alternative_id)
                ->where('product_id', $timeRequest->product_id)
                ->first();

            if ($projectRow) {
                $current = $projectRow->project_minutes ?? 0;
                $projectRow->project_minutes = $current + $timeRequest->extra_minutes;
                $projectRow->save();
            }
        });

        return back()->with('message', 'Zeit-Erweiterung wurde genehmigt.');
    }

    /**
     * Admin: reject a request.
     */
    public function reject(Request $request, ProjectTimeRequest $timeRequest)
    {
        if ($timeRequest->status !== 'pending') {
            return back()->with('error', 'Anfrage ist bereits bearbeitet.');
        }

        $employeeId = (int) auth()->user()->name;

        $timeRequest->status      = 'rejected';
        $timeRequest->approved_by = $employeeId;
        $timeRequest->approved_at = now();
        $timeRequest->answer      = $request->get('answer');
        $timeRequest->save();

        return back()->with('message', 'Zeit-Erweiterung wurde abgelehnt.');
    }
}

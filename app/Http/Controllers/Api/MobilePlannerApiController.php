<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlannerItem;
use App\Models\PlannerItemChecklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon;
use Illuminate\Support\Str;

class MobilePlannerApiController extends Controller
{
    private function resolvePhotoUrl($imageName)
    {
        if (!$imageName) return null;
        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }
        $cleanPath = ltrim($imageName, '/');
        $domain = 'https://nuri-head.de'; 
        if (Str::contains($cleanPath, 'images/employee')) {
             return $domain . '/' . $cleanPath;
        }
        return $domain . '/images/employee/' . $cleanPath;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $employeeId = null;
        if (is_numeric($user->name)) {
            $employeeId = (int) $user->name;
        } else {
            $employee = \App\Models\Employee::where('name', 'LIKE', "%{$user->name}%")
                ->orWhere('lastname', 'LIKE', "%{$user->name}%")
                ->first();
            if ($employee) $employeeId = $employee->id;
        }

        if (!$employeeId) {
            return response()->json(['error' => 'Kein Mitarbeiterprofil gefunden.'], 403);
        }

        // Handle Date Parameter
        $targetDate = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::now();

        $items = PlannerItem::query()
            ->with(['plan.customer', 'employees', 'checklists', 'assets'])
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->whereNull('deleted_at')
            ->whereDate('planned_start_at', $targetDate->format('Y-m-d')) 
            ->orderBy('planned_start_at')
            ->get();

        $mobileTasks = $items->map(function ($item) {
            
            $details = [];
            $type = $item->source_type ?? 'personal_task';
            
            if ($type === 'appointment') {
                $source = DB::table('main_appointments')->where('id', $item->source_id)->first();
                if ($source) {
                    $details['appointment_type'] = $source->appointment_type ?? 'Allgemein';
                    $details['name'] = $source->name;
                    $details['address'] = $source->full_address;
                    $details['lat'] = $source->{'lat/long'} ?? null;
                    $details['is_report'] = (bool) $source->is_report;
                }
            } 
            elseif ($type === 'ticket') {
                $source = DB::table('problems')->where('id', $item->source_id)->first();
                if ($source) {
                    $details['ticket_no'] = $source->ticket_no;
                    $details['error_code'] = $source->error_code;
                    $details['problem'] = $source->problem;
                    $details['solution'] = $source->solution; 
                    $details['priority'] = $source->priority;
                    $details['status'] = $source->status;
                }
            } 
            elseif ($type === 'phase_activity') {
                $source = DB::table('phase_activities')
                    ->leftJoin('task_phases', 'phase_activities.phase_id', '=', 'task_phases.id')
                    ->select('phase_activities.*', 'task_phases.phase_name')
                    ->where('phase_activities.id', $item->source_id)
                    ->first();
                    
                if ($source) {
                    $details['phase_name'] = $source->phase_name ?? 'Phase';
                    $details['title'] = $source->title;
                    $details['description'] = $source->description;
                    $details['notes'] = $source->notes;
                    $details['duration_estimated'] = $source->duration;
                }
            }
            elseif ($type === 'personal_task') {
                $source = DB::table('personal_tasks')->where('id', $item->source_id)->first();
                if ($source) {
                    $details['priority'] = $source->priority;
                    $details['description'] = $source->description;
                    $details['is_customer'] = (bool) $source->is_customer;
                }
            }

            $customer = $item->plan?->customer;
            
            // --- FIX: Logic to exclude Lat/Log from address string ---
            $rawAddress = $details['address'] ?? ($customer ? ($customer->full_address ?? $customer->city) : null);
            
            // If address is empty OR contains "Lat:"/"Log:", fallback to city or generic text
            if (empty($rawAddress) || Str::contains($rawAddress, ['Lat:', 'lat:', 'Log:', 'log:', 'Lon:', 'lon:'])) {
                $location = $customer ? ($customer->city ?? 'Zum Kunden') : 'Büro / Intern';
            } else {
                $location = $rawAddress;
            }

            $time = $item->planned_start_at 
                ? Carbon::parse($item->planned_start_at)->format('H:i') 
                : '08:00';

            $statusMap = [
                'open' => 'pending', 'planned' => 'pending', 'in_progress' => 'pending', 
                'done' => 'completed', 'completed' => 'completed', 'cancelled' => 'cancelled'
            ];
            $mobileStatus = $statusMap[$item->status] ?? 'pending';

            $team = $item->employees->map(function($emp) {
                $fullname = trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? ''));
                $photo = $this->resolvePhotoUrl($emp->image);
                
                if (!$photo) {
                    $bg = '164191'; 
                    $photo = "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background={$bg}&color=fff";
                }
                return [
                    'id' => $emp->id,
                    'name' => $fullname,
                    'photo_url' => $photo
                ];
            })->values();

            return [
                'id'          => $item->id,
                'type'        => $type,
                'title'       => $item->title,
                'description' => $item->description,
                'location'    => $location,
                'time'        => $time,
                'team'        => $team,
                'status'      => $mobileStatus,
                'details'     => $details,
                'checklist'   => $item->checklists->map(fn($c) => ['id'=>$c->id, 'txt'=>$c->title, 'done'=>(bool)$c->is_completed]),
                'materials'   => $item->assets->map(fn($a) => $a->item . ' ' . $a->model)->toArray(),
            ];
        });

        return response()->json([
            'ok' => true, 
            'date' => $targetDate->format('Y-m-d'),
            'data' => $mobileTasks
        ]);
    }

    public function sync(Request $request)
    {
        Log::info("MOBILE API: Sync Request Received", $request->all());

        $data = $request->validate([
            'id' => 'required|integer',
            'status' => 'nullable|string',
            'checklist' => 'nullable|array',
            'report' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        $item = PlannerItem::find($data['id']);
        if (!$item) return response()->json(['ok' => false], 404);

        if (isset($data['status'])) {
            $backendStatus = ($data['status'] === 'completed') ? 'done' : 'planned';
            if ($item->status !== $backendStatus && $item->status !== 'done') {
                $item->status = $backendStatus;
            }
        }

        if (isset($data['report']) || isset($data['signature'])) {
            $meta = $item->meta ? json_decode($item->meta, true) : [];
            if(isset($data['report'])) $meta['mobile_report'] = $data['report'];
            if(isset($data['signature'])) $meta['signature'] = $data['signature']; 
            $item->meta = json_encode($meta);
        }
        
        $item->save();

        if (isset($data['checklist']) && is_array($data['checklist'])) {
            foreach ($data['checklist'] as $chk) {
                if (isset($chk['id'])) {
                    PlannerItemChecklist::where('id', $chk['id'])->update([
                        'is_completed' => $chk['done'] ? 1 : 0
                    ]);
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
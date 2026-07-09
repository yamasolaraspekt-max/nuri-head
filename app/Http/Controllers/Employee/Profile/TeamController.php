<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,update')->only(['update']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = trim($request->input('search'));

        $teams = Team::with([
            'department',
            'leader.employee',
            'members.employee',
            'reserves.employee'
        ])
        ->when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('department', fn($q) => $q->where('department_name', 'like', "%{$search}%"))
                ->orWhereHas('leader.employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%");
                });
        })
        ->orderBy('name')
        ->get();

        $departments = Department::orderBy('department_name')->get();

        return view('admin.teams.index', compact('teams', 'departments', 'search'));
    }


    public function create()
    {
        $departments = Department::orderBy('department_name')->get();
        return view('admin.teams.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required','string','max:190'],
            'department_id' => ['required','exists:departments,id'],
            'status'        => ['nullable','string'],
            'description'   => ['nullable','string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $team = Team::create($validated);

        return redirect()->route('teams.index')
            ->with('success', 'Team created.');
    }

    public function edit(Team $team)
    {
        $team->load(['department','membersAll.employee','membersAll.position','leader']);
        $positions = Position::orderBy('position')->get();

        // Employees restricted to this team's department
        $deptEmployees = Employee::select('employees.*')
            ->join('employee_departments','employee_departments.employee_id','=','employees.id')
            ->where('employee_departments.department_id', $team->department_id)
            ->orderBy('lastname')->orderBy('name')
            ->get();

        return view('admin.teams.edit', compact('team','positions','deptEmployees'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name'          => ['required','string','max:190'],
            'status'        => ['nullable','string'],
            'description'   => ['nullable','string'],
        ]);

        $team->update($validated);

        return back()->with('success', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted.');
    }

    /**
     * Sync members from the drag & drop UI:
     * Expect arrays:
     * - leader: [ {employee_id, position_id} ] (0..1)
     * - members: [ {employee_id, position_id}, ... ]
     * - reserves: [ {employee_id, position_id}, ... ]
     */
    public function syncMembers(Request $request, Team $team)
    {
        $payload = $request->validate([
            'leader'   => ['nullable','array'],
            'members'  => ['nullable','array'],
            'reserves' => ['nullable','array'],
        ]);

        DB::transaction(function () use ($payload, $team) {
            // wipe and rebuild for simplicity & certainty
            TeamMember::where('team_id', $team->id)->delete();

            $sort = 0;

            // leader (max 1)
            if (!empty($payload['leader'])) {
                $l = $payload['leader'][0];
                TeamMember::create([
                    'team_id'     => $team->id,
                    'employee_id' => (int)$l['employee_id'],
                    'position_id' => !empty($l['position_id']) ? (int)$l['position_id'] : null,
                    'role'        => 'leader',
                    'sort_order'  => $sort++,
                ]);
            }

            // members
            foreach (($payload['members'] ?? []) as $m) {
                TeamMember::create([
                    'team_id'     => $team->id,
                    'employee_id' => (int)$m['employee_id'],
                    'position_id' => !empty($m['position_id']) ? (int)$m['position_id'] : null,
                    'role'        => 'member',
                    'sort_order'  => $sort++,
                ]);
            }

            // reserves
            $rSort = 0;
            foreach (($payload['reserves'] ?? []) as $r) {
                TeamMember::create([
                    'team_id'     => $team->id,
                    'employee_id' => (int)$r['employee_id'],
                    'position_id' => !empty($r['position_id']) ? (int)$r['position_id'] : null,
                    'role'        => 'reserve',
                    'sort_order'  => $rSort++,
                ]);
            }
        });

        return back()->with('success', 'Team members saved.');
    }

    /**
     * Promote a reserve to member and optionally demote a current member to reserve.
     * Request: reserve_employee_id, (optional) replace_member_employee_id
     */
    public function promoteReserve(Request $request, Team $team)
    {
        $data = $request->validate([
            'reserve_employee_id'        => ['required','exists:employees,id'],
            'replace_member_employee_id' => ['nullable','exists:employees,id'],
        ]);

        DB::transaction(function () use ($data, $team) {
            $reserve = TeamMember::where('team_id',$team->id)
                ->where('employee_id',$data['reserve_employee_id'])
                ->where('role','reserve')->firstOrFail();

            // make reserve a member (append to end)
            $maxSort = TeamMember::where('team_id',$team->id)->where('role','member')->max('sort_order') ?? 0;
            $reserve->update(['role'=>'member','sort_order'=>$maxSort+1]);

            if (!empty($data['replace_member_employee_id'])) {
                $member = TeamMember::where('team_id',$team->id)
                    ->where('employee_id',$data['replace_member_employee_id'])
                    ->where('role','member')->first();
                if ($member) {
                    // demote to first reserve slot
                    $minReserve = TeamMember::where('team_id',$team->id)->where('role','reserve')->min('sort_order') ?? 0;
                    $member->update(['role'=>'reserve','sort_order'=>$minReserve-1]);
                }
            }
        });

        return back()->with('success', 'Reserve promoted.');
    }
}

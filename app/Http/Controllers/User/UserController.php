<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\User;
use App\Models\UserRoll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // FIX P0-06: Benutzerverwaltung erfordert das 'Users'-Recht je Aktion.
        // CheckUserPermission nutzt die in Paket 1 reparierte hasPermission() (users.id + is_admin-Bypass),
        // laeuft also auf dem gefixten Muster -> Aktivieren sperrt Admins NICHT aus.
        $this->middleware('permission:Users,read')->only(['admin_user', 'limit_user', 'adminUsersPage', 'adminUsersFetch']);
        $this->middleware('permission:Users,add')->only(['store', 'limit_store', 'adminUsersStore']);
        // FIX P0-2 (MASTER-01): updatePassword (Legacy-Route users/{user}/password) war ungegatet
        // -> Account-Takeover jedes Kontos. Jetzt gleiches Gate wie der Zwilling adminUsersPassword.
        $this->middleware('permission:Users,update')->only(['make_limit', 'active', 'deactive', 'limit_edit', 'adminUsersUpdate', 'adminUsersToggleActive', 'adminUsersPassword', 'updatePassword']);
        $this->middleware('permission:Users,delete')->only(['destroy', 'limit_destroy', 'adminUsersDestroy']);
    }

    /* =========================================================================
     | HELPERS
     * ========================================================================= */

    /**
     * IMPORTANT:
     * In your project, users.name stores employee_id.
     */
    protected function resolveEmployeeIdFromRequest(Request $request): ?string
    {
        if ($request->filled('employee_id')) {
            return (string) $request->employee_id;
        }

        if ($request->filled('name')) {
            return (string) $request->name;
        }

        return null;
    }

    protected function isSelf(User $user): bool
    {
        return auth()->check() && (int) auth()->id() === (int) $user->id;
    }

    protected function deleteUserImageIfExists(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $path = public_path('images/user/' . $photo);

        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /* =========================================================================
     | LEGACY: permissions
     * ========================================================================= */

    public function hasPremission($user_id, $roll)
    {
        $hasPermission = DB::table('user_rolls')
            ->where('user_id', $user_id)
            ->where('item_id', $roll)
            ->where('is_read', 1)
            ->orwhere('is_read', 'on')
            ->exists();

        return response()->json([
            'hasPermission' => $hasPermission,
        ]);
    }

    /* =========================================================================
     | LEGACY: general user list
     * ========================================================================= */

    public function index()
    {
        $data = DB::table('user_rolls')
            ->join('users', 'users.id', '=', 'user_rolls.user_id')
            ->select('user_rolls.*', 'users.name')
            ->get();

        // users.name speichert die employees.id -> Employee fuer Name/Bild im Profil laden.
        $employee = \App\Models\Employee::find(auth()->user()->name);

        return view('admin.user.user_view')
            ->with('data', $data)
            ->with('employee', $employee)
            ->with('user', auth()->user());
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->back()->with('not_msg', 'You cannot delete your own account.');
        }

        $this->deleteUserImageIfExists($user->image ?? null);
        $user->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        $employeeId = $this->resolveEmployeeIdFromRequest($request);

        if (!$employeeId) {
            return redirect()->back()->with('not_msg', 'Employee is required.');
        }

        $duplicate = User::where('email', $request->email)->first();

        if ($duplicate) {
            return redirect()->back()->with('delete_msg', 'The record is duplicated');
        }

        $user = new User();
        $user->name      = $employeeId; // employee_id stored in users.name
        $user->email     = $request->email;
        $user->password  = bcrypt($request->password);
        $user->is_admin  = auth()->user()->is_admin ? (int) ($request->input('is_admin', 1)) : 0; // FIX P0-06: Nicht-Admins koennen keinen Admin anlegen
        $user->is_active = $request->boolean('is_active') ? 1 : 0; // FIX P4-41: korrekte Auswertung statt No-Op
        $user->save();

        return redirect()->back()->with('save_msg', 'The record saved successfully!');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:users,id',
            'email' => 'required|email|max:255',
        ]);

        $user = User::findOrFail($request->id);

        $employeeId = $this->resolveEmployeeIdFromRequest($request);
        if ($employeeId) {
            $user->name = $employeeId; // employee_id
        }

        $user->email = $request->email;
        $user->save();

        return redirect()->to('/admin_user')->with('update_msg', 'The record updated successfully!');
    }

    /* =========================================================================
     | LEGACY: admin users page
     * ========================================================================= */

    public function admin_user(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = DB::table('users')
            ->leftJoin('employees', 'employees.id', '=', 'users.name') // users.name = employee_id
            ->where('users.is_admin', 1)
            ->select([
                'users.id',
                'users.email',
                'users.name as user_name', // employee_id
                'users.created_at',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'users.is_admin',
                'users.is_active',
            ]);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('users.email', 'like', "%{$q}%")
                    ->orWhere('employees.name', 'like', "%{$q}%")
                    ->orWhere('employees.lastname', 'like', "%{$q}%")
                    ->orWhereRaw(
                        "CONCAT(COALESCE(employees.name,''), ' ', COALESCE(employees.lastname,'')) like ?",
                        ["%{$q}%"]
                    );
            });
        }

        $data = $query->orderByDesc('users.id')->paginate(4)->appends($request->query());

        $employees = Employee::where('status', 'Active')
            ->get(['id', 'name', 'lastname', 'image']);

        return view('admin.user.admin_user', [
            'data'     => $data,
            'employee' => $employees,
        ]);
    }

    public function admin_destroy($id)
    {
        // FIX P0-06: Admin-Konten loeschen darf NUR ein bestehender Admin.
        abort_unless((bool) auth()->user()->is_admin, 403, 'Nur Administratoren duerfen Admin-Konten loeschen.');

        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->back()->with('not_msg', 'You cannot delete your own account.');
        }

        $this->deleteUserImageIfExists($user->image ?? null);
        $user->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully!');
    }

    /* =========================================================================
     | LEGACY: limited users page
     * ========================================================================= */

    public function limit_user()
    {
        $data['data'] = DB::table('users')
            ->leftJoin('employees', 'employees.id', '=', 'users.name')
            ->where('users.is_admin', '!=', 1)
            ->select([
                'users.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
            ])
            ->orderByDesc('users.id')
            ->paginate(4);

        $data['employee'] = Employee::whereRaw('LOWER(status) = ?', ['active'])
            ->get();

        return view('admin.user.limit_user', $data);
    }

    public function limit_destroy($id)
    {
        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->back()->with('not_msg', 'You cannot delete your own account.');
        }

        $this->deleteUserImageIfExists($user->image ?? null);
        $user->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully!');
    }

    public function limit_edit(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:users,id',
            'email' => 'required|email|max:255',
        ]);

        $user = User::findOrFail($request->id);

        $employeeId = $this->resolveEmployeeIdFromRequest($request);
        if ($employeeId) {
            $user->name = $employeeId;
        }

        $user->email = $request->email;
        $user->save();

        return redirect()->to('/limit_user')->with('update_msg', 'The record updated successfully!');
    }

    public function limit_store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $employeeId = $this->resolveEmployeeIdFromRequest($request);

        if (!$employeeId) {
            return redirect()->back()->with('not_msg', 'Employee is required.');
        }

        $user = new User();
        $user->name      = $employeeId; // employee_id
        $user->email     = $request->email;
        $user->password  = bcrypt($request->password);
        $user->is_admin  = 2;
        $user->is_active = $request->boolean('is_active') ? 1 : 0; // FIX P4-41: korrekte Auswertung statt No-Op
        $user->save();

        return redirect()->to('/limit_user')->with('save_msg', 'The record saved successfully!');
    }

    /* =========================================================================
     | LEGACY: photo
     * ========================================================================= */

   public function photo_create()
    {
        $data = User::with('employee')->findOrFail(auth()->id());

        return view('admin.user.user_photo_create')->with('data', $data);
    }

    public function save_photo(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:users,id',
            'cropped_image' => 'required|string',
        ]);
 
        $user = User::findOrFail($request->id);

        // Security: user can only change own photo unless admin
        if ((int) $user->id !== (int) auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Nicht erlaubt.');
        }

        $imageData = $request->input('cropped_image');

        if (!preg_match('/^data:image\/(png|jpg|jpeg|webp);base64,/', $imageData, $matches)) {
            return back()->withErrors([
                'image' => 'Ungültiges Bildformat.',
            ]);
        }

        $extension = strtolower($matches[1]);

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $imageData = preg_replace('/^data:image\/(png|jpg|jpeg|webp);base64,/', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);

        if ($decodedImage === false) {
            return back()->withErrors([
                'image' => 'Das Bild konnte nicht verarbeitet werden.',
            ]);
        }

        // Max approx 2 MB after crop
        if (strlen($decodedImage) > 2 * 1024 * 1024) {
            return back()->withErrors([
                'image' => 'Das Bild ist zu groß. Bitte kleiner zuschneiden.',
            ]);
        }

        $imageName = 'user_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;

        $userPath = public_path('images/user');
        $employeePath = public_path('images/employee');

        if (!File::exists($userPath)) {
            File::makeDirectory($userPath, 0755, true);
        }

        if (!File::exists($employeePath)) {
            File::makeDirectory($employeePath, 0755, true);
        }

        // Delete old user image
        $this->deleteImageFromFolder($user->image, 'images/user');

        // Find employee by auth()->user()->name because in your DB this is employee ID
        $employee = Employee::find(auth()->user()->name);

        if ($employee && $employee->image && $employee->image !== $user->image) {
            $this->deleteImageFromFolder($employee->image, 'images/employee');
        }

        // Save same image into both folders
        File::put($userPath . DIRECTORY_SEPARATOR . $imageName, $decodedImage);
        File::put($employeePath . DIRECTORY_SEPARATOR . $imageName, $decodedImage);

        $user->image = $imageName;
        $user->save();

        if ($employee) {
            $employee->image = $imageName;
            $employee->save();
        }

        return redirect()
            ->route('user.photo')
            ->with('save_msg', 'Profilbild wurde erfolgreich aktualisiert.');
    }

    private function deleteImageFromFolder(?string $image, string $folder): void
    {
        if (!$image) {
            return;
        }

        $path = public_path($folder . DIRECTORY_SEPARATOR . $image);

        if (File::exists($path) && File::isFile($path)) {
            File::delete($path);
        }
    }

    public function delete_photo_profile($photo)
    {
        $this->deleteImageFromFolder($photo, 'images/user');
        $this->deleteImageFromFolder($photo, 'images/employee');
    }

    /* =========================================================================
     | LEGACY: session / logoff
     * ========================================================================= */

    public function logOffUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($this->isSelf($user)) {
            return redirect()->back()->with('error', 'You cannot log off yourself from here.');
        }

        // Z2-W0-9 — **ehrlich beschriftet, statt eine Wirkung vorzutaeuschen.** Der alte Weg las
        // `session('user_session_<id>')` (ein Schluessel, den niemand setzt) und loeschte in einer
        // `sessions`-Tabelle, die es in dieser Installation nicht gibt: er war wirkungslos. Die
        // Sitzung endet jetzt ueber die Middleware `EnsureUserNotDisabled` beim NAECHSTEN Request
        // des Nutzers — nicht in derselben Sekunde, aber verlaesslich und unabhaengig vom
        // Session-Treiber. Die Meldung sagt genau das.
        $user->is_active = 0;
        $user->disabled_at = now();
        $user->save();

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return redirect()->back()->with('success', 'Konto deaktiviert — die Sitzung endet beim naechsten Aufruf des Nutzers, vorhandene Token sind widerrufen.');
    }

    /* =========================================================================
     | LEGACY: auth user changes own password
     * ========================================================================= */

    public function change_password(Request $request)
    {
        $request->validate([
            'password'         => 'required|min:8',
            'new_password'     => 'required|max:256|same:confirm_password',
            'confirm_password' => 'required|min:8',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('not_msg', 'The password is incorrect!');
        }

        $user = User::findOrFail(auth()->user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('save_msg', 'The password changed successfully!');
    }

    /* =========================================================================
     | LEGACY: role/status changes
     * ========================================================================= */

    public function make_limit($id)
    {
        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->to('/admin_user')->with('not_save', 'You can not make your self LIMITED!!');
        }

        $user->is_admin = 2;
        $user->save();

        return redirect()->to('/limit_user')->with('update_msg', 'The account is now limited');
    }

    public function make_admin($id)
    {
        // FIX P0-06: Admin-Rechte vergeben darf NUR ein bestehender Admin (Privilege-Escalation-Schutz).
        abort_unless((bool) auth()->user()->is_admin, 403, 'Nur Administratoren duerfen Admin-Rechte vergeben.');

        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->to('/admin_user')->with('not_save', 'You can not change your own role here.');
        }

        $user->is_admin = 1;
        $user->save();

        return redirect()->to('/admin_user')->with('update_msg', 'The account is now admin');
    }

    public function deactive($id)
    {
        $user = User::findOrFail($id);

        if ($this->isSelf($user)) {
            return redirect()->back()->with('not_save', 'You can not deactivate yourself!');
        }

        // Z2-W0-9: der Kontostatus steht in `disabled_at`, nicht in `is_active` — letzteres ist ein
        // Online-Flag und wird beim naechsten Login zurueckgesetzt. `is_active` wird hier weiterhin
        // mitgesetzt, damit die Online-Anzeige stimmt; die SPERRE haengt allein an `disabled_at`.
        $user->is_active = 0;
        $user->disabled_at = now();
        $user->save();

        // Vorhandene Tokens werden widerrufen — sonst bliebe der Mobile-Zugang offen.
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return redirect()->back()->with('update_msg', 'The account is deactivated');
    }

    public function active($id)
    {
        $user = User::findOrFail($id);

        // Z2-W0-9: die Gegenrichtung hebt den Kontostatus auf. Ohne diese Zeile bliebe ein
        // reaktivierter Nutzer gesperrt — die Sperre haengt an `disabled_at`, nicht an `is_active`.
        $user->is_active = 1;
        $user->disabled_at = null;
        $user->save();

        return redirect()->back()->with('update_msg', 'The User is Activated');
    }

    /* =========================================================================
     | SHARED: admin reset password for a specific user (legacy route)
     * ========================================================================= */

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('save_msg', 'The password changed successfully!');
    }

    /* =========================================================================
     | NEW AJAX ADMIN USERS MODULE
     * ========================================================================= */

public function adminUsersPage()
{
    $employees = Employee::where('status', 'Active')
        ->orderBy('name')
        ->get(['id','name','lastname','image']);

    // IMPORTANT: your blade expects $employees (plural)
    return view('admin.user.admin_user', [
        'employees' => $employees,
        'employee'  => $employees, // keep legacy compatibility if any old parts use $employee
        'data'      => collect([]), // avoid "Undefined $data" if your blade references it
    ]);
}
    public function adminUsersFetch(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $status  = (string) $request->get('status', 'all');    // all|active|inactive
        $admin   = (string) $request->get('admin', '1');       // 1|2|all
        $sort    = (string) $request->get('sort', 'newest');   // newest|oldest|name_asc|name_desc|email_asc|email_desc
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = min(100, max(6, (int) $request->get('per_page', 12)));

        $base = DB::table('users')
            ->leftJoin('employees', 'employees.id', '=', 'users.name') // users.name = employee_id
            ->select([
                'users.id',
                'users.email',
                'users.name as employee_id',
                'users.is_admin',
                'users.is_active',
                'users.created_at',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.status as emp_status',
            ])
            ->when($admin !== 'all', function ($query) use ($admin) {
                $query->where('users.is_admin', (int) $admin);
            });

        if ($status === 'active') {
            $base->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $base->where('users.is_active', 0);
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('users.email', 'like', "%{$q}%")
                    ->orWhere('employees.name', 'like', "%{$q}%")
                    ->orWhere('employees.lastname', 'like', "%{$q}%")
                    ->orWhereRaw(
                        "CONCAT(COALESCE(employees.name,''), ' ', COALESCE(employees.lastname,'')) like ?",
                        ["%{$q}%"]
                    );
            });
        }

        switch ($sort) {
            case 'oldest':
                $base->orderBy('users.created_at', 'asc');
                break;
            case 'name_asc':
                $base->orderByRaw("COALESCE(employees.name, '') asc")
                     ->orderByRaw("COALESCE(employees.lastname, '') asc");
                break;
            case 'name_desc':
                $base->orderByRaw("COALESCE(employees.name, '') desc")
                     ->orderByRaw("COALESCE(employees.lastname, '') desc");
                break;
            case 'email_asc':
                $base->orderBy('users.email', 'asc');
                break;
            case 'email_desc':
                $base->orderBy('users.email', 'desc');
                break;
            case 'newest':
            default:
                $base->orderBy('users.created_at', 'desc');
                break;
        }

        $total = (clone $base)->count();
        $items = $base->forPage($page, $perPage)->get();

        $statsBase = DB::table('users')->where('is_admin', 1);

        $stats = [
            'total'    => (clone $statsBase)->count(),
            'active'   => (clone $statsBase)->where('is_active', 1)->count(),
            'inactive' => (clone $statsBase)->where('is_active', 0)->count(),
            'new_7d'   => DB::table('users')
                ->where('is_admin', 1)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'items' => $items,
            'pagination' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => $total,
                'has_more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function adminUsersStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', Password::min(8)],
            'is_admin'    => ['required', 'in:1,2'],
            'is_active'   => ['nullable'],
        ]);

        $user = User::create([
            'name'      => (string) $data['employee_id'], // employee_id stored in users.name
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_admin'  => auth()->user()->is_admin ? (int) $data['is_admin'] : 0, // FIX P0-06: Nicht-Admins koennen keinen Admin anlegen
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);

        return response()->json([
            'ok'   => true,
            'id'   => $user->id,
            'msg'  => 'User created successfully.',
        ]);
    }

    public function adminUsersUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_admin'    => ['required', 'in:1,2'],
            'is_active'   => ['required', 'in:0,1'],
        ]);

        $user->name      = (string) $data['employee_id'];
        $user->email     = $data['email'];
        $user->is_admin  = auth()->user()->is_admin ? (int) $data['is_admin'] : (int) $user->is_admin; // FIX P0-06: Nicht-Admins koennen das Admin-Flag nicht aendern
        $user->is_active = (int) $data['is_active'];
        $user->save();

        return response()->json([
            'ok'  => true,
            'msg' => 'User updated successfully.',
        ]);
    }

    public function adminUsersDestroy(User $user)
    {
        if ($this->isSelf($user)) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $this->deleteUserImageIfExists($user->image ?? null);
        $user->delete();

        return response()->json([
            'ok'  => true,
            'msg' => 'User deleted successfully.',
        ]);
    }

    public function adminUsersToggleActive(User $user)
    {
        if ($this->isSelf($user)) {
            return response()->json([
                'message' => 'You cannot change your own status here.',
            ], 422);
        }

        // Z2-W0-9: der Schalter fuehrt jetzt den Kontostatus. `is_active` laeuft mit, damit die
        // Online-Anzeige nicht springt; gesperrt wird ueber `disabled_at`.
        $deaktivieren = (bool) $user->is_active;
        $user->is_active = $deaktivieren ? 0 : 1;
        $user->disabled_at = $deaktivieren ? now() : null;
        $user->save();

        if ($deaktivieren && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return response()->json([
            'ok'        => true,
            'is_active' => (int) $user->is_active,
            'msg'       => 'Status updated.',
        ]);
    }

    public function adminUsersToggleAdmin(User $user)
    {
        // FIX P0-06: Admin-Rechte umschalten darf NUR ein bestehender Admin.
        abort_unless((bool) auth()->user()->is_admin, 403, 'Nur Administratoren duerfen Admin-Rechte aendern.');

        if ($this->isSelf($user)) {
            return response()->json([
                'message' => 'You cannot change your own role here.',
            ], 422);
        }

        $user->is_admin = ((int) $user->is_admin === 1) ? 2 : 1;
        $user->save();

        return response()->json([
            'ok'       => true,
            'is_admin' => (int) $user->is_admin,
            'msg'      => 'Role updated.',
        ]);
    }

    public function adminUsersPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'ok'  => true,
            'msg' => 'Password updated successfully.',
        ]);
    }
}
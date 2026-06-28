<?php

namespace App\Http\Controllers\Product\Distributor;
use App\Http\Controllers\Controller;

use App\Models\Distributor;
use App\Models\DistributorDepartment;
use Illuminate\Http\Request;

class DistributorDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all departments for a distributor.
     */
    public function index(Request $request, Distributor $distributor)
    {
        $query = DistributorDepartment::where('d_id', $distributor->id);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('d_department', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('office', 'like', "%{$search}%")
                  ->orWhere('home', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $departments = $query
            ->orderBy('id', 'desc')
            ->paginate((int) $request->get('perPage', 25));

        return view('admin.product.distributor.distributor_department', [
            'distributor' => $distributor,
            'departments' => $departments,
        ]);
    }

    /**
     * Store one or multiple new departments for a distributor.
     * Only name is required.
     */
    public function store(Request $request, Distributor $distributor)
    {
        $validated = $request->validate(
            [
                'd'                  => 'required|array|min:1',
                'd.*.name'           => 'required|string|max:255',
                'd.*.d_department'   => 'nullable|string|max:255',
                'd.*.position'       => 'nullable|string|max:255',
                'd.*.email'          => 'nullable|email|max:255',
                'd.*.phone'          => 'nullable|string|max:50',
                'd.*.home'           => 'nullable|string|max:50',
                'd.*.office'         => 'nullable|string|max:50',
                'd.*.status'         => 'nullable|string|max:50',
            ],
            [
                'd.required'         => 'Mindestens ein Datensatz ist erforderlich.',
                'd.array'            => 'Ungültige Datenstruktur.',
                'd.*.name.required'  => 'Der Name / Ansprechpartner ist erforderlich.',
                'd.*.email.email'    => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            ]
        );

        foreach ($validated['d'] as $row) {
            DistributorDepartment::create([
                'd_id'         => $distributor->id,
                'name'         => $row['name'],
                'd_department' => $row['d_department'] ?? null,
                'position'     => $row['position'] ?? null,
                'email'        => $row['email'] ?? null,
                'phone'        => $row['phone'] ?? null,
                'home'         => $row['home'] ?? null,
                'office'       => $row['office'] ?? null,
                'status'       => $row['status'] ?? 'Unpublished',
            ]);
        }

        return back()->with('save_msg', 'Datensätze erfolgreich gespeichert!');
    }

    /**
     * Update a specific department.
     * Only name is required.
     */
    public function update(Request $request, Distributor $distributor, DistributorDepartment $department)
    {
        if ((int) $department->d_id !== (int) $distributor->id) {
            abort(404);
        }

        $validated = $request->validate(
            [
                'name'         => 'required|string|max:255',
                'd_department' => 'nullable|string|max:255',
                'position'     => 'nullable|string|max:255',
                'email'        => 'nullable|email|max:255',
                'phone'        => 'nullable|string|max:50',
                'home'         => 'nullable|string|max:50',
                'office'       => 'nullable|string|max:50',
                'status'       => 'nullable|string|max:50',
            ],
            [
                'name.required' => 'Der Name / Ansprechpartner ist erforderlich.',
                'email.email'   => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            ]
        );

        $department->update([
            'name'         => $validated['name'],
            'd_department' => $validated['d_department'] ?? null,
            'position'     => $validated['position'] ?? null,
            'email'        => $validated['email'] ?? null,
            'phone'        => $validated['phone'] ?? null,
            'home'         => $validated['home'] ?? null,
            'office'       => $validated['office'] ?? null,
            'status'       => $validated['status'] ?? ($department->status ?? 'Unpublished'),
        ]);

        return back()->with('update_msg', 'Datensatz erfolgreich aktualisiert!');
    }

    /**
     * Delete a specific department.
     */
    public function destroy(Distributor $distributor, DistributorDepartment $department)
    {
        if ((int) $department->d_id !== (int) $distributor->id) {
            abort(404);
        }

        $department->delete();

        return back()->with('delete_msg', 'Datensatz erfolgreich gelöscht!');
    }
}
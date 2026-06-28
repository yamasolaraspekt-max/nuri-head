<?php

namespace App\Http\Controllers\Branch;
use App\Http\Controllers\Controller;

use App\Models\Branch;
use App\Models\BranchAddress;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function rules(?int $branchId = null): array
    {
        return [
            'branch'         => ['required', 'string', 'max:255'],
            'initial'        => ['nullable', 'string', 'max:50'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:branches,slug' . ($branchId ? ',' . $branchId : '')],
            'chairman'       => ['nullable', 'exists:employees,id'],
            'contact_person' => ['nullable', 'exists:employees,id'],

            'street'         => ['nullable', 'string', 'max:255'],
            'postcode'       => ['nullable', 'string', 'max:50'],
            'city'           => ['nullable', 'string', 'max:150'],
            'country'        => ['nullable', 'string', 'max:150'],

            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:100'],
            'whatsapp'       => ['nullable', 'string', 'max:100'],
            'web'            => ['nullable', 'string', 'max:255'],

            'color'          => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'second_color'   => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],

            'employee_count' => ['nullable', 'integer', 'min:0'],
            'total_expense'  => ['nullable', 'numeric', 'min:0'],

            'bank'           => ['nullable', 'string', 'max:255'],
            'iban'           => ['nullable', 'string', 'max:100'],
            'bic'            => ['nullable', 'string', 'max:100'],
            'register'       => ['nullable', 'string', 'max:255'],
            'tax'            => ['nullable', 'string', 'max:255'],
            'vat'            => ['nullable', 'string', 'max:255'],

            'gf'             => ['nullable', 'string', 'max:255'],
            'logo_url'       => ['nullable', 'string', 'max:500'],
            'status'         => ['nullable', 'in:published,unpublished'],

            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ];
    }

    protected function normalizeData(array $validated, ?Branch $branch = null): array
    {
        $validated['branch'] = trim($validated['branch']);
        $validated['initial'] = !empty($validated['initial'])
            ? mb_strtoupper(trim($validated['initial']))
            : mb_strtoupper(mb_substr($validated['branch'], 0, 2));

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['branch']);

        $validated['color'] = $validated['color'] ?? ($branch->color ?? '#93c21c');
        $validated['second_color'] = $validated['second_color'] ?? ($branch->second_color ?? '#1f2937');
        $validated['status'] = $validated['status'] ?? ($branch->status ?? 'published');

        if (!empty($validated['web']) && !preg_match('~^https?://~i', $validated['web'])) {
            $validated['web'] = 'https://' . ltrim($validated['web'], '/');
        }

        return $validated;
    }

    protected function handleImageUpload(Request $request, array &$data, ?Branch $branch = null): void
    {
        if ($request->hasFile('image')) {
            if ($branch && $branch->image && Storage::disk('public')->exists('branches/' . $branch->image)) {
                Storage::disk('public')->delete('branches/' . $branch->image);
            }

            $file = $request->file('image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('branches', $fileName, 'public');
            $data['image'] = $fileName;
        }
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $query = Branch::query()->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('branch', 'like', "%{$search}%")
                    ->orWhere('initial', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('postcode', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('web', 'like', "%{$search}%")
                    ->orWhere('bank', 'like', "%{$search}%")
                    ->orWhere('iban', 'like', "%{$search}%")
                    ->orWhere('bic', 'like', "%{$search}%")
                    ->orWhere('register', 'like', "%{$search}%")
                    ->orWhere('tax', 'like', "%{$search}%")
                    ->orWhere('vat', 'like', "%{$search}%")
                    ->orWhere('gf', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(12);

        $employees = Employee::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get(['id', 'name', 'lastname', 'image', 'gender']);

        $employeeMap = $employees->mapWithKeys(function ($employee) {
            return [$employee->id => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))];
        });

        return view('admin.branch.branch', [
            'data'        => $data,
            'employees'   => $employees,
            'employeeMap' => $employeeMap,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated = $this->normalizeData($validated);

        $this->handleImageUpload($request, $validated);

        Branch::create($validated);

        return redirect()
            ->route('branch.info')
            ->with('save_msg', 'Die Filiale wurde erfolgreich gespeichert.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:branches,id'],
        ]);

        $branch = Branch::findOrFail($request->id);

        $validated = $request->validate($this->rules($branch->id));
        $validated = $this->normalizeData($validated, $branch);

        unset($validated['id']);

        $this->handleImageUpload($request, $validated, $branch);

        $branch->update($validated);

        return redirect()
            ->route('branch.info')
            ->with('update_msg', 'Die Filiale wurde erfolgreich aktualisiert.');
    }

    public function chartIndex()
    {
        $branches = Branch::query()
            ->orderBy('branch')
            ->get(['id', 'branch', 'initial', 'color', 'second_color', 'status']);

        return response()->json([
            'success'  => true,
            'branches' => $branches,
        ]);
    }

    public function branchStore(Request $request)
    {
        $validated = $request->validate([
            'branch'       => ['required', 'string', 'max:255'],
            'initial'      => ['nullable', 'string', 'max:50'],
            'color'        => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'second_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        $validated = $this->normalizeData($validated);

        $branch = Branch::create([
            'branch'       => $validated['branch'],
            'initial'      => $validated['initial'],
            'slug'         => $validated['slug'],
            'color'        => $validated['color'],
            'second_color' => $validated['second_color'],
            'status'       => 'published',
        ]);

        return response()->json([
            'success' => true,
            'branch'  => $branch,
            'message' => 'Filiale erfolgreich erstellt.',
        ]);
    }

    public function branchDestroy(Branch $branch)
    {
        if ($branch->image && Storage::disk('public')->exists('branches/' . $branch->image)) {
            Storage::disk('public')->delete('branches/' . $branch->image);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Filiale erfolgreich gelöscht.',
        ]);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        if ($branch->image && Storage::disk('public')->exists('branches/' . $branch->image)) {
            Storage::disk('public')->delete('branches/' . $branch->image);
        }

        $branch->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Die Filiale wurde erfolgreich gelöscht.');
    }

    public function profile($id)
    {
        $branch = Branch::findOrFail($id);

        $employee = Employee::select('id', 'name', 'lastname', 'image', 'gender')
            ->where('status', 'Active')
            ->get();

        $branchEmployees = Employee::select('id', 'name', 'lastname', 'image', 'gender', 'email', 'phone', 'status')
            ->where('status', 'Active')
            ->where(function ($q) use ($branch) {
                $q->where('branch', $branch->id)
                    ->orWhere('branch', $branch->initial);
            })
            ->get();

        $addresses = BranchAddress::where('branch_id', $id)
            ->orderByDesc('id')
            ->get();

        return view('admin.branch.profile', [
            'data'            => $branch,
            'employee'        => $employee,
            'branchEmployees' => $branchEmployees,
            'addresses'       => $addresses,
        ]);
    }

    public function create()
    {
        $employee = Employee::where('status', 'Active')->get();

        return view('admin.branch.branch_create', compact('employee'));
    }

    public function active($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->status = $branch->status === 'published' ? 'unpublished' : 'published';
        $branch->save();

        return back()->with('update_msg', 'Der Status wurde auf "' . $branch->status . '" geändert.');
    }


    public function offerIndex()
    {
        $branches = Branch::all()->keyBy('slug'); // e.g., 'solar-aspekt', 'werk-studio'
        return response()->json(['success' => true, 'branches' => $branches]);
    }

    public function offerUpdate(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $data = $request->except(['logo_file']);

        // Handle Logo Upload
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store in public/logo folder (matching your current setup)
            $file->move(public_path('logo'), $filename);
            
            // Update the database field with the new path
            $data['logoUrl'] = asset('logo/' . $filename);
        }

        $branch->update($data);

        return response()->json([
            'success' => true, 
            'message' => 'Filiale erfolgreich aktualisiert',
            'branch' => $branch
        ]);
    }
}
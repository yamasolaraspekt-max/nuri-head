<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,update')->only(['update', 'active', 'deactive']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    /**
     * Feiertage anzeigen
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $data = Holiday::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('year', 'like', "%{$search}%")
                        ->orWhere('holiday', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('year')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employee.holiday.holiday', compact('data', 'search'));
    }

    /**
     * Feiertag speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'year' => ['required', 'integer', 'digits:4', 'unique:holidays,year'],
                'holiday' => ['required', 'string'],
            ],
            [
                'year.required' => 'Das Jahr ist erforderlich.',
                'year.integer' => 'Das Jahr muss eine Zahl sein.',
                'year.digits' => 'Das Jahr muss aus 4 Ziffern bestehen.',
                'year.unique' => 'Für dieses Jahr existiert bereits ein Feiertagseintrag.',
                'holiday.required' => 'Die Feiertage sind erforderlich.',
            ]
        );

        Holiday::create([
            'year' => $validated['year'],
            'holiday' => $validated['holiday'],
            'status' => 'Not published',
        ]);

        return redirect()
            ->back()
            ->with('save_msg', 'Die Feiertage wurden erfolgreich hinzugefügt.');
    }

    /**
     * Feiertag aktualisieren
     */
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'id' => ['required', 'exists:holidays,id'],
                'year' => ['required', 'integer', 'digits:4', 'unique:holidays,year,' . $request->id],
                'holiday' => ['required', 'string'],
            ],
            [
                'id.required' => 'Der Datensatz ist ungültig.',
                'id.exists' => 'Der Datensatz wurde nicht gefunden.',
                'year.required' => 'Das Jahr ist erforderlich.',
                'year.integer' => 'Das Jahr muss eine Zahl sein.',
                'year.digits' => 'Das Jahr muss aus 4 Ziffern bestehen.',
                'year.unique' => 'Für dieses Jahr existiert bereits ein Feiertagseintrag.',
                'holiday.required' => 'Die Feiertage sind erforderlich.',
            ]
        );

        $holiday = Holiday::findOrFail($validated['id']);

        $holiday->update([
            'year' => $validated['year'],
            'holiday' => $validated['holiday'],
            'status' => $holiday->status ?? 'Not published',
        ]);

        return redirect()
            ->back()
            ->with('save_msg', 'Die Feiertage wurden erfolgreich aktualisiert.');
    }

    /**
     * Feiertagsjahr aktivieren
     *
     * Nur ein Datensatz darf aktiv/published sein.
     */
    public function active($id)
    {
        $holiday = Holiday::findOrFail($id);

        $alreadyPublished = Holiday::where('status', 'Published')
            ->where('id', '!=', $holiday->id)
            ->exists();

        if ($alreadyPublished) {
            return redirect()
                ->back()
                ->with('delete_msg', 'Es ist bereits ein Feiertagsjahr aktiviert.');
        }

        $holiday->update([
            'status' => 'Published',
        ]);

        return redirect()
            ->to('holiday_view')
            ->with('save_msg', 'Der Datensatz wurde erfolgreich aktiviert.');
    }

    /**
     * Feiertagsjahr deaktivieren
     */
    public function deactive($id)
    {
        $holiday = Holiday::findOrFail($id);

        $holiday->update([
            'status' => 'Unpublished',
        ]);

        return redirect()
            ->to('holiday_view')
            ->with('save_msg', 'Der Datensatz wurde erfolgreich deaktiviert.');
    }

    /**
     * Feiertag löschen
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Die Feiertage wurden erfolgreich gelöscht.');
    }
}
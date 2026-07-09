<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,update')->only(['update']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    /**
     * Länder anzeigen.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $data = Country::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('country', 'like', "%{$search}%")
                        ->orWhere('nationality', 'like', "%{$search}%");
                });
            })
            ->orderBy('country', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employee.country.country', [
            'data' => $data,
            'search' => $search,
        ]);
    }

    /**
     * Neues Land speichern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'country' => ['required', 'array', 'min:1'],
                'country.*.country' => ['required', 'string', 'max:255'],
                'country.*.nationality' => ['required', 'string', 'max:255'],
            ],
            [
                'country.required' => 'Bitte geben Sie mindestens ein Land ein.',
                'country.*.country.required' => 'Das Land ist erforderlich.',
                'country.*.nationality.required' => 'Die Staatsangehörigkeit ist erforderlich.',
                'country.*.country.max' => 'Das Land darf maximal 255 Zeichen lang sein.',
                'country.*.nationality.max' => 'Die Staatsangehörigkeit darf maximal 255 Zeichen lang sein.',
            ]
        );

        foreach ($validated['country'] as $item) {
            Country::create([
                'country' => $item['country'],
                'nationality' => $item['nationality'],
            ]);
        }

        return redirect()
            ->back()
            ->with('save_msg', 'Das Land wurde erfolgreich hinzugefügt.');
    }

    /**
     * Land aktualisieren.
     */
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'id' => ['required', 'integer', 'exists:countries,id'],
                'country' => ['required', 'string', 'max:255'],
                'nationality' => ['required', 'string', 'max:255'],
            ],
            [
                'id.required' => 'Die ID ist erforderlich.',
                'id.exists' => 'Das ausgewählte Land wurde nicht gefunden.',
                'country.required' => 'Das Land ist erforderlich.',
                'nationality.required' => 'Die Staatsangehörigkeit ist erforderlich.',
                'country.max' => 'Das Land darf maximal 255 Zeichen lang sein.',
                'nationality.max' => 'Die Staatsangehörigkeit darf maximal 255 Zeichen lang sein.',
            ]
        );

        $country = Country::findOrFail($validated['id']);

        $country->update([
            'country' => $validated['country'],
            'nationality' => $validated['nationality'],
        ]);

        return redirect()
            ->back()
            ->with('save_msg', 'Das Land wurde erfolgreich aktualisiert.');
    }

    /**
     * Land löschen.
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Das Land wurde erfolgreich gelöscht.');
    }
}
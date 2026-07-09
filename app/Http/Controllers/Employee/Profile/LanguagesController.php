<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Languages;
use Illuminate\Http\Request;

class LanguagesController extends Controller
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
     * Sprachenliste anzeigen
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $data = Languages::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('language', 'like', "%{$search}%");
            })
            ->orderBy('language', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employee.employee_language.emp_lang', [
            'data' => $data,
            'search' => $search,
        ]);
    }

    /**
     * Sprachen für AJAX laden
     */
    public function view()
    {
        $languages = Languages::query()
            ->orderBy('language', 'asc')
            ->get(['id', 'language']);

        return response()->json($languages);
    }

    /**
     * Eine Sprache per AJAX speichern
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'max:255', 'unique:languages,language'],
        ], [
            'language.required' => 'Die Sprache ist erforderlich.',
            'language.unique' => 'Diese Sprache existiert bereits.',
        ]);

        $language = Languages::create([
            'language' => $validated['language'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Die Sprache wurde erfolgreich gespeichert.',
            'language' => $language,
        ]);
    }

    /**
     * Eine oder mehrere Sprachen speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'language' => ['required', 'array', 'min:1'],
            'language.*.language' => ['required', 'string', 'max:255'],
        ], [
            'language.required' => 'Bitte geben Sie mindestens eine Sprache ein.',
            'language.*.language.required' => 'Die Sprache ist erforderlich.',
        ]);

        foreach ($validated['language'] as $item) {
            Languages::updateOrCreate(
                ['language' => trim($item['language'])],
                ['language' => trim($item['language'])]
            );
        }

        return redirect()
            ->back()
            ->with('save_msg', 'Die Sprache wurde erfolgreich hinzugefügt!');
    }

    /**
     * Sprache aktualisieren
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:languages,id'],
            'language' => ['required', 'string', 'max:255', 'unique:languages,language,' . $request->input('id')],
        ], [
            'id.required' => 'Die Sprache wurde nicht gefunden.',
            'id.exists' => 'Die Sprache wurde nicht gefunden.',
            'language.required' => 'Die Sprache ist erforderlich.',
            'language.unique' => 'Diese Sprache existiert bereits.',
        ]);

        $language = Languages::findOrFail($validated['id']);
        $language->language = trim($validated['language']);
        $language->save();

        return redirect()
            ->back()
            ->with('save_msg', 'Die Sprache wurde erfolgreich aktualisiert!');
    }

    /**
     * Sprache löschen
     */
    public function destroy($id)
    {
        $language = Languages::findOrFail($id);
        $language->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Die Sprache wurde erfolgreich gelöscht!');
    }
}
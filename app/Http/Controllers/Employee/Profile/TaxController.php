<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Steuerklassen / Steuern anzeigen
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $data = Tax::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('tax', 'like', "%{$search}%")
                        ->orWhere('class', 'like', "%{$search}%")
                        ->orWhere('remark', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tax.tax', [
            'data' => $data,
            'search' => $search,
        ]);
    }

    /**
     * Neuen Steuereintrag speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ], [
            'tax.required' => 'Die Steuer ist erforderlich.',
            'tax.string' => 'Die Steuer muss ein gültiger Text sein.',
            'tax.max' => 'Die Steuer darf maximal 255 Zeichen enthalten.',

            'class.string' => 'Die Klasse muss ein gültiger Text sein.',
            'class.max' => 'Die Klasse darf maximal 255 Zeichen enthalten.',

            'remark.string' => 'Die Bemerkung muss ein gültiger Text sein.',
            'remark.max' => 'Die Bemerkung darf maximal 1000 Zeichen enthalten.',
        ]);

        Tax::create($validated);

        return redirect()
            ->route('tax.info')
            ->with('save_msg', 'Der Steuereintrag wurde erfolgreich gespeichert.');
    }

    /**
     * Steuereintrag aktualisieren
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:taxes,id'],
            'tax' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ], [
            'id.required' => 'Die Datensatz-ID fehlt.',
            'id.exists' => 'Der ausgewählte Steuereintrag wurde nicht gefunden.',

            'tax.required' => 'Die Steuer ist erforderlich.',
            'tax.string' => 'Die Steuer muss ein gültiger Text sein.',
            'tax.max' => 'Die Steuer darf maximal 255 Zeichen enthalten.',

            'class.string' => 'Die Klasse muss ein gültiger Text sein.',
            'class.max' => 'Die Klasse darf maximal 255 Zeichen enthalten.',

            'remark.string' => 'Die Bemerkung muss ein gültiger Text sein.',
            'remark.max' => 'Die Bemerkung darf maximal 1000 Zeichen enthalten.',
        ]);

        $tax = Tax::findOrFail($validated['id']);

        $tax->update([
            'tax' => $validated['tax'],
            'class' => $validated['class'] ?? null,
            'remark' => $validated['remark'] ?? null,
        ]);

        return redirect()
            ->route('tax.info')
            ->with('save_msg', 'Der Steuereintrag wurde erfolgreich aktualisiert.');
    }

    /**
     * Steuereintrag löschen
     */
    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Der Steuereintrag wurde erfolgreich gelöscht.');
    }
}
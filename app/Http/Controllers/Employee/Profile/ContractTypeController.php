<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;

class ContractTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vertragsarten anzeigen.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $data = ContractType::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('contract_type', 'like', "%{$search}%")
                        ->orWhere('contract_duration', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employee.contractType.contract_type', [
            'data' => $data,
        ]);
    }

    /**
     * Neue Vertragsart speichern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_type' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
        ], [
            'contract_type.required' => 'Bitte geben Sie eine Vertragsart ein.',
            'contract_type.max' => 'Die Vertragsart darf maximal 255 Zeichen lang sein.',
            'duration.max' => 'Die Laufzeit darf maximal 255 Zeichen lang sein.',
        ]);

        ContractType::create([
            'contract_type' => $validated['contract_type'],
            'contract_duration' => $validated['duration'] ?? null,
        ]);

        return redirect()
            ->back()
            ->with('save_msg', 'Vertragsart wurde erfolgreich gespeichert.');
    }

    /**
     * Vertragsart aktualisieren.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'exists:contract_types,id'],
            'contract_type' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
        ], [
            'id.required' => 'Die Vertragsart-ID fehlt.',
            'id.exists' => 'Die ausgewählte Vertragsart wurde nicht gefunden.',
            'contract_type.required' => 'Bitte geben Sie eine Vertragsart ein.',
            'contract_type.max' => 'Die Vertragsart darf maximal 255 Zeichen lang sein.',
            'duration.max' => 'Die Laufzeit darf maximal 255 Zeichen lang sein.',
        ]);

        $contractType = ContractType::findOrFail($validated['id']);

        $contractType->update([
            'contract_type' => $validated['contract_type'],
            'contract_duration' => $validated['duration'] ?? null,
        ]);

        return redirect()
            ->route('contract.type.info')
            ->with('update_msg', 'Vertragsart wurde erfolgreich aktualisiert.');
    }

    /**
     * Vertragsart löschen.
     */
    public function destroy($id)
    {
        $contractType = ContractType::findOrFail($id);
        $contractType->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Vertragsart wurde erfolgreich gelöscht.');
    }
}
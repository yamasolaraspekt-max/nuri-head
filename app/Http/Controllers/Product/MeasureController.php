<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Measure;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Maßeinheiten anzeigen.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $data = Measure::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('measure', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product.product.measure', compact('data'));
    }

    /**
     * Neue Maßeinheit speichern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'measure' => ['required', 'string', 'max:255'],
            ],
            [
                'measure.required' => 'Die Maßeinheit ist erforderlich.',
                'measure.string' => 'Die Maßeinheit muss ein gültiger Text sein.',
                'measure.max' => 'Die Maßeinheit darf maximal 255 Zeichen lang sein.',
            ]
        );

        Measure::create([
            'measure' => $validated['measure'],
        ]);

        return redirect()
            ->route('measure.info')
            ->with('save_msg', 'Die Maßeinheit wurde erfolgreich gespeichert.');
    }

    /**
     * Maßeinheit aktualisieren.
     */
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'id' => ['required', 'integer', 'exists:measures,id'],
                'measure' => ['required', 'string', 'max:255'],
            ],
            [
                'id.required' => 'Die Datensatz-ID fehlt.',
                'id.exists' => 'Die ausgewählte Maßeinheit wurde nicht gefunden.',
                'measure.required' => 'Die Maßeinheit ist erforderlich.',
                'measure.string' => 'Die Maßeinheit muss ein gültiger Text sein.',
                'measure.max' => 'Die Maßeinheit darf maximal 255 Zeichen lang sein.',
            ]
        );

        $measure = Measure::findOrFail($validated['id']);

        $measure->update([
            'measure' => $validated['measure'],
        ]);

        return redirect()
            ->route('measure.info')
            ->with('save_msg', 'Die Maßeinheit wurde erfolgreich aktualisiert.');
    }

    /**
     * Maßeinheit löschen.
     */
    public function destroy($id)
    {
        $measure = Measure::findOrFail($id);
        $measure->delete();

        return redirect()
            ->route('measure.info')
            ->with('delete_msg', 'Die Maßeinheit wurde erfolgreich gelöscht.');
    }
}
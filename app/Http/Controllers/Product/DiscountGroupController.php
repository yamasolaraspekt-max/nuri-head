<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\DiscountGroup;
use Illuminate\Http\Request;

class DiscountGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Rabattgruppen anzeigen.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $data = DiscountGroup::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('discount_group', 'like', '%' . $search . '%')
                        ->orWhere('discount', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product.discount_group.discount_group', compact('data'));
    }

    /**
     * Neue Rabattgruppe speichern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'discount_group' => ['required', 'string', 'max:255'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'discount_group.required' => 'Die Rabattgruppe ist erforderlich.',
            'discount_group.string' => 'Die Rabattgruppe muss ein gültiger Text sein.',
            'discount_group.max' => 'Die Rabattgruppe darf maximal 255 Zeichen lang sein.',

            'discount.required' => 'Der Rabatt ist erforderlich.',
            'discount.numeric' => 'Der Rabatt muss eine Zahl sein.',
            'discount.min' => 'Der Rabatt darf nicht kleiner als 0 sein.',
            'discount.max' => 'Der Rabatt darf nicht größer als 100 sein.',
        ]);

        DiscountGroup::create($validated);

        return redirect()
            ->route('discount_group.info')
            ->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert.');
    }

    /**
     * Rabattgruppe aktualisieren.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:discount_groups,id'],
            'discount_group' => ['required', 'string', 'max:255'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'id.required' => 'Die Datensatz-ID fehlt.',
            'id.exists' => 'Der Datensatz wurde nicht gefunden.',

            'discount_group.required' => 'Die Rabattgruppe ist erforderlich.',
            'discount_group.string' => 'Die Rabattgruppe muss ein gültiger Text sein.',
            'discount_group.max' => 'Die Rabattgruppe darf maximal 255 Zeichen lang sein.',

            'discount.required' => 'Der Rabatt ist erforderlich.',
            'discount.numeric' => 'Der Rabatt muss eine Zahl sein.',
            'discount.min' => 'Der Rabatt darf nicht kleiner als 0 sein.',
            'discount.max' => 'Der Rabatt darf nicht größer als 100 sein.',
        ]);

        $discountGroup = DiscountGroup::findOrFail($validated['id']);

        $discountGroup->update([
            'discount_group' => $validated['discount_group'],
            'discount' => $validated['discount'],
        ]);

        return redirect()
            ->route('discount_group.info')
            ->with('save_msg', 'Der Datensatz wurde erfolgreich aktualisiert.');
    }

    /**
     * Rabattgruppe löschen.
     */
    public function destroy($id)
    {
        $discountGroup = DiscountGroup::findOrFail($id);
        $discountGroup->delete();

        return redirect()
            ->back()
            ->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht.');
    }
}
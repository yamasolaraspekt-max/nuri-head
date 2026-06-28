<?php

namespace App\Http\Controllers\Phase;
use App\Http\Controllers\Controller;

use App\Models\PhaseSection;
use Illuminate\Http\Request;
use DB;
class PhaseSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected array $protectedDefaults = [
            'complete',
            'montage',
            'product',
            'plan',
            'maintenance',
            'repair',
            'reclaim',
            'others',
        ];

      public function ajaxUpdate(Request $request, PhaseSection $section)
    {
        // do NOT allow renaming defaults
        if (in_array($section->phase_section, $this->protectedDefaults, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Standard-Leistungen können nicht umbenannt werden.',
            ], 422);
        }

        $data = $request->validate([
            'phase_section' => 'required|string|max:255',
        ]);

        $section->phase_section = $data['phase_section'];
        $section->save();

        return response()->json([
            'success' => true,
            'id'      => $section->id,
            'name'    => $section->phase_section,
        ]);
    }

    public function ajaxDelete(PhaseSection $section)
    {
        // do NOT allow deleting defaults
        if (in_array($section->phase_section, $this->protectedDefaults, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Standard-Leistungen können nicht gelöscht werden.',
            ], 422);
        }

        $section->delete();

        return response()->json([
            'success' => true,
            'id'      => $section->id,
        ]);
    }

    public function byProduct($productId)
    {
        $services = DB::table('phase_sections')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->orderBy('phase_section', request('sort', 'asc') === 'desc' ? 'desc' : 'asc')
            ->get()
            ->map(function ($row) {
                $row->label = match ($row->phase_section) {
                    'complete'    => 'Komplettlösung',
                    'montage'     => 'Montage',
                    'product'     => 'Produkt',
                    'plan'        => 'Planung',
                    'maintenance' => 'Wartung',
                    'repair'      => 'Reparatur',
                    'reclaim'     => 'Reklamation',
                    'others'      => 'Sonstiges',
                    default       => $row->phase_section,
                };
                $row->created_at_formatted = optional($row->created_at)->format('d.m.Y');
                return $row;
            });

        return response()->json([
            'success'  => true,
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'    =>  'required|exists:article_groups,id',
            'phase_section' => 'required',
            'status'   =>   'nullable'
        ]);

        $data = PhaseSection::create([
            'product_id'    =>  $request->product_id,
            'phase_section' =>  $request->phase_section,
            'status'   =>   'Published'
        ]);

        return redirect()->back()->with('save_msg', 'Der Datensatz wird erfugriesh gespeichert');
    }

    /**
     * Display the specified resource.
     */
    public function show(PhaseSection $phaseSection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhaseSection $phaseSection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'    =>  'required|exists:phase_sections,id',
            'product_id'    =>  'required|exists:article_groups,id',
            'phase_section' =>  'required|string',
            'status'   =>   'nullable'
        ]);

        $data = PhaseSection::find($request->id)->update([
            'product_id'    =>  $request->product_id,
            'phase_section' =>  $request->phase_section,
            'status'   =>   'Published'
        ]);
        return redirect()->back()->with('save_msg', 'Task Saved');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $data = PhaseSection::find($id);

        if ($data) {
            $data->delete();
            return redirect()->back()->with('success', 'Anfrage erfolgreich gelöscht');
        }


        return redirect()->back()->with('error', 'Anfrage nicht gefunden');
    }

        public function restore($id)
    {
        $data = PhaseSection::withTrashed()->find($id);

        if ($data) {
            $data->restore(); // Restores the soft-deleted record
            return redirect()->back()->with('save_msg', 'Anfrage erfolgreich wiederhergestellt');
        }

        return redirect()->back()->with('error', 'Anfrage nicht gefunden');
    }
}

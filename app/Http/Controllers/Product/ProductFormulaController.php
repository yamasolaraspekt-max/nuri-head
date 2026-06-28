<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ProductFormula;
use App\Models\ArticleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductFormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $search = $request->query('search');
    
        $products = ArticleGroup::with([
            'formulas' => function ($query) use ($search) {
                $query->whereNull('deleted_at')
                      ->with(['creator', 'deleter', 'editor']); // eager load employees
                if ($search) {
                    $query->where('section_name', 'LIKE', "%{$search}%");
                }
            }
        ])->get();
        
    
        return view('admin.formula.list', [
            'products' => $products,
        ]);
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $product_id)
    {
        $data['formula'] = ProductFormula::findOrFail($id);
        $data['product_id'] = ArticleGroup::findOrFail($product_id);
        return view('admin.formula.create', $data);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $formula = ProductFormula::create([
                'product_id' => $request->product_id,
                'section_name' => $request->section_name ?? 'Unbenannte Checkliste',
                'fields' => $request->fields ?? json_encode([]),
                'created_by'  =>    auth()->user()->name, 
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Checkliste gespeichert.',
                'id' => $formula->id,
                'product_id' => $request->product_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Formel speichern fehlgeschlagen: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
    
    
    
    public function test($id)
    {
        $formula = ProductFormula::findOrFail($id);
        \Log::info('formula: ', [$formula]);
        return view('admin.formula.test', compact('formula'));
    }
    
    public function testSubmit(Request $request)
    {
        // Optional: validate or log test data
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
    {
        $formulas = ProductFormula::where('product_id', $product_id)->get();
        $products = ArticleGroup::all(); // assuming article groups = products

        return view('admin.formula.create', compact('formulas', 'products', 'product_id'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$product_id)
    {
        $formulas = ProductFormula::find($id);
        $products = ArticleGroup::find($product_id); 

        return view('admin.formula.create', compact('formulas', 'products', 'product_id', 'id'));

    }

    public function editFormula($id, $product_id)
    {
        $formulas = ProductFormula::find($id);
        $fields = json_decode($formulas->fields, true); // Decode the JSON string into an array
    
        return view('admin.formula.edit', compact('formulas', 'fields', 'id', 'product_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function save(Request $request)
    {
        \Log::info('requested data', $request->all());
        \Log::info('Formulas type:', ['is_array' => is_array($request->formulas)]);
    
        try {
            $request->validate([
                'id'    =>  'required|integer|exists:product_formulas,id',
                'product_id' => 'required|integer|exists:article_groups,id',
                'formulas'   => 'required|array',
                'formulas.*' => 'array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }
    
        $productId = $request->product_id;
        $formulasArr = $request->formulas;
    
        foreach ($formulasArr as $sectionName => $fieldsArray) {
            $existing = ProductFormula::find($request->id);
        
            if ($existing) {
                $existing->update([
                    'section_name'  => $sectionName,
                    'fields'        => json_encode($fieldsArray),
                    'edited_by'     =>  auth()->user()->name
                ]);
            } else {
                ProductFormula::create([
                    'product_id'    => $productId,
                    'section_name'  => $sectionName,
                    'fields'        => json_encode($fieldsArray),
                ]);
            }
        }
        
    
        return response()->json(['success' => true, 'message' => 'Form successfully saved.']);
    }
    
     
     
    public function updateFormula(Request $request)
    {
        \Log::info('requested data', $request->all());
        \Log::info('Formulas type:', ['is_array' => is_array($request->formulas)]);
    
        try {
            $request->validate([
                'id'    =>  'required|integer|exists:product_formulas,id',
                'product_id' => 'required|integer|exists:article_groups,id',
                'formulas'   => 'required|array',
                'formulas.*' => 'array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }
    
        $productId = $request->product_id;
        $formulasArr = $request->formulas;
    
        foreach ($formulasArr as $sectionName => $fieldsArray) {
            $existing = ProductFormula::find($request->id);
        
            if ($existing) {
                $existing->update([
                    'product_id' => $productId,
                    'section_name' => $sectionName,
                    'fields' => json_encode($fieldsArray),
                    'version'       => $existing->version + 1, 
                    'edited_by'     =>  auth()->user()->name

                ]);
            }
            break;
        }
        
    
        return response()->json(['success' => true, 'message' => 'Form successfully saved.']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
        ]);
    
        if (!\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'Ungültiges Passwort'], 403);
        }
    
        $formula = ProductFormula::findOrFail($id);
    
        $formula->deleted_by = auth()->user()->name;
        $formula->save(); // Save deleted_by before soft-deleting
    
        $formula->delete(); // soft delete
    
        return response()->json(['success' => true], 200);
    }
    
    
    public function getProduct($product_id)
    {
        $data = ProductFormula::where('product_id', $product_id)
                            ->where('status', 'Published')
                            ->get(); 

        \Log::info('requested Formula', $data);
        return view('admin.new_leads.checklists.checklist', compact('formula', 'product_id'));
    }


    public function loadChecklist($product_id)
    {
        $formulas = ProductFormula::where('product_id', $product_id)
                    ->where('status', 'Published')
                    ->orderBy('id')
                    ->get(['id', 'section_name', 'fields']); // keep it lightweight

        return response()->json($formulas);
    }


   


}

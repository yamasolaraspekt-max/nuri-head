<?php

namespace App\Http\Controllers\Product\Brand;
use App\Http\Controllers\Controller;

use App\Models\ExternalPersonal;
use Illuminate\Http\Request;
use DB;
use Illuminate\Validation\ValidationException;


class ExternalPersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function __construct(){
        $this->middleware('auth');
    }
     public function index()
    {
        $search=request()->query('search');

        if($search){
            $data['products'] = DB::table('article_groups')->get();
            $data['data']=DB::table('external_personals')
                        ->where('company_name', 'LIKE', "%$search%")
                        ->where('admin_name', 'LIKE', "%$search%")
                        ->paginate(10);
                return view('admin.employee.external.external', $data);
        }
        else{
            
            $data['products'] = DB::table('article_groups')->get();
            $data['data']=DB::table('external_personals') 
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            return view('admin.employee.external.external', $data); 
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
{
    try {
        $request->validate([
            'company_name' => 'required',
            'price_type' => 'required',
            'price' => 'required',
            'tax_id' => 'required',
            'product_id' => 'required|exists:article_groups,id',
            'email' => 'required|email',
            'phone' => 'required',
            'admin_name' => 'required',
        ]);

        // Log the request data for debugging
        \Log::info('Request data:', $request->all());

        ExternalPersonal::create($request->all());

        return redirect()->back()->with('save_msg', 'Das Unternehmen wurde erfolgreich gespeichert.');
        
    } catch (ValidationException $e) { 
        \Log::error('Validation error:', $e->errors());
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        \Log::error('Error during save:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error_msg', 'An error occurred while saving. Please try again.');
    }
}


    /**
     * Display the specified resource.
     */
    public function show(ExternalPersonal $externalPersonal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExternalPersonal $externalPersonal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExternalPersonal $externalPersonal)
    {
         $request->validate([
            'company_name' => 'required',
            'price_type' => 'required',
            'price' => 'required',
            'tax_id' => 'required',
            'product_id' => 'required|exists:article_groups,id',
            'email' => 'required',
            'phone' => 'required',
            'admin_name' => 'required', 

        ]);

        ExternalPersonal::find($request->id)->update($request->all());

        return redirect()->back()->with('save_msg', 'Das Unternehmen hat erfolgreich');
    }

    public function publish($id){
        $data = ExternalPersonal::find($id);
        $data->status="Published";
        $data->save();
        return redirect()->back()->with('save_msg', 'Das Unternehmen ist aktiv geworden');

    }   

       public function unpublish($id){
        $data = ExternalPersonal::find($id);
        $data->status="Unpublished";
        $data->save();
        return redirect()->back()->with('save_msg', 'Das Unternehmen ist inaktiv geworden');

    }  
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = ExternalPersonal::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Das Unternehmen hat erfolgreich gelöscht');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ArticleGroup;
use App\Models\Customer; 
use App\Models\CustomerProduct;
use App\Models\Employee;
use App\Models\HeatingType;
use App\Models\Image;
use App\Models\Product;
use App\Models\SubArticleGroup;
use Illuminate\Http\Request;
use DB;
use App\Models\CustomerCart;
use App\Models\ImageCategory;
use App\Models\CustomerAlternativeAdd;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use App\Models\RadiatorInstallation;  
use App\Models\CustomerHeatingCircuit; 
use App\Models\CustomerRoomDimension; 


class CustomerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($id, $postcode, $address_no)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $item['customer'] = $customer;
        } else {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        $item['product'] = Product::all();

        try {
            // Retrieve selected products for the customer
            $item['selectedProducts'] = DB::table('customer_product_lists')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                ->select('article_groups.id as product_id')
                ->where('customers.id', $id)
                ->pluck('product_id')
                ->toArray();

            // Retrieve all articles/products to display
            $item['articles'] = ArticleGroup::all();
        } catch (\Exception $e) {
            $item['selectedProducts'] = [];
            \Log::error('Error fetching selected products: ' . $e->getMessage());
        }

        $item['sub_article'] = SubArticleGroup::all();
        $item['images'] = Image::where('customer_id', $id)->where('address_no', $address_no)->get();
     // Fetch the images and join with article_groups
        $images = DB::table('article_groups')
            ->join('images', 'images.article_group', '=', 'article_groups.id')
            ->where('images.customer_id', $id)
            ->where('images.address_no', $address_no)
            ->select('images.*', 'article_groups.article_group')
            ->get(); 
        // Group images by article_group
        $groupedImages = $images->groupBy('article_group'); 
        // Convert the grouped data into an array format (if needed for JSON response or further processing)
        $groupedImagesArray = $groupedImages->map(function ($group) {
            return $group->toArray();
        })->toArray();

        // Now $groupedImagesArray is structured by article_group with associated image data
        $item['image_p_sort'] = $groupedImagesArray;
        
   
          // Fetch images where stage is 'customer'
            $customerImages = DB::table('images')
                ->where('customer_id', $id)
                ->where('address_no', $address_no)
                ->where('stage', 'customer')
                ->select('images.*')
                ->get();

            // Organize images under the 'customer' key
            $item['image_c_sort'] = [
                'customer' => $customerImages->toArray() // Convert to array for easy handling in Blade
            ];
 

              // Fetch images where stage is 'montage'
            $customerImages = DB::table('images')
                ->where('customer_id', $id)
                ->where('address_no', $address_no)
                ->where('stage', 'montage')
                ->select('images.*')
                ->get();

            // Organize images under the 'customer' key
            $item['image_m_sort'] = [
                'montage' => $customerImages->toArray() // Convert to array for easy handling in Blade
            ];

              // Fetch images where stage is 'montage'
            $customerImages = DB::table('images')
                ->where('customer_id', $id)
                ->where('address_no', $address_no)
                ->where('stage', 'end')
                ->select('images.*')
                ->get();

            // Organize images under the 'customer' key
            $item['image_e_sort'] = [
                'end' => $customerImages->toArray() // Convert to array for easy handling in Blade
            ];
 
 
         

        Image::where('customer_id', $id)->where('address_no', $address_no)->get();
        
 
        $item['category'] = ImageCategory::all();
        $item['rediators'] = RadiatorInstallation::where('customer_id', $id)->where('postcode', $postcode)->get();
        
       

        // Initialize an empty array to hold the formatted data
        $item['image_category'] = [];

        try {
            // Retrieve data from the database
            $images = DB::table('images')
                ->join('image_categories', 'image_categories.id', '=', 'images.category_id')
                ->select('image_categories.category', 'images.*')
                ->where('images.customer_id', $id)
                ->get();

            // Iterate through the images and group them by category
            foreach ($images as $image) {
                // Check if the category already exists in the array
                if (!isset($item['image_category'][$image->category])) {
                    // Initialize the category array if it doesn't exist
                    $item['image_category'][$image->category] = [
                        'category_id' => $image->category_id,
                        'images' => []
                    ];
                }

                // Add the image to the category's image array
                $item['image_category'][$image->category]['images'][] = [
                    'id' => $image->id,
                    'customer_id' => $image->customer_id,
                    'image_name' => $image->image_name,
                    'image' => $image->image,
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching images: ' . $e->getMessage());
        }

        $item['tiles'] = DB::table('product_images')
            ->join('products', 'products.id', '=', 'product_images.product_id')
            ->select('product_images.*', 'products.category', 'products.product', 'products.roof_type')
            ->where('products.category', 'Dachziegel')
            ->get();

        $item['electro'] = DB::table('brands')
            ->where('purpose', 'ELEKTRO')
            ->get();

        $item['alternative'] = DB::table('customer_alternative_adds')
            ->where('customer_id', $id)
            ->where('postcode', $postcode)
            ->where('address_no', $address_no)
            ->first();
        $item['employees'] = Employee::select('name', 'lastname', 'id')->get();
        $item['heating_types'] = HeatingType::where('status', 'Published')->get();

   $item['customerReview'] = DB::table('customer_reviews')
                                ->join('employees', 'employees.id', 'customer_reviews.employee_id')
                                ->join('article_groups', 'article_groups.id', 'customer_reviews.product_id')
                                ->join('customers', 'customers.id', 'customer_reviews.customer_id') 
                                ->select('customer_reviews.*', 'employees.name', 'employees.lastname', 'article_groups.article_group'   ) 
                                ->where('customers.id', $id) 
                                ->get();
    $item['ReviewList'] = DB::table('customer_review_lists')
                                ->orderBy('id')
                                ->get();

    $wp = DB::table('w_p_checklists')
                            ->where('customer_id', $id)
                            ->where('postcode', $postcode)
                            ->first();

    if($wp){
       $item['wp']= $wp;
    }
    else{
        $item['wp']=null;
    }

    $item['cold_water'] = DB::table('customer_w_p_cables')
                        ->where('customer_id', $id)
                        ->where('postcode', $postcode)
                        ->where('system', '=', 'Kalt-Wasser')
                        ->first();

    $item['warm_water'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('postcode', $postcode)
            ->where('system', '=', 'Warm-Wasser')
            ->first();

    $item['circulation'] = DB::table('customer_w_p_cables')
                ->where('customer_id', $id)
                ->where('postcode', $postcode)
                ->where('system', '=', 'Zirkulation')
                ->first();

    $item['heating'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('postcode', $postcode)
            ->where('system', '=', 'Heizung')
            ->first();

    $item['meter_cabinet'] = DB::table('customer_meter_cabinets')
        ->where('customer_id', $id)
        ->where('postcode', $postcode)
        ->first();
        
   
   
   

        return view('admin.customer.customer_product_create', $item);
    }


    /**
     * Show the form for creating a new resource.
     */
   public function show($customer_id, $product_id, $address_no)
{
    $data['article'] = DB::table('customer_product_lists')
        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
        ->leftJoin('employees as contact', 'contact.id', '=', 'customers.contact_person')
        ->select('article_groups.initial', 'article_groups.article_group', 'article_groups.id as product_id', 'customers.id as customer_id', 'article_groups.image'
          
                )
        ->where('customer_product_lists.customer_id', '=', $customer_id)
        ->where('article_groups.id', '=', $product_id)
        ->first();

    if($data['article']->article_group == "Photovoltaik" || $data['article']->article_group == "PHOTOVOLTAIK"  || $data['article']->initial == "PV" ){

        $pv_checklist = DB::table('p_v_checklists')
        ->where('customer_id', '=', $customer_id)
        ->first();

        $pv_roof = DB::table('p_v_roofs')
            ->join('p_v_checklists', 'p_v_checklists.id', '=', 'p_v_roofs.pv_id')
            ->select('p_v_roofs.*')
            ->get();

        if($pv_checklist){
        $data['pv_checklist'] = $pv_checklist;
        $data['pv_roof'] = $pv_roof;

        }
       
        else{
            return redirect()->back()->with('delete_msg', 'The Configuration of PV Module is not saved yet');
        }

    }

    elseif($data['article']->article_group == "WÄRMEPUMPE" || $data['article']->article_group = "Heat Pump" || $data['article']->initial == "WP"){
        $data['wp_checklist'] = DB::table('w_p_checklists')
        ->where('customer_id', '=', $customer_id)
        ->first();
        if(!$data['wp_checklist']){
            return redirect()->back()->with('delete_msg', 'The Configuration of WP Module is not saved yet');

        }
        $data['wp_heating_circuit']= CustomerHeatingCircuit::where('customer_id', $customer_id)->get();
        $data['room_dimention']=CustomerRoomDimension::where('customer_id', $customer_id)->get();
    }
    else {
        return redirect()->back()->with('delete_msg', 'The Product is not Selected and filled');
    }
  
   $data['electro'] = DB::table('brands')
            ->where('purpose', 'ELEKTRO')
            ->get();

    $data['heating'] = DB::table('customer_w_p_cables')
        ->where('customer_id', $customer_id)  
        ->get();

    $data['meter_cabinet'] = DB::table('customer_meter_cabinets')
        ->where('customer_id', $customer_id) 
        ->first();

    $data['article_icon'] = DB::table('customer_product_lists')
        ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
        ->select('customer_product_lists.*', 'article_groups.initial', 'article_groups.article_group')
        ->where('customer_product_lists.customer_id', '=', $customer_id)
        ->get();

    $data['customer'] = DB::table('customers')
            ->leftJoin('employees as contact', 'contact.id', '=', 'customers.contact_person')
            ->select('customers.*',   'contact.name as cname', 'contact.lastname as clastname', 'contact.image as cimage')
            ->where('customers.id', $customer_id)
            ->first();
    $data['alternative'] = DB::table('customer_alternative_adds')
        ->join('customer_product_lists', 'customer_product_lists.customer_id', '=', 'customer_alternative_adds.customer_id')
        ->where('customer_alternative_adds.customer_id', $customer_id)
        ->where('customer_product_lists.product_id', $product_id)
        ->where('customer_alternative_adds.address_no', $address_no)
        ->first();

    $data['tiles'] = DB::table('products')
        ->join('product_images', 'product_images.product_id', '=', 'products.id')
        ->select('products.*', 'product_images.image')
        ->where('products.category', '=', 'Dachziegel')
        ->get();

   
    $data['image_category'] = DB::table('image_categories')->get();
 
    $data['phases'] = TaskPhase::with('articleGroup') 
                    ->join('customer_phase_lists', 'customer_phase_lists.phase_id', '=', 'task_phases.id')
                    ->where('customer_phase_lists.customer', $customer_id)
                    ->where('product_id', $product_id) 
                    ->select('task_phases.*')
                    ->orderBy('order', 'asc')
                    ->get();

 
    $data['stages'] = DB::table('customer_phase_stages')   
                        ->join('customers', 'customers.id', '=', 'customer_phase_stages.customer_id')           
                        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_stages.phase_id') 
                        ->where('customer_phase_stages.customer_id', $customer_id)
                        ->where('task_phases.product_id', $product_id) 
                        ->select('customer_phase_stages.*')
                        ->get(); 

$data['task_docs'] = DB::table('task_documents')
    ->leftJoin('task_to_dos', 'task_to_dos.customer_id', '=', 'task_documents.customer_id')
    ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
    ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
    ->select('task_documents.id', 'task_documents.customer_id', 'task_documents.phase_id', 'task_documents.product_id', 'task_documents.activities_id', 
        'task_documents.document_name', 'task_documents.document', 'task_documents.document_sum', 
        'task_documents.document_note', 'task_documents.document_status', 'task_documents.created_at', 
        'task_documents.updated_at', 'contact.name as cname', 'contact.lastname as clastname', 
        'contact.image as cimage', 'responsible.name as rname', 'responsible.lastname as rlastname', 
        'responsible.image as rimage')
    ->where('task_documents.customer_id', $customer_id)
    ->where('task_documents.product_id', $product_id)
    ->distinct('task_documents.document')
    ->get();

    $data['employees'] = DB::table('employees')->where('status', 'Active')->get();
    $data['outside'] = DB::table('external_personals')->where('status', '=', 'Published')->get();
    $data['tasks'] = DB::table('phase_activities')->where('product_id', $product_id)->get(); 
    
 
    $data['activities'] = DB::table('task_sub_tasks')->where('status','=', 'published')->get();                        
    $data['current_user'] = DB::table('employees')->where('id','=', auth()->user()->name)->select('id', 'name', 'lastname', 'image')->first();                        
    $data['to_does'] = DB::table('task_to_dos')
             ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
             ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person') 
             ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service') 
             ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company') 
            ->where('customer_id', '=', $customer_id)
            ->where('address_no', '=', $address_no)
            ->select('task_to_dos.*', 'responsible.name as rname', 'responsible.lastname as rlastname', 'responsible.image as rimage',
                        'contact.name as cname', 'contact.lastname as clastname', 'contact.image as cimage', 
                        'outside_s.name as osname', 'outside_s.lastname as oslastname', 'outside_s.image as osimage', 
                        'outside_c.admin_name', 'outside_c.company_name' 
                        
                    )
            ->get();

 
  

    return view('admin.customer.products_details.product_details', $data);
}


   public function edit($customer_id, $product_id, $address_no)
    {
        $data = [];

        $data['article'] = DB::table('customer_product_lists')
            ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
            ->select('article_groups.initial', 'article_groups.article_group', 'article_groups.id as product_id', 'customers.id as customer_id', 'article_groups.image')
            ->where('customer_id', '=', $customer_id)
            ->where('article_groups.id', '=', $product_id)
            ->first();

        $data['article_icon'] = DB::table('customer_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
            ->select('customer_product_lists.*', 'article_groups.initial', 'article_groups.article_group')
            ->where('customer_id', '=', $customer_id)
            ->get();

        $data['customer'] = Customer::find($customer_id);

        $data['alternative'] = DB::table('customer_alternative_adds')
            ->join('customer_product_lists', 'customer_product_lists.customer_id', '=', 'customer_alternative_adds.customer_id')
            ->where('customer_alternative_adds.customer_id', $customer_id)
            ->where('customer_product_lists.product_id', $product_id)
            ->where('customer_alternative_adds.address_no', $address_no)
            ->first();

        $data['tiles'] = DB::table('products')
            ->join('product_images', 'product_images.product_id', '=', 'products.id')
            ->select('products.*', 'product_images.image')
            ->where('products.category', '=', 'Dachziegel')
            ->get();

        $data['pv_checklist'] = DB::table('p_v_checklists')
            ->where('customer_id', '=', $customer_id)
            ->where('address_no', $address_no)
            ->first();

        $data['pv_roof'] = DB::table('p_v_roofs')
            ->join('p_v_checklists', 'p_v_checklists.id', '=', 'p_v_roofs.pv_id')
            ->select('p_v_roofs.*')
            ->where('p_v_checklists.customer_id', '=', $customer_id)
            ->where('p_v_checklists.address_no', '=', $address_no)
            ->get(); 
        $data['image_category'] = DB::table('image_categories')->get();

        $data['roofPlan'] = DB::table('p_v_roof_plans')
                                ->where('product_id', $product_id)
                                ->get();
        return view('admin.customer.products_details.product_details_edit', $data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            $productId = $request->input('product_id');
            $interested = $request->input('interested');

            // Find the customer
            $customer = Customer::find($customerId);

            if ($customer) {
                // Check if the customer is interested in the product and update accordingly
                if ($interested) {
                    // Attach product if interested
                    $customer->products()->syncWithoutDetaching([$productId]);
                } else {
                    // Detach product if not interested
                    $customer->products()->detach($productId);
                }
            }

            return response()->json(['success' => true, 'message' => 'Product status updated successfully']);

        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error saving product status: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to save product status.'], 500);
        }
    }


     public function alternative(Request $request)
     { 
         // Validate address data
         $addressValidate = $request->validate([
             'old_city' => 'required',
             'old_street' => 'required',
             'old_postcode' => 'required'
         ]);

         // Determine if the address is the same
         $same_address = $request->same_address;

         // Create or update CustomerAlternativeAdd instance
         $alternative = null;

         if ($same_address) {
             if ($request->request_address_no == "new") {
                 $alternative = new CustomerAlternativeAdd;
                 $alternative->fill([
                     'street' => $request->old_street,
                     'city' => $request->old_city,
                     'postcode' => $request->old_postcode,
                     'lat' => $request->latitude,
                     'lon' => $request->longitude,
                     'elevation' => $request->old_elevation,
                     'customer_id' => $request->customer_id,
                     'main' => 1,
                     'address_no' => $this->alternativeAddress()
                 ]);
                 $alternative->save();
             } else {
                 $alternative = CustomerAlternativeAdd::where('address_no', '=', $request->request_address_no)->first();
                 if ($alternative) {
                     $alternative->update([
                         'street' => $request->old_street,
                         'city' => $request->old_city,
                         'postcode' => $request->old_postcode,
                         'lat' => $request->latitude,
                         'lon' => $request->longitude,
                         'elevation' => $request->old_elevation,
                         'customer_id' => $request->customer_id,
                         'main' => 1,
                         'address_no' => $request->request_address_no
                     ]);
                 } else {
                     return redirect()->back()->withErrors(['request_address_no' => 'Invalid address number']);
                 }
             }
         } else {
             $alternative = CustomerAlternativeAdd::where('address_no', '=', $request->request_address_no)->first();
             if ($alternative) {
                 $alternative->update([
                     'street' => $request->street,
                     'city' => $request->city,
                     'postcode' => $request->postcode,
                     'lat' => $request->latitude,
                     'lon' => $request->longitude,
                     'elevation' => $request->elevation,
                     'customer_id' => $request->customer_id,
                     'main' => 0,
                     'address_no' => $request->request_address_no
                 ]);
             } else {
                 return redirect()->back()->withErrors(['request_address_no' => 'Invalid address number']);
             }
         }

       

         // Redirect with a success message
         return redirect()->to('customer_product_create/' . $request->customer_id . '/' . $alternative->postcode . '/' . $alternative->address_no)
             ->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
     }

     private function alternativeAddress()
     {
         do {
             $address_no = mt_rand(1000000, 9999999);
         } while (CustomerAlternativeAdd::where('address_no', $address_no)->exists());
         return $address_no;
     }




    private function generateUniqueSerial()
{
    do {
        $serial = mt_rand(1000000, 9999999);
    } while (CustomerCart::where('serial', $serial)->exists());
    return $serial;
}




    /**
     * Show the form for editing the specified resource.
     */
    public function profile($id)
    {
        $data['customer']=Customer::find($id);
        $data['product']=DB::table('customer_products')
                                ->join('article_groups', 'article_groups.id', 'customer_products.product_id')
                                ->join('customers', 'customers.id', 'customer_products.customer_id')
                                ->leftJoin('employees', 'employees.id', '=', 'customers.contact_person')
                                ->select('article_groups.article_group', 'customer_products.product_count', 'customers.id as customer_id', 'employees.name as ename',
                                'employees.lastname as elastname', 'employees.image as eimage')
                                ->where('customer_products.customer_id', '=', $id)
                                ->get();

        $data['first_contact']=DB::table('customers')
                                 ->join('employees', 'employees.id', '=', 'customers.contact_person')
                                 ->where('customers.id', $id)
                                 ->select('customers.id as customer_id', 'employees.name as ename',
                                 'employees.lastname as elastname', 'employees.image as eimage')
                                 ->get();
        return view('admin.customer.documentation.customer_profile', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    { 
        $id=$request->id;
        $data=CustomerProduct::find($id);
        $data->customer_id=$request->customer_id;
        $data->product_id=$request->product_id;
        $data->product_count=$request->product_count;
        $data->save();
        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    public function updatePV(Request $request){
        return dd($request->all());
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = CustomerProduct::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

   public function getCustomerProduct($phase, $product, $alternative, $service)
{
    // Debugging input parameters
    \Log::info('getCustomerProduct - Input Parameters', [
        'phase' => $phase,
        'product' => $product,
        'alternative' => $alternative,
        'service' => $service,
    ]);

    // Fetch tasks
    $tasks = DB::table('phase_activities')
        ->leftJoin('task_to_dos as does', function ($join) {
            $join->on('does.phase_id', '=', 'phase_activities.phase_id')
                ->on('does.activities_id', '=', 'phase_activities.id'); // Ensure correct matching
        })
        ->leftJoin('employees as contact', 'contact.id', '=', 'does.contact_person')
        ->leftJoin('employees as res', 'res.id', '=', 'does.responsible_person')
        ->leftJoin('employees as outside', 'outside.id', '=', 'does.outside_service')
        ->where('phase_activities.phase_id', $phase)
        ->where('phase_activities.product_id', $product)
        ->where('phase_activities.section_name', $service)
        ->select(
            'phase_activities.*', 
            'contact.name as cname', 
            'contact.lastname as clastname', 
            'contact.image as cimage', 
            'res.name as rname', 
            'res.lastname as rlastname', 
            'res.image as rimage',
            'outside.name as osname', 
            'outside.lastname as oslastname', 
            'outside.image as osimage',
            'does.done', 
            'does.type', 
            'does.out_source_type'
        )
        ->get();

    return response()->json([
        'tasks' => $tasks,
    ]);
}


    public function to_dos($id, $product, $alternative, $service)
    {
        // Debugging input parameters
        \Log::info('to_dos - Input Parameters', [
            'id' => $id,
            'product' => $product,
            'alternative' => $alternative,
            'service' => $service,
        ]);

        // Fetch data
        $data = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person')
            ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person')
            ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service')
            ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company')
            ->where('task_to_dos.customer_id', '=', $id)
            ->where('task_to_dos.alternative', '=', $alternative)
            ->select(
                'task_to_dos.*',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'responsible.image as rimage',
                'contact.name as cname',
                'contact.lastname as clastname',
                'contact.image as cimage',
                'outside_s.name as osname',
                'outside_s.lastname as oslastname',
                'outside_s.image as osimage',
                'outside_c.admin_name',
                'outside_c.company_name'
            )
            ->get();

        \Log::info('to_dos - Query Results', $data->toArray());

        return response()->json($data, 200);
    }

    public function getSubTask($phase, $task_id)
    {
         
        // Fetch data
        $data = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person')
            ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person')
            ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service')
            ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company')
            ->where('task_to_dos.customer_id', '=', $id)
            ->where('task_to_dos.alternative', '=', $alternative)
            ->select(
                'task_to_dos.*',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'responsible.image as rimage',
                'contact.name as cname',
                'contact.lastname as clastname',
                'contact.image as cimage',
                'outside_s.name as osname',
                'outside_s.lastname as oslastname',
                'outside_s.image as osimage',
                'outside_c.admin_name',
                'outside_c.company_name'
            )
            ->get();

        \Log::info('to_dos - Query Results', $data->toArray());

        return response()->json($data, 200);
    }
}

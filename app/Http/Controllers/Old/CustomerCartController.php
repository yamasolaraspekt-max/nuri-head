<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCart;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;

class CustomerCartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search=request()->query('search');

        if($search){
            $data=DB::table('customer_carts')
                        ->join('customers', 'customers.id', '=', 'customer_carts.customer_id')
                        ->join('employees', 'employees.id', '=', 'customer_carts.employee_id')
                        ->select('customer_carts.*', 'customers.name as cname', 'customers.lastname as clastname', 'employees.name as ename', 'employees.lastname as elastname')
                        ->where('customer_carts.serial', 'LIKE', "%$search%")
                        ->orWhere('customer_carts.project', 'LIKE', "%$search%")
                        ->orWhere('customers.name', 'LIKE', "%$search%")
                        ->orWhere('customers.name', 'LIKE', "%$search%")
                        ->paginate(20);

                        return view('admin.project.customer.customer_cart_details')->with('data', $data);
        }
        else{
            $data=DB::table('customer_carts')
            ->join('customers', 'customers.id', '=', 'customer_carts.customer_id')
            ->join('employees', 'employees.id', '=', 'customer_carts.employee_id')
            ->select('customer_carts.*', 'customers.name as cname', 'customers.lastname as clastname', 'employees.name as ename', 'employees.lastname as elastname')
            ->paginate(20);

            return view('admin.project.customer.customer_cart_details')->with('data', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['product']=Product::all();
        $data['employee']=Employee::all();
        $data['customer']=Customer::all();

        return view('admin.project.customer.customer_cart', $data);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   =>  'required', 
            'product_id'    =>  'required',
            'employee_id'       =>  'required'
        ],
    [
        'customer_id'   =>  'Please select the customer', 
            'product_id'    =>  'Please select the Products',
            'employee_id'       =>  'Please select the Responsible Employee'
    ]);
     
        $data= new CustomerCart;
        $data->serial=mt_rand(100000,999999);
        $data->customer_id=$request->customer_id;
        $data->employee_id=$request->employee_id;
        $data->project=$request->project;
        $data->status="Not Publish";
        $data->save();

        $data->product()->sync($request->product_id, false);

        return redirect()->to('customer_cart_details')->with('save_msg', 'The record saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function project($id)
    {
        $data=CustomerCart::find($id)
        ->join('customers', 'customers.id', '=', 'customer_carts.customer_id')
        ->join('employees', 'employees.id', '=', 'customer_carts.employee_id')
        ->select('customer_carts.*', 'customers.name as cname', 'customers.lastname as clastname', 'employees.name as ename', 'employees.lastname as elastname')
        ->get();

        return view('admin.project.folder.main')->with('data', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerCart $customerCart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerCart $customerCart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerCart $customerCart)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\AssetInstallment;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Machine;
use DB;
use Illuminate\Http\Request;

class AssetInstallmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($asset, $type, $branch)
    {
        if ($type == 'machine') {
            $data['asset'] = Machine::find($asset);
            if (! $data['asset']) {
                return redirect()->to('asset_installment_show')->with('delete_msg', 'Der zugehörige Datensatz wurde nicht gefunden.');
            }
            $data['employees'] = Employee::where('status', '=', 'Active')->get();

            $duplicate = AssetInstallment::where('asset_id', '=', $asset)->where('type', '=', $type)->where('branch_id', '=', $branch)->first();
            if ($duplicate) {
                return redirect()->to('asset_installment_show?search='.$data['asset']->name)->with('data', $data);

            } else {
                return view('admin.product.assets.installment.installment_create', $data);

            }
        } elseif ($type == 'asset') {
            $data['asset'] = Asset::find($asset);
            if (! $data['asset']) {
                return redirect()->to('asset_installment_show')->with('delete_msg', 'Der zugehörige Datensatz wurde nicht gefunden.');
            }
                 $data['employees'] = Employee::where('status', '=', 'Active')->get();

            $duplicate = AssetInstallment::where('asset_id', '=', $asset)->where('type', '=', $type)->where('branch_id', '=', $branch)->first();
            if ($duplicate) {
             return redirect()->to('asset_installment_show')->with('data', $data);

            } else {
                return view('admin.product.assets.installment.installment_create', $data);

            }

        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit($id, $asset, $type)
    {
        if ($type == 'machine') {
            $data['asset'] = Machine::find($asset);
        } elseif ($type == 'asset') {
            $data['asset'] = Asset::find($asset);

        }
        $data['employees'] = Employee::where('status', '=', 'Active')->get();
        $data['data'] = AssetInstallment::find($id);

        if (empty($data['asset']) || ! $data['data']) {
            return redirect()->to('asset_installment_show')->with('delete_msg', 'Der zugehörige Datensatz wurde nicht gefunden.');
        }

        return view('admin.product.assets.installment.installment_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'purchased_from' => 'required',
            'price_per_month' => 'required',
            'fines' => 'required',
            'installment_duration' => 'required',
            'total' => 'required',
            'due_date' => 'required',
            'paid_by' => 'required',
            'payment_method' => 'required',
        ]);

        $data = AssetInstallment::create($request->all());

        if ($data) {
            return redirect()->to('asset_installment_show')->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert!');
        } else {
            return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich gespeichert!');

        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $search = request()->input('search');

        if ($search) {
            $data['asset'] = DB::table('asset_installments')
                ->join('assets', 'assets.id', '=', 'asset_installments.asset_id')
                ->where('asset_installments.type', '=', 'asset')
                ->select('asset_installments.*', 'assets.item', 'assets.model')
                ->where('assets.item', 'LIKE', "%$search%")
                ->orWhere('assets.id', 'LIKE', "%$search%")
                ->paginate(20);
            $data['car'] = DB::table('asset_installments')
                 ->join('machines', 'machines.id', '=', 'asset_installments.asset_id')
                ->where('asset_installments.type', '=', 'machine')
                ->select('asset_installments.*', 'machines.name', 'machines.model')
                ->where('machines.name', 'LIKE', "%$search%")  
                ->paginate(20);
             

            return view('admin.product.assets.installment.installment', $data);
        } else {
            $data['asset'] = DB::table('asset_installments')
                ->join('assets', 'assets.id', '=', 'asset_installments.asset_id')
                ->where('asset_installments.type', '=', 'asset')
                ->select('asset_installments.*', 'assets.item', 'assets.model')
                ->paginate(20);
            $data['car'] = DB::table('asset_installments')
                ->join('machines', 'machines.id', '=', 'asset_installments.asset_id')
                ->where('asset_installments.type', '=', 'machine')
                ->select('asset_installments.*', 'machines.name', 'machines.model')
                ->paginate(20);

            return view('admin.product.assets.installment.installment', $data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function append($id)
    {
        $data = AssetInstallment::find($id);
        $newData = $data->replicate();
        $newData->save();

        return redirect()->to('asset_installment_show')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetInstallment $assetInstallment)
    {
        $request->validate([
            'purchased_from' => 'required',
            'price_per_month' => 'required',
            'fines' => 'required',
            'installment_duration' => 'required',
            'total' => 'required',
            'due_date' => 'required',
            'paid_by' => 'required',
            'payment_method' => 'required',
        ]);
        $id = $request->id;
        $data = AssetInstallment::find($id)->update($request->all());

        if ($data) {
            return redirect()->to('asset_installment_show')->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert!');
        } else {
            return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich gespeichert!');

        }
    }

    public function delete_photo($photo)
    {

        if (! empty($photo)) {
            $photo_path = 'images/installment/contract/'.$photo;

            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = AssetInstallment::find($id);
        $this->delete_photo($data->contract_document);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich  gelöscht');
    }

    public function save_pdf(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:4048',
        ]);

        $data = AssetInstallment::find($request->id);

        if ($request->hasFile('pdf_file')) {
            $this->delete_photo($data->contract_document);
            $image_name = time().'.'.$request->file('pdf_file')->getClientOriginalExtension();
            $request->file('pdf_file')->move('images/installment/contract/', $image_name);
            $data->contract_document = $image_name;
            $data->save();

            return redirect()->back()->with('save_msg', 'Das PDF wurde hochgeladen');

        } else {
            $data->save();

            return redirect()->back()->with('delete_msg', 'Das PDF wurde nicht hochgeladen');

        }
    }
}

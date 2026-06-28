<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\CustomerContactPerson;
use Illuminate\Http\Request;
use App\Models\NewLeads;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerContactPersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Helper to log activity manually.
     */
    private function logActivity($event, $modelId, $leadId, $altId = null, $changes = [])
    {
        $userName = 'System';
        if (Auth::check()) {
            $employeeId = Auth::user()->name; 
            $employee = DB::table('employees')->where('id', $employeeId)->first();
            if ($employee) {
                $userName = trim($employee->name . ' ' . $employee->lastname);
            }
        }

        DB::table('lead_activity_logs')->insert([
            'new_leads_id'   => $leadId,
            'alternative_id' => $altId,
            'user_id'        => Auth::id(),
            'user_name'      => $userName,
            'event_type'     => $event, // created, updated, deleted
            'model_type'     => 'App\Models\CustomerContactPerson',
            'model_id'       => $modelId,
            'changes'        => json_encode($changes),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function index(NewLeads $customer)
    {
        $people = $customer->contactPeople()
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id'        => $p->id,
                    'relation'  => $p->relation,
                    'name'      => $p->name,
                    'lastname'  => $p->lastname,
                    'phone'     => $p->phone,
                    'office'    => $p->office,
                    'home'      => $p->home,
                    'email'     => $p->email,
                    'status'    => $p->status,
                    'full_name' => trim(($p->name ?? '') . ' ' . ($p->lastname ?? '')),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $people,
        ]);
    }

    public function saves(Request $request, NewLeads $customer)
    {
        $validator = Validator::make($request->all(), [
            'relation'      => ['nullable', 'string', 'max:255'],
            'name'          => ['nullable', 'string', 'max:255'],
            'lastname'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:255'],
            'office'        => ['nullable', 'string', 'max:255'],
            'home'          => ['nullable', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'alternative_id'=> ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['customer_id'] = $customer->id;

        $person = CustomerContactPerson::create($data);

        // ★ LOG
        $this->logActivity('created', $person->id, $customer->id, $data['alternative_id'] ?? null, [
            'info' => 'Kontaktperson hinzugefügt',
            'name' => $person->name . ' ' . $person->lastname
        ]);

        return response()->json([
            'success' => true,
            'data'    => $person,
        ]);
    }

    public function update(Request $request, CustomerContactPerson $person)
    {
        $validator = Validator::make($request->all(), [
            'relation'      => ['nullable', 'string', 'max:255'],
            'name'          => ['nullable', 'string', 'max:255'],
            'lastname'      => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:255'],
            'office'        => ['nullable', 'string', 'max:255'],
            'home'          => ['nullable', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'alternative_id'=> ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldName = $person->name . ' ' . $person->lastname;
        $person->update($validator->validated());

        // ★ LOG
        $this->logActivity('updated', $person->id, $person->customer_id, $person->alternative_id, [
            'info' => 'Kontaktperson bearbeitet',
            'name' => ['from' => $oldName, 'to' => $person->name . ' ' . $person->lastname]
        ]);

        return response()->json([
            'success' => true,
            'data'    => $person,
        ]);
    }

    public function deletes(CustomerContactPerson $person)
    {
        // ★ LOG (Before delete)
        $this->logActivity('deleted', $person->id, $person->customer_id, $person->alternative_id, [
            'info' => 'Kontaktperson gelöscht',
            'name' => $person->name . ' ' . $person->lastname
        ]);

        $person->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        foreach ($request->contact_people as $person) {
            $created = CustomerContactPerson::create([
                'customer_id'    => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'relation'       => $person['relation'],
                'name'           => $person['name'],
                'lastname'       => $person['lastname'],
                'phone'          => $person['phone'],
                'office'         => $person['office'],
                'home'           => $person['home'],
                'email'          => $person['email'],
                'status'         => 'Published'
            ]);

            // ★ LOG
            $this->logActivity('created', $created->id, $request->customer_id, $request->alternative_id, [
                'info' => 'Kontaktperson via Batch erstellt',
                'name' => $created->name . ' ' . $created->lastname
            ]);
        }

        return redirect()->back()->with('success', 'Contact persons saved successfully!');
    }

    public function fetch($customerId, $alternativeId)
    {
        $data = \App\Models\CustomerContactPerson::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->get();

        return response()->json($data);
    }

    public function updateAll(Request $request)
    {
        foreach ($request->contact_people as $person) {
            if (!empty($person['id'])) {
                // update existing
                $existing = CustomerContactPerson::find($person['id']);
                if ($existing) {
                    $existing->update($person);
                    // ★ LOG
                    $this->logActivity('updated', $existing->id, $request->customer_id, $request->alternative_id, ['info' => 'Kontaktperson Batch-Update']);
                }
            } else {
                // create new
                $new = CustomerContactPerson::create([
                    'customer_id'    => $request->customer_id,
                    'alternative_id' => $request->alternative_id,
                    'relation'       => $person['relation'],
                    'name'           => $person['name'],
                    'lastname'       => $person['lastname'],
                    'phone'          => $person['phone'],
                    'office'         => $person['office'],
                    'home'           => $person['home'],
                    'email'          => $person['email'],
                ]);
                // ★ LOG
                $this->logActivity('created', $new->id, $request->customer_id, $request->alternative_id, ['info' => 'Kontaktperson Batch-Create']);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function delete($id)
    {
        $person = CustomerContactPerson::find($id);
        if ($person) {
            // ★ LOG
            $this->logActivity('deleted', $person->id, $person->customer_id, $person->alternative_id, ['info' => 'Kontaktperson gelöscht']);
            $person->delete();
        }
        return response()->json(['status' => 'deleted']);
    }

    public function list($customerId, $alternativeId)
    {
        $contacts = CustomerContactPerson::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->get();

        return response()->json($contacts);
    }

    public function storeOrUpdate(Request $request)
    {
        $person = CustomerContactPerson::create([
            'customer_id'    => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'relation'       => $request->relation,
            'name'           => $request->name,
            'lastname'       => $request->lastname,
            'phone'          => $request->phone,
            'office'         => $request->office,
            'home'           => $request->home,
            'email'          => $request->email,
            'status'         => 'Published',
        ]);

        // ★ LOG
        $this->logActivity('created', $person->id, $request->customer_id, $request->alternative_id, ['info' => 'Kontaktperson gespeichert']);
    
        return response()->json(['success' => true, 'message' => 'Kontaktperson gespeichert.']);
    }

    public function destroy($id)
    {
        $person = CustomerContactPerson::findOrFail($id);
        // ★ LOG
        $this->logActivity('deleted', $person->id, $person->customer_id, $person->alternative_id, ['info' => 'Kontaktperson zerstört']);
        $person->delete();
        return response()->json(['status' => 'deleted']);
    }

    public function save(Request $request)
    {   
        $people = $request->input('contact_people', []);
        foreach ($people as $person) {
            if (!empty($person['id'])) {
                // ✅ Update
                $existing = CustomerContactPerson::find($person['id']);
                if ($existing) {
                    $existing->update([
                        'name' => $person['name'],
                        'lastname' => $person['lastname'],
                        'relation' => $person['relation'],
                        'phone' => $person['phone'],
                        'home' => $person['home'],
                        'office' => $person['office'],
                        'email' => $person['email'],
                    ]);
                    // ★ LOG
                    $this->logActivity('updated', $existing->id, $request->customer_id, $request->alternative_id, ['info' => 'Kontaktperson Batch-Update']);
                }
            } else {
                // ✅ Create
                $new = CustomerContactPerson::create([
                    'customer_id' => $request->customer_id,
                    'alternative_id' => $request->alternative_id,
                    'name' => $person['name'],
                    'lastname' => $person['lastname'],
                    'relation' => $person['relation'],
                    'phone' => $person['phone'],
                    'home' => $person['home'],
                    'office' => $person['office'],
                    'email' => $person['email'],
                ]);
                // ★ LOG
                $this->logActivity('created', $new->id, $request->customer_id, $request->alternative_id, ['info' => 'Kontaktperson Batch-Create']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontaktpersonen erfolgreich gespeichert.'
        ]);
    }
}
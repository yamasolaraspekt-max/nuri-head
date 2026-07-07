<?php
namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;

use App\Models\LeadAlternativeAdd;
use App\Models\NewLeads;
use App\Models\RadiatorInstallation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RadiatorInstallationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * One Blade page for customer select, object select, list, create, edit, delete.
     */
    public function index()
    {
        return view('admin.configurations.radiator.radiator_create');
    }

    /**
     * Select2 customer search.
     */
    public function ajaxCustomers(Request $request)
    {
        $id = $request->input('id');
        $search = trim((string) $request->input('q'));

        $customers = NewLeads::query()
            ->select([
                'id',
                'customer_no',
                'firma',
                'name',
                'lastname',
                'street',
                'postcode',
                'city',
                'phone',
                'telephone',
                'email',
            ])
            ->when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when(!$id && $search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_no', 'like', "%{$search}%")
                        ->orWhere('firma', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('postcode', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->limit($id ? 1 : 25)
            ->get();

        return response()->json([
            'results' => $customers->map(function ($customer) {
                $name = trim(($customer->firma ?: '') ?: (($customer->name ?? '') . ' ' . ($customer->lastname ?? '')));

                if ($name === '') {
                    $name = '#' . $customer->id;
                }

                $address = trim(($customer->street ?? '') . ', ' . ($customer->postcode ?? '') . ' ' . ($customer->city ?? ''));

                return [
                    'id' => $customer->id,
                    'text' => $name . ' · ' . ($address ?: 'Keine Adresse'),
                    'customer' => [
                        'id' => $customer->id,
                        'customer_no' => $customer->customer_no,
                        'name' => $name,
                        'street' => $customer->street,
                        'postcode' => $customer->postcode,
                        'city' => $customer->city,
                        'phone' => $customer->phone ?: $customer->telephone,
                        'email' => $customer->email,
                    ],
                ];
            }),
        ]);
    }
    /**
     * Load objects for selected customer.
     */
    public function ajaxObjects(NewLeads $customer)
    {
        $objects = LeadAlternativeAdd::query()
            ->where('lead_id', $customer->id)
            ->orderByDesc('main')
            ->orderBy('id')
            ->get();

        // fallback object from new_leads if no lead_alternative_add exists
        if ($objects->isEmpty()) {
            return response()->json([
                'customer' => $this->customerPayload($customer),
                'auto_select' => true,
                'objects' => [
                    [
                        'id' => null,
                        'text' => trim(($customer->street ?? '') . ', ' . ($customer->postcode ?? '') . ' ' . ($customer->city ?? '')) ?: 'Hauptadresse',
                        'street' => $customer->street,
                        'postcode' => $customer->postcode,
                        'city' => $customer->city,
                        'address_no' => null,
                        'main' => true,
                    ],
                ],
            ]);
        }

        return response()->json([
            'customer' => $this->customerPayload($customer),
            'auto_select' => $objects->count() === 1,
            'objects' => $objects->map(function ($object) {
                $address = trim(($object->object_name ? $object->object_name . ' · ' : '') . ($object->street ?? '') . ', ' . ($object->postcode ?? '') . ' ' . ($object->city ?? ''));

                return [
                    'id' => $object->id,
                    'text' => $address ?: ('Objekt #' . $object->id),
                    'street' => $object->street,
                    'postcode' => $object->postcode,
                    'city' => $object->city,
                    'address_no' => $object->address_no,
                    'main' => (bool) $object->main,
                ];
            })->values(),
        ]);
    }

    /**
     * Load radiator list for selected customer/object.
     */
    public function ajaxList(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'exists:lead_alternative_adds,id'],
        ]);

        $radiators = RadiatorInstallation::query()
            ->where('customer_id', $validated['customer_id'])
            ->when(
                array_key_exists('alternative_id', $validated) && $validated['alternative_id'],
                fn ($q) => $q->where('alternative_id', $validated['alternative_id']),
                fn ($q) => $q->whereNull('alternative_id')
            )
            ->latest('id')
            ->get();

        return response()->json([
            'ok' => true,
            'stats' => [
                'total' => $radiators->count(),
                'with_image' => $radiators->whereNotNull('image')->count(),
                'with_socket' => $radiators->where('has_socket', true)->count(),
                'needs_thermostat' => $radiators->where('renew_thermostat_head', true)->count(),
            ],
            'data' => $radiators,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        DB::beginTransaction();

        try {
            $radiator = new RadiatorInstallation();
            $radiator->fill($validated);
            $radiator->fill($this->booleanData($request));

            if ($request->hasFile('image')) {
                $radiator->image = $this->uploadImage($request);
            }

            $radiator->save();

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Heizkörper wurde erfolgreich gespeichert.',
                'data' => $radiator->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Heizkörper konnte nicht gespeichert werden.',
            ], 422);
        }
    }

    public function update(Request $request)
    {
        $validated = $this->validatedData($request, true);

        DB::beginTransaction();

        try {
            $radiator = RadiatorInstallation::findOrFail($validated['id']);

            unset($validated['id']);

            $radiator->fill($validated);
            $radiator->fill($this->booleanData($request));

            if ($request->hasFile('image')) {
                $this->deletePhoto($radiator->image);
                $radiator->image = $this->uploadImage($request);
            }

            $radiator->save();

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Heizkörper wurde erfolgreich aktualisiert.',
                'data' => $radiator->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Heizkörper konnte nicht aktualisiert werden.',
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $radiator = RadiatorInstallation::findOrFail($id);

            $this->deletePhoto($radiator->image);
            $radiator->delete();

            return response()->json([
                'ok' => true,
                'message' => 'Heizkörper wurde erfolgreich gelöscht.',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Heizkörper konnte nicht gelöscht werden.',
            ], 422);
        }
    }

    private function validatedData(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'id' => [$isUpdate ? 'required' : 'nullable', 'nullable', 'exists:radiator_installations,id'],

            'customer_id' => ['required', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'exists:lead_alternative_adds,id'],

            'postcode' => ['nullable', 'string', 'max:255'],
            'address_no' => ['nullable', 'string', 'max:255'],

            'number' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'radiator_type' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],

            'room_size' => ['nullable', 'numeric', 'min:0'],

            'width' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'integer', 'min:0'],
            'depth' => ['nullable', 'integer', 'min:0'],

            'niche_top' => ['nullable', 'integer', 'min:0'],
            'niche_bottom' => ['nullable', 'integer', 'min:0'],
            'niche_left' => ['nullable', 'integer', 'min:0'],
            'niche_right' => ['nullable', 'integer', 'min:0'],

            'supply_valve' => ['nullable', 'string', 'max:255'],
            'return_valve' => ['nullable', 'string', 'max:255'],
            'design' => ['nullable', 'string', 'max:255'],

            'socket_distance' => ['nullable', 'numeric', 'min:0'],
            'limbs' => ['nullable', 'string', 'max:255'],

            // EN-442-Auslegung (additiv; Spalten seit Migration 140005, alle nullable/Default).
            // Katalog-Link + Rechen-Input, damit RadiatorPerformanceService q_norm/exponent/norm_bedingung
            // aus product_radiator_specs ziehen kann. Reine Reuse der Bestandsaufnahme, keine Bestandsänderung.
            'radiator_spec_id' => ['nullable', 'exists:product_radiator_specs,id'],
            'anzahl' => ['nullable', 'integer', 'min:1'],
            'anschluss_position' => ['nullable', 'in:seitlich,unten,mittel,wechselseitig'],
            'anschluss_fuehrung' => ['nullable', 'in:zweirohr,einrohr'],
            'ventil_einsatz_bestand' => ['nullable', 'string', 'max:255'],
            'kopf_norm_bestand' => ['nullable', 'in:M30x1_5,RA,RAV,RAVL,sonstige'],
            'heating_circuit_id' => ['nullable', 'exists:heating_circuits,id'],
            'q_norm_w_pro_m_override' => ['nullable', 'numeric', 'min:0'],
            'exponent_n_override' => ['nullable', 'numeric', 'min:0'],
            'typ_konfidenz' => ['nullable', 'in:sicher,geschaetzt'],

            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ]);
    }

    private function booleanData(Request $request): array
    {
        return [
            'has_window_sill' => $request->boolean('has_window_sill'),
            'supply_valve_presettable' => $request->boolean('supply_valve_presettable'),
            'return_valve_present' => $request->boolean('return_valve_present'),
            'renew_thermostat_head' => $request->boolean('renew_thermostat_head'),
            'has_socket' => $request->boolean('has_socket'),
        ];
    }

    private function customerPayload(NewLeads $customer): array
    {
        $name = trim(($customer->firma ?: '') ?: (($customer->name ?? '') . ' ' . ($customer->lastname ?? '')));

        return [
            'id' => $customer->id,
            'customer_no' => $customer->customer_no,
            'name' => $name ?: ('#' . $customer->id),
            'street' => $customer->street,
            'postcode' => $customer->postcode,
            'city' => $customer->city,
            'phone' => $customer->phone ?: $customer->telephone,
            'email' => $customer->email,
        ];
    }

    private function uploadImage(Request $request): string
    {
        $directory = public_path('images/radiators');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $name = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        $file->move($directory, $name);

        return $name;
    }

    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $path = public_path('images/radiators/' . $photo);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
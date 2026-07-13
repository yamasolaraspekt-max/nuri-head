<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\LeadAlternativeAdd;
use App\Models\MasterSet;
use App\Models\MasterSetGroup;
use App\Models\NewLeads;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\LeadProductList;
use App\Models\Offer;
use App\Models\OfferFolder;
use App\Models\OfferDetail;
use App\Models\Branch;
use App\Models\OfferTemplate;
class OfferWizardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $templateEditId = $request->integer('template_id') ?: null;

        $isTemplateEditMode = (bool) (
            $templateEditId
            || $request->boolean('edit_template')
            || $request->get('mode') === 'template'
        );

        $editingTemplate = null;

        if ($templateEditId) {
            $editingTemplate = OfferTemplate::query()
                ->select([
                    'id',
                    'name',
                    'description',
                    'department_id',
                    'article_group_id',
                    'brand_id',
                    'distributor_id',
                    'brand_color',
                    'brand_mode',
                    'brand_logo_url',
                    'company_name',
                    'cover_text_html',
                    'sections',
                    'placed_images',
                    'biography_data',
                    'leistung',
                ])
                ->find($templateEditId);
        }

        return view('admin.offer.configuration.offer.config', [
            'templateEditId' => $templateEditId,
            'isTemplateEditMode' => $isTemplateEditMode,
            'editingTemplate' => $editingTemplate,
            'templateEditPayload' => $editingTemplate ? [
                'id' => (int) $editingTemplate->id,
                'name' => (string) ($editingTemplate->name ?? ''),
                'description' => (string) ($editingTemplate->description ?? ''),
                'department_id' => $editingTemplate->department_id,
                'article_group_id' => $editingTemplate->article_group_id,
                'brand_id' => $editingTemplate->brand_id,
                'distributor_id' => $editingTemplate->distributor_id,
                'brand_color' => $editingTemplate->brand_color,
                'brand_mode' => $editingTemplate->brand_mode,
                'brand_logo_url' => $editingTemplate->brand_logo_url,
                'company_name' => $editingTemplate->company_name,
                'cover_text_html' => $editingTemplate->cover_text_html,
                'sections' => $editingTemplate->sections,
                'placed_images' => $editingTemplate->placed_images,
                'biography_data' => $editingTemplate->biography_data,
                'leistung' => $editingTemplate->leistung,
            ] : null,
        ]);
    }

    public function smart()
    {
        $branches = Branch::query()
            ->orderBy('branch')
            ->get();

        return view('admin.offer.configuration.offer.wizard-smart', compact('branches'));
    }
    public function customerShow(NewLeads $lead)
    {
        $displayName = trim(
            $lead->firma
            ?: trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? ''))
            ?: ($lead->email ?? '')
            ?: ($lead->customer_no ?? '')
            ?: ('#' . $lead->id)
        );

        return response()->json([
            'customer' => [
                'id' => (int) $lead->id,
                'name' => $displayName,
                'display_name' => $displayName,
                'customer_no' => $lead->customer_no,
                'customer_type' => $lead->customer_type,
                'title' => $lead->title,
                'academic_title' => $lead->academic_title,
                'firma' => $lead->firma,
                'name_first' => $lead->name,
                'lastname' => $lead->lastname,
                'street' => $lead->street,
                'postcode' => $lead->postcode,
                'city' => $lead->city,
                'email' => $lead->email,
                'phone' => $lead->phone,
            ],
        ]);
    }

    public function searchCustomers(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = NewLeads::query()
            ->select([
                'id',
                'customer_no',
                'firma',
                'name',
                'lastname',
                'street',
                'postcode',
                'city',
                'email',
                DB::raw("
                    COALESCE(
                        NULLIF(TRIM(COALESCE(firma,'')), ''),
                        NULLIF(TRIM(CONCAT(COALESCE(name,''), ' ', COALESCE(lastname,''))), ''),
                        NULLIF(TRIM(COALESCE(email,'')), ''),
                        NULLIF(TRIM(COALESCE(customer_no,'')), ''),
                        CONCAT('#', id)
                    ) as display_name
                "),
            ])
            ->when($q !== '', function ($qry) use ($q) {
                $qry->where(function ($w) use ($q) {
                    $w->where('customer_no', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('lastname', 'like', "%{$q}%")
                        ->orWhere('firma', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('street', 'like', "%{$q}%")
                        ->orWhere('city', 'like', "%{$q}%")
                        ->orWhere('postcode', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'id' => (int) $c->id,
                'name' => (string) $c->display_name,
                'display_name' => (string) $c->display_name,
                'customer_no' => (string) ($c->customer_no ?? ''),
                'street' => (string) ($c->street ?? ''),
                'postcode' => (string) ($c->postcode ?? ''),
                'city' => (string) ($c->city ?? ''),
                'email' => (string) ($c->email ?? ''),
            ])
            ->values();

        return response()->json(['items' => $items]);
    }

    public function customerObjects(NewLeads $lead)
    {
        $objects = LeadAlternativeAdd::query()
            ->where('lead_id', $lead->id)
            ->select([
                'id',
                'lead_id',
                'object_name',
                'street',
                'postcode',
                'city',
                'main',
                'full_address',
            ])
            ->orderByDesc('main')
            ->orderBy('id')
            ->get()
            ->map(function ($object) {
                $objectName = trim((string) ($object->object_name ?? ''));

                $address = trim(implode(', ', array_filter([
                    $object->street,
                    trim(($object->postcode ?? '') . ' ' . ($object->city ?? '')),
                ])));

                $displayName = $objectName !== ''
                    ? $objectName
                    : ($address !== '' ? $address : 'Objekt #' . $object->id);

                return [
                    'id' => (int) $object->id,
                    'alternative_id' => (int) $object->id,
                    'object_name' => $object->object_name,
                    'display_name' => $displayName,
                    'street' => $object->street,
                    'postcode' => $object->postcode,
                    'city' => $object->city,
                    'full_address' => $object->full_address,
                    'main' => (bool) $object->main,
                ];
            })
            ->values();

        $products = LeadProductList::query()
            ->with([
                'articleGroup:id,article_group,initial,image',
                'service:id,phase_section',
                'department:id,department_name',
            ])
            ->where('customer_id', $lead->id)
            ->get()
            ->map(function (LeadProductList $row) use ($objects) {
                $articleGroup = $row->articleGroup;
                $phaseSection = $row->service;

                $articleTitle = trim((string) ($articleGroup?->article_group ?? ''));
                $initial = trim((string) ($articleGroup?->initial ?? ''));

                $serviceText = trim((string) (
                    $row->service
                    ?: $phaseSection?->phase_section
                    ?: ''
                ));

                $serviceLabel = $this->wizardServiceLabel($serviceText);

                $object = $objects->firstWhere('id', (int) $row->alternative_id);

                $label = $articleTitle !== ''
                    ? ($initial !== '' ? "{$articleTitle} • {$initial}" : $articleTitle)
                    : "Produkt #{$row->product_id}";

                if ($serviceLabel) {
                    $label .= ' — ' . $serviceLabel;
                }

                return [
                    'lead_product_id' => (int) $row->id,
                    'lead_product_list_id' => (int) $row->id,

                    'alternative_id' => $row->alternative_id ? (int) $row->alternative_id : null,
                    'object_name' => $object['object_name'] ?? null,
                    'object_display_name' => $object['display_name'] ?? null,

                    'product_id' => (int) $row->product_id,
                    'article_group_id' => (int) $row->product_id,
                    'article_group' => $articleTitle,
                    'initial' => $initial,
                    'image' => $articleGroup?->image,

                    'service_id' => $row->service_id ? (int) $row->service_id : null,
                    'service' => $serviceText,
                    'service_label' => $serviceLabel,

                    'department_id' => $row->department_id ? (int) $row->department_id : null,
                    'department_name' => $row->department?->department_name,

                    'status' => $row->status,
                    'stage' => $row->stage,
                    'work_status' => $row->work_status,
                    'price' => $row->price,

                    'label' => $label,
                ];
            })
            ->values();

        $customer = [
            'id' => (int) $lead->id,
            'customer_no' => $lead->customer_no,
            'customer_type' => $lead->customer_type,
            'title' => $lead->title,
            'academic_title' => $lead->academic_title,
            'firma' => $lead->firma,
            'name' => $lead->name,
            'lastname' => $lead->lastname,
            'street' => $lead->street,
            'postcode' => $lead->postcode,
            'city' => $lead->city,
            'email' => $lead->email,
            'phone' => $lead->phone,
        ];

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'objects' => $objects,
            'products' => $products,
        ]);
    }

    private function wizardServiceLabel(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $map = [
            'complete' => 'Komplett',
            'maintanence' => 'Wartung',
            'maintenance' => 'Wartung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'repair' => 'Reparatur',
        ];

        return $map[strtolower($value)] ?? ucfirst($value);
    }
    public function createOffer(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', Rule::exists('new_leads', 'id')],
            'alternative_id' => ['required', 'integer', Rule::exists('lead_alternative_adds', 'id')],
            'doc_type' => ['nullable', 'string', 'max:100'],
            'project_date' => ['nullable', 'date'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', Rule::exists('article_groups', 'id')],
        ]);

        $alternative = LeadAlternativeAdd::query()
            ->where('id', (int) $data['alternative_id'])
            ->where('lead_id', (int) $data['customer_id'])
            ->firstOrFail();

        $firstProductId = collect($data['product_ids'])
            ->filter()
            ->map(fn($id) => (int) $id)
            ->first();

        if (!$firstProductId) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst ein Produkt / Gewerk auswählen.',
            ], 422);
        }

        $leadProduct = LeadProductList::query()
            ->with([
                'service:id,phase_section',
                'department:id,department_name',
            ])
            ->where('customer_id', (int) $data['customer_id'])
            ->where('alternative_id', (int) $data['alternative_id'])
            ->where('product_id', (int) $firstProductId)
            ->first();

        if (!$leadProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Für diese Kunde/Objekt/Gewerk-Kombination wurde kein LeadProductList-Eintrag gefunden.',
            ], 422);
        }

        $employeeId = null;

        if (auth()->check()) {
            $user = auth()->user();

            if (is_numeric($user->name ?? null)) {
                $employeeId = (int) $user->name;
            } elseif (!empty($user->employee_id)) {
                $employeeId = (int) $user->employee_id;
            } elseif (is_numeric($user->id ?? null)) {
                $employeeId = (int) $user->id;
            }
        }

        $branch = null;

        if (!empty($data['branch_id'])) {
            $branch = Branch::query()->find((int) $data['branch_id']);
        }

        $result = DB::transaction(function () use ($data, $firstProductId, $employeeId, $leadProduct, $alternative, $branch) {
            $serviceId = $leadProduct->service_id ? (int) $leadProduct->service_id : null;
            $departmentId = $leadProduct->department_id ? (int) $leadProduct->department_id : null;

            $serviceText = trim((string) (
                $leadProduct->service
                ?: $leadProduct->service?->phase_section
                ?: $data['doc_type']
                ?: 'Angebot'
            ));

            if ($serviceText === '') {
                $serviceText = 'Angebot';
            }

            /*
            |--------------------------------------------------------------------------
            | 1. Create offer
            |--------------------------------------------------------------------------
            | Offer::create() is used so Offer::booted() can generate offer_no.
            | IMPORTANT: Offer::resolveOfferPrefix() must use department_name,
            | not name/short_name.
            |--------------------------------------------------------------------------
            */
            $offer = Offer::create([
                'customer_id' => (int) $data['customer_id'],
                'alternative_id' => (int) $data['alternative_id'],
                'product_id' => (int) $firstProductId,
                'service_id' => $serviceId,
                'department_id' => $departmentId,
                'service' => $serviceText,
                'created_by' => $employeeId,
                'created_for' => $employeeId,
                'status' => 'draft',
                'status_msg' => 'Smart Wizard erstellt',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Create folder
            |--------------------------------------------------------------------------
            */
            $folderPayload = [
                'offer_id' => $offer->id,
                'customer_id' => $offer->customer_id,
                'alternative_id' => $offer->alternative_id,
                'product_id' => $offer->product_id,
                'name' => 'Ordner: ' . ($offer->offer_no ?: ('Angebot #' . $offer->id)),
                'color' => '#93c21c',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('offer_folders', 'department_id')) {
                $folderPayload['department_id'] = $departmentId;
            }

            if (Schema::hasColumn('offer_folders', 'created_by')) {
                $folderPayload['created_by'] = $employeeId;
            }

            if (Schema::hasColumn('offer_folders', 'document_status')) {
                $folderPayload['document_status'] = 'offer';
            }

            if (Schema::hasColumn('offer_folders', 'offer_status')) {
                $folderPayload['offer_status'] = 'draft';
            }

            if (Schema::hasColumn('offer_folders', 'deal_status')) {
                $folderPayload['deal_status'] = 'open';
            }

            if (Schema::hasColumn('offer_folders', 'history')) {
                $folderPayload['history'] = json_encode([
                    [
                        'type' => 'folder_created',
                        'title' => 'Ordner erstellt',
                        'reason' => 'Smart Wizard erstellt',
                        'changed_by' => [
                            'employee_id' => $employeeId,
                        ],
                        'created_at' => now()->toDateTimeString(),
                    ],
                ]);
            }

            $folderId = DB::table('offer_folders')->insertGetId($folderPayload);

            /*
            |--------------------------------------------------------------------------
            | 3. Optional offer_items
            |--------------------------------------------------------------------------
            */
            if (Schema::hasTable('offer_items')) {
                $rows = collect($data['product_ids'])
                    ->filter()
                    ->map(function ($pid) use ($offer) {
                        return [
                            'offer_id' => $offer->id,
                            'product_id' => (int) $pid,
                            'qty' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })
                    ->values()
                    ->all();

                if (!empty($rows)) {
                    DB::table('offer_items')->insert($rows);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Create empty folder detail
            |--------------------------------------------------------------------------
            */
            if (Schema::hasTable('offer_details')) {
                $detailPayload = [
                    'offer_id' => $offer->id,
                    'offer_folder_id' => $folderId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('offer_details', 'offer_no')) {
                    $detailPayload['offer_no'] = $offer->offer_no;
                }

                if (Schema::hasColumn('offer_details', 'document_status')) {
                    $detailPayload['document_status'] = 'offer';
                }

                if (Schema::hasColumn('offer_details', 'tax_rate')) {
                    $detailPayload['tax_rate'] = 19;
                }

                if (Schema::hasColumn('offer_details', 'total_net')) {
                    $detailPayload['total_net'] = 0;
                }

                if (Schema::hasColumn('offer_details', 'total_gross')) {
                    $detailPayload['total_gross'] = 0;
                }

                if (Schema::hasColumn('offer_details', 'sections')) {
                    $detailPayload['sections'] = json_encode([]);
                }

                if (Schema::hasColumn('offer_details', 'placed_images')) {
                    $detailPayload['placed_images'] = json_encode([]);
                }

                if (Schema::hasColumn('offer_details', 'print_attachments')) {
                    $detailPayload['print_attachments'] = json_encode([]);
                }

                if (Schema::hasColumn('offer_details', 'biography_data')) {
                    $detailPayload['biography_data'] = json_encode([
                        [
                            'type' => 'created',
                            'note' => 'Smart Wizard erstellt',
                            'employee_id' => $employeeId,
                            'created_at' => now()->toDateTimeString(),
                        ],
                    ]);
                }

                if (Schema::hasColumn('offer_details', 'material_history')) {
                    $detailPayload['material_history'] = json_encode([]);
                }

                if (Schema::hasColumn('offer_details', 'branch_id')) {
                    $detailPayload['branch_id'] = $branch?->id;
                }

                if (Schema::hasColumn('offer_details', 'company_name')) {
                    $detailPayload['company_name'] = $branch?->branch ?: 'SOLAR ASPEKT';
                }

                if (Schema::hasColumn('offer_details', 'brand_color')) {
                    $detailPayload['brand_color'] = $branch?->color ?: '#93c21c';
                }

                if (Schema::hasColumn('offer_details', 'brand_mode')) {
                    $detailPayload['brand_mode'] = ($branch?->logo_url || $branch?->image) ? 'image' : 'text';
                }

                if (Schema::hasColumn('offer_details', 'brand_logo_url')) {
                    $detailPayload['brand_logo_url'] = $branch?->logo_url
                        ?: ($branch?->image ? asset('storage/' . ltrim($branch->image, '/')) : null);
                }

                if (Schema::hasColumn('offer_details', 'company_footer')) {
                    $detailPayload['company_footer'] = json_encode([
                        'branch_id' => $branch?->id,
                        'company' => $branch?->branch,
                        'street' => $branch?->street,
                        'postcode' => $branch?->postcode,
                        'city' => $branch?->city,
                        'country' => $branch?->country,
                        'phone' => $branch?->phone,
                        'whatsapp' => $branch?->whatsapp,
                        'email' => $branch?->email,
                        'web' => $branch?->web,
                        'bank' => $branch?->bank,
                        'iban' => $branch?->iban,
                        'bic' => $branch?->bic,
                        'register' => $branch?->register,
                        'tax' => $branch?->tax,
                        'vat' => $branch?->vat,
                        'gf' => $branch?->gf,
                        'contact_person' => $branch?->contact_person,
                    ]);
                }

                DB::table('offer_details')->insert($detailPayload);
            }

            return [
                'offer_id' => $offer->id,
                'offer_no' => $offer->offer_no,
                'folder_id' => $folderId,
                'service_id' => $serviceId,
                'department_id' => $departmentId,
                'service' => $serviceText,
                'object_name' => $alternative->object_name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Angebot und Ordner wurden erstellt.',
            'offer_id' => $result['offer_id'],
            'offer_no' => $result['offer_no'],
            'folder_id' => $result['folder_id'],
            'service_id' => $result['service_id'],
            'department_id' => $result['department_id'],
            'service' => $result['service'],
            'object_name' => $result['object_name'],
            'redirect' => route('admin.offers.folders.show', $result['folder_id']),
        ]);
    }
    public function groupSetsCatalog(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $articleGroupId = $request->integer('article_group_id') ?: null;
        $ctx = strtolower((string) $request->get('context', 'angebot'));

        $placeholder = asset('images/icons/placeholder.svg');
        $prodImgUrl = fn(?string $f) => $f ? asset('images/products/' . ltrim($f, '/')) : $placeholder;

        $agTable = (new ArticleGroup())->getTable();
        $agLabelCol = Schema::hasColumn($agTable, 'article_group')
            ? 'article_group'
            : (Schema::hasColumn($agTable, 'name')
                ? 'name'
                : (Schema::hasColumn($agTable, 'title') ? 'title' : null));

        if (!$agLabelCol) {
            abort(500, "ArticleGroup table '{$agTable}' must have column 'article_group' or 'name' or 'title'.");
        }

        $gTable = (new MasterSetGroup())->getTable();
        $gNameCol = Schema::hasColumn($gTable, 'name') ? 'name' : 'title';
        $gDescCol = Schema::hasColumn($gTable, 'description') ? 'description' : 'desc';

        $groupRows = DB::table('master_set_groups as g')
            ->join('article_groups as ag', 'ag.id', '=', 'g.article_group_id')
            ->leftJoin('master_set_group_master_set as piv', 'piv.master_set_group_id', '=', 'g.id')
            ->leftJoin('master_sets as ms', 'ms.id', '=', 'piv.master_set_id')
            ->leftJoin('master_set_components as msc', 'msc.master_set_id', '=', 'ms.id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'msc.product_id')
            ->when($articleGroupId, fn($qq) => $qq->where('g.article_group_id', $articleGroupId))
            ->when($q !== '', function ($qq) use ($q, $gNameCol, $gDescCol) {
                $qq->where(function ($w) use ($q, $gNameCol, $gDescCol) {
                    $w->where("g.$gNameCol", 'like', "%{$q}%")
                        ->orWhere("g.$gDescCol", 'like', "%{$q}%");
                });
            })
            ->whereNull('g.deleted_at')
            ->where(function ($w) {
                $w->whereNull('ms.deleted_at')->orWhereNull('ms.id');
            })
            ->where(function ($w) {
                $w->where('ms.status', 'Published')->orWhereNull('ms.id');
            })
            ->groupBy(
                'g.id',
                'g.article_group_id',
                "g.$gNameCol",
                "g.$gDescCol",
                'g.color',
                'ag.id',
                "ag.$agLabelCol",
                'ag.image'
            )
            ->selectRaw("
                g.id as group_id,
                g.article_group_id,
                g.$gNameCol as group_name,
                g.$gDescCol as group_description,
                g.color as group_color,

                ag.id as ag_id,
                ag.$agLabelCol as article_group_label,
                ag.image as article_group_image,

                COUNT(DISTINCT ms.id) as master_sets_count,
                MIN(pi.image) as cover_product_image
            ")
            ->orderBy("g.$gNameCol")
            ->get();

        $previewRows = DB::table('master_set_group_master_set as piv')
            ->join('master_sets as ms', 'ms.id', '=', 'piv.master_set_id')
            ->leftJoin('master_set_components as msc', 'msc.master_set_id', '=', 'ms.id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'msc.product_id')
            ->whereNull('ms.deleted_at')
            ->where('ms.status', 'Published')
            ->when($articleGroupId, fn($qq) => $qq->where('ms.article_group_id', $articleGroupId))
            ->groupBy(
                'piv.master_set_group_id',
                'ms.id',
                'ms.name',
                'ms.description',
                'ms.count_offer',
                'ms.count_copy',
                'ms.is_locked'
            )
            ->selectRaw("
                piv.master_set_group_id as group_id,
                ms.id as master_set_id,
                ms.name as master_set_name,
                ms.description as master_set_description,
                ms.count_offer,
                ms.count_copy,
                ms.is_locked,
                MIN(pi.image) as master_set_thumb
            ")
            ->orderByDesc('ms.id')
            ->get();

        $previewsByGroup = [];
        foreach ($previewRows as $r) {
            $gid = (int) $r->group_id;
            $previewsByGroup[$gid] ??= [];

            if (count($previewsByGroup[$gid]) >= 6) {
                continue;
            }

            $previewsByGroup[$gid][] = [
                'id' => (int) $r->master_set_id,
                'name' => (string) $r->master_set_name,
                'description' => (string) ($r->master_set_description ?? ''),
                'count_offer' => (int) ($r->count_offer ?? 0),
                'count_copy' => (int) ($r->count_copy ?? 0),
                'is_locked' => (int) ($r->is_locked ?? 1),
                'image' => $r->master_set_thumb,
                'image_url' => $prodImgUrl($r->master_set_thumb),
            ];
        }

        $bucket = [];
        foreach ($groupRows as $g) {
            $agName = (string) ($g->article_group_label ?? 'Ohne Gruppe');

            $bucket[$agName] ??= [
                'article_group' => $agName,
                'article_group_id' => (int) $g->article_group_id,
                'article_group_image' => $g->article_group_image,
                'article_group_image_url' => $g->article_group_image
                    ? asset('images/article-groups/' . ltrim($g->article_group_image, '/'))
                    : $placeholder,
                'group_sets' => [],
            ];

            $gid = (int) $g->group_id;

            $bucket[$agName]['group_sets'][] = [
                'id' => $gid,
                'name' => (string) $g->group_name,
                'description' => (string) ($g->group_description ?? ''),
                'color' => $g->group_color ?? null,
                'master_sets_count' => (int) ($g->master_sets_count ?? 0),
                'cover_image' => $g->cover_product_image,
                'cover_image_url' => $prodImgUrl($g->cover_product_image),
                'master_sets_preview' => $previewsByGroup[$gid] ?? [],
            ];
        }

        return response()->json([
            'q' => $q,
            'context' => $ctx,
            'groups' => array_values($bucket),
        ]);
    }

    public function groupSetShow(Request $request, MasterSetGroup $group)
    {
        $context = strtolower((string) $request->get('context', 'angebot'));
        $placeholder = asset('images/icons/placeholder.svg');

        $prodImgUrl = function (?string $file) use ($placeholder): string {
            if (!$file) {
                return $placeholder;
            }

            $file = ltrim($file, '/');
            $file = preg_replace('~^images/products/~', '', $file);

            return asset('images/products/' . $file);
        };

        $agTable = (new ArticleGroup())->getTable();
        $agLabelCol = Schema::hasColumn($agTable, 'article_group')
            ? 'article_group'
            : (Schema::hasColumn($agTable, 'name')
                ? 'name'
                : (Schema::hasColumn($agTable, 'title') ? 'title' : null));

        if (!$agLabelCol) {
            abort(500, "ArticleGroup table '{$agTable}' must have column 'article_group' or 'name' or 'title'.");
        }

        $group->load([
            'articleGroup' => function ($q) use ($agLabelCol) {
                $q->select('id', $agLabelCol, 'image');
            },
            'masterSets' => function ($q) use ($context) {
                $q->where('status', 'Published')
                    ->whereNull('deleted_at')
                    ->with([
                        'creator:id,name,lastname',
                        'components' => function ($c) use ($context) {
                            $c->whereNull('parent_id')
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->with([
                                    'product:id,product,measure_unit,price_unit',
                                    'product.images:id,product_id,image',
                                    'position:id,position,description,qualification_id,qualification,price',
                                    'distributor:id,name,short_name,email,phone,city,image,status,payment_terms,cash_discount',
                                    'distributorPrice:id,distributor_id,product_id,discount_group_id,article_no,discount_price,discount_percent,price,purchase_price,price_date,availability,status,creator_id,notice',
                                    'children' => function ($ch) use ($context) {
                                        $ch->orderBy('sort_order')
                                            ->orderBy('id')
                                            ->with([
                                                'product:id,product,measure_unit,price_unit',
                                                'product.images:id,product_id,image',
                                                'position:id,position,description,qualification_id,qualification,price',
                                                'distributor:id,name,short_name,email,phone,city,image,status,payment_terms,cash_discount',
                                                'distributorPrice:id,distributor_id,product_id,discount_group_id,article_no,discount_price,discount_percent,price,purchase_price,price_date,availability,status,creator_id,notice',
                                                'descriptionVariants' => function ($dv) use ($context) {
                                                    $dv->where('context', $context)
                                                        ->orderBy('sort_order')
                                                        ->orderBy('id');
                                                },
                                            ]);
                                    },
                                    'descriptionVariants' => function ($dv) use ($context) {
                                        $dv->where('context', $context)
                                            ->orderBy('sort_order')
                                            ->orderBy('id');
                                    },
                                ]);
                        },
                        'labor' => function ($l) {
                            $l->orderBy('sort_order')
                                ->orderBy('id')
                                ->with([
                                    'department:id,department_name',
                                    'position:id,position',
                                    'employee:id,name,lastname',
                                    'qualification:id,name,default_price',
                                ]);
                        },
                    ]);
            },
        ]);

        $positionLabel = function ($position): string {
            if (!$position) {
                return '';
            }

            return trim((string) (
                $position->position
                ?? $position->description
                ?? $position->qualification
                ?? ''
            ));
        };

        $mapDistributor = function ($distributor) {
            if (!$distributor) {
                return null;
            }

            return [
                'id' => (int) $distributor->id,
                'name' => (string) ($distributor->name ?? ''),
                'short_name' => (string) ($distributor->short_name ?? ''),
                'email' => (string) ($distributor->email ?? ''),
                'phone' => (string) ($distributor->phone ?? ''),
                'city' => (string) ($distributor->city ?? ''),
                'image' => $distributor->image ?? null,
                'status' => (string) ($distributor->status ?? ''),
                'payment_terms' => (int) ($distributor->payment_terms ?? 0),
                'cash_discount' => (float) ($distributor->cash_discount ?? 0),
            ];
        };

        $mapDistributorPrice = function ($price) {
            if (!$price) {
                return null;
            }

            return [
                'id' => (int) $price->id,
                'distributor_id' => $price->distributor_id ? (int) $price->distributor_id : null,
                'product_id' => $price->product_id ? (int) $price->product_id : null,
                'discount_group_id' => $price->discount_group_id ? (int) $price->discount_group_id : null,
                'article_no' => (string) ($price->article_no ?? ''),
                'discount_price' => $price->discount_price !== null ? (float) $price->discount_price : null,
                'discount_percent' => $price->discount_percent !== null ? (float) $price->discount_percent : null,
                'price' => $price->price !== null ? (float) $price->price : null,
                'purchase_price' => $price->purchase_price !== null ? (float) $price->purchase_price : null,
                'price_date' => $price->price_date ?? null,
                'availability' => $price->availability ?? null,
                'status' => (string) ($price->status ?? ''),
                'creator_id' => $price->creator_id ? (int) $price->creator_id : null,
                'notice' => (string) ($price->notice ?? ''),
            ];
        };

        $groupCover = null;
        foreach ($group->masterSets as $set) {
            foreach ($set->components as $component) {
                $groupCover = $component->product?->images?->first()?->image
                    ?: $component->children?->first()?->product?->images?->first()?->image;

                if ($groupCover) {
                    break 2;
                }
            }
        }

        $masterSets = $group->masterSets->map(function ($set) use ($prodImgUrl, $positionLabel, $mapDistributor, $mapDistributorPrice) {
            $setThumb = null;

            foreach ($set->components as $component) {
                $setThumb = $component->product?->images?->first()?->image
                    ?: $component->children?->first()?->product?->images?->first()?->image;

                if ($setThumb) {
                    break;
                }
            }

            $components = $set->components->map(function ($comp) use ($prodImgUrl, $positionLabel, $mapDistributor, $mapDistributorPrice) {
                $unit = (string) ($comp->measure ?: ($comp->price_unit ?: ($comp->product?->measure_unit ?? $comp->product?->price_unit ?? 'Stk')));

                $variant = $comp->descriptionVariants->first();
                $descHtml = $variant?->html ?? $comp->description ?? '';
                $descText = $variant?->text ?? strip_tags((string) $descHtml);

                $imgFile = $comp->product?->images?->first()?->image;

                $qty = (float) ($comp->qty ?? 1);
                $vk = (float) ($comp->unit_price ?? 0);
                $ek = (float) ($comp->purchase_price ?? 0);

                $resolvedSkonto = $comp->skonto !== null
                    ? (float) $comp->skonto
                    : (float) ($comp->distributor?->cash_discount ?? 0);

                $resolvedPaymentTerms = $comp->payment_terms !== null
                    ? (int) $comp->payment_terms
                    : (int) ($comp->distributor?->payment_terms ?? 14);

                $children = $comp->children->map(function ($child) use ($prodImgUrl, $positionLabel, $mapDistributor, $mapDistributorPrice) {
                    $unit = (string) ($child->measure ?: ($child->price_unit ?: ($child->product?->measure_unit ?? $child->product?->price_unit ?? 'Stk')));

                    $variant = $child->descriptionVariants->first();
                    $descHtml = $variant?->html ?? $child->description ?? '';
                    $descText = $variant?->text ?? strip_tags((string) $descHtml);

                    $imgFile = $child->product?->images?->first()?->image;

                    $qty = (float) ($child->qty ?? 1);
                    $vk = (float) ($child->unit_price ?? 0);
                    $ek = (float) ($child->purchase_price ?? 0);

                    $resolvedSkonto = $child->skonto !== null
                        ? (float) $child->skonto
                        : (float) ($child->distributor?->cash_discount ?? 0);

                    $resolvedPaymentTerms = $child->payment_terms !== null
                        ? (int) $child->payment_terms
                        : (int) ($child->distributor?->payment_terms ?? 14);

                    return [
                        'id' => (int) $child->id,
                        'master_set_id' => (int) ($child->master_set_id ?? 0),
                        'parent_id' => $child->parent_id ? (int) $child->parent_id : null,

                        'product_id' => (int) ($child->product_id ?? 0),
                        'position_id' => $child->position_id ? (int) $child->position_id : null,
                        'position_name' => $positionLabel($child->position),

                        'article_no' => (string) ($child->article_no ?? $child->product?->article_no ?? ''),
                        'distributor_article_no' => (string) ($child->distributor_article_no ?? $child->distributorPrice?->article_no ?? ''),
                        'distributor_id' => $child->distributor_id ? (int) $child->distributor_id : null,
                        'distributor_price_id' => $child->distributor_price_id ? (int) $child->distributor_price_id : null,
                        'distributor' => $mapDistributor($child->distributor),
                        'distributor_price' => $mapDistributorPrice($child->distributorPrice),

                        'name' => $child->product?->product ?? 'Unterkomponente',
                        'qty' => $qty,
                        'unit' => $unit,

                        'unit_price' => $vk,
                        'purchase_price' => $ek,
                        'margin' => (float) ($child->margin ?? 0),

                        'skonto' => $resolvedSkonto,
                        'payment_terms' => $resolvedPaymentTerms,
                        'availability' => $child->availability,

                        'type' => (string) ($child->type ?? 'haupt'),
                        'is_stammartikel' => (bool) ($child->is_stammartikel ?? false),
                        'is_favorite' => (bool) ($child->is_favorite ?? false),

                        'measure' => (string) ($child->measure ?? $unit),
                        'vpe' => (float) ($child->vpe ?? 1),
                        'price_unit' => (string) ($child->price_unit ?? $child->product?->price_unit ?? ''),

                        'sort_order' => (int) ($child->sort_order ?? 0),

                        'total' => $vk * $qty,
                        'description' => (string) ($child->description ?? ''),
                        'description_variant_html' => $descHtml,
                        'description_variant_text' => $descText,

                        'image' => $imgFile,
                        'image_url' => $prodImgUrl($imgFile),
                    ];
                })->values();

                return [
                    'id' => (int) $comp->id,
                    'master_set_id' => (int) ($comp->master_set_id ?? 0),
                    'parent_id' => $comp->parent_id ? (int) $comp->parent_id : null,

                    'product_id' => (int) ($comp->product_id ?? 0),
                    'position_id' => $comp->position_id ? (int) $comp->position_id : null,
                    'position_name' => $positionLabel($comp->position),

                    'article_no' => (string) ($comp->article_no ?? $comp->product?->article_no ?? ''),
                    'distributor_article_no' => (string) ($comp->distributor_article_no ?? $comp->distributorPrice?->article_no ?? ''),
                    'distributor_id' => $comp->distributor_id ? (int) $comp->distributor_id : null,
                    'distributor_price_id' => $comp->distributor_price_id ? (int) $comp->distributor_price_id : null,
                    'distributor' => $mapDistributor($comp->distributor),
                    'distributor_price' => $mapDistributorPrice($comp->distributorPrice),

                    'name' => $comp->product?->product ?? 'Komponente',
                    'qty' => $qty,
                    'unit' => $unit,

                    'unit_price' => $vk,
                    'purchase_price' => $ek,
                    'margin' => (float) ($comp->margin ?? 0),

                    'skonto' => $resolvedSkonto,
                    'payment_terms' => $resolvedPaymentTerms,
                    'availability' => $comp->availability,

                    'type' => (string) ($comp->type ?? 'haupt'),
                    'is_stammartikel' => (bool) ($comp->is_stammartikel ?? false),
                    'is_favorite' => (bool) ($comp->is_favorite ?? false),

                    'measure' => (string) ($comp->measure ?? $unit),
                    'vpe' => (float) ($comp->vpe ?? 1),
                    'price_unit' => (string) ($comp->price_unit ?? $comp->product?->price_unit ?? ''),

                    'sort_order' => (int) ($comp->sort_order ?? 0),

                    'total' => $vk * $qty,
                    'description' => (string) ($comp->description ?? ''),
                    'description_variant_html' => $descHtml,
                    'description_variant_text' => $descText,

                    'image' => $imgFile,
                    'image_url' => $prodImgUrl($imgFile),
                    'children' => $children,
                ];
            })->values();

            $labor = $set->labor->map(function ($l) {
                $name = $l->employee
                    ? trim(($l->employee->name ?? '') . ' ' . ($l->employee->lastname ?? ''))
                    : ($l->position->position ?? ($l->department->department_name ?? 'Dienstleistung'));

                $hours = (float) ($l->hours ?? 1);
                $rate = (float) ($l->rate ?? 0);

                return [
                    'id' => (int) $l->id,
                    'name' => $name ?: 'Dienstleistung',
                    'rate' => $rate,
                    'hourly_rate' => $rate,
                    'hours' => $hours,
                    'total' => $rate * $hours,
                    'sort_order' => (int) ($l->sort_order ?? 0),
                ];
            })->values();

            return [
                'id' => (int) $set->id,
                'name' => (string) $set->name,
                'description_html' => (string) ($set->description ?? ''),
                'description_text' => strip_tags((string) ($set->description ?? '')),
                'status' => (string) ($set->status ?? 'Published'),

                'creator_id' => $set->creator_id ? (int) $set->creator_id : null,
                'creator_name' => $set->creator
                    ? trim(($set->creator->name ?? '') . ' ' . ($set->creator->lastname ?? ''))
                    : null,

                'count_copy' => (int) ($set->count_copy ?? 0),
                'count_offer' => (int) ($set->count_offer ?? 0),
                'is_locked' => (int) ($set->is_locked ?? 1),

                'image' => $setThumb,
                'image_url' => $prodImgUrl($setThumb),

                'components' => $components,
                'labor' => $labor,
            ];
        })->values();

        return response()->json([
            'id' => (int) $group->id,
            'name' => (string) $group->name,
            'description' => (string) ($group->description ?? ''),
            'color' => $group->color ?? null,

            'article_group' => (string) (optional($group->articleGroup)->{$agLabelCol} ?? ''),
            'article_group_image' => $group->articleGroup?->image ?? null,
            'article_group_image_url' => $group->articleGroup?->image
                ? asset('images/article-groups/' . ltrim($group->articleGroup->image, '/'))
                : $placeholder,

            'image' => $groupCover,
            'image_url' => $prodImgUrl($groupCover),

            'master_sets' => $masterSets,
        ]);
    }

    public function productsList(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $limit = (int) $request->get('limit', 80);
        $limit = max(10, min(200, $limit));

        $placeholder = asset('images/icons/placeholder.svg');

        $imgUrlFromFile = function (?string $file) use ($placeholder): string {
            if (!$file) {
                return $placeholder;
            }

            return asset('images/products/' . ltrim($file, '/'));
        };

        $bestSub = DB::table('distributor_prices as dp')
            ->selectRaw('dp.product_id, MIN(COALESCE(dp.purchase_price, dp.discount_price, dp.price)) as best_price')
            ->where('dp.status', 'Published')
            ->groupBy('dp.product_id');

        $rows = Product::query()
            ->from('products as p')
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->leftJoinSub($bestSub, 'bp', function ($join) {
                $join->on('bp.product_id', '=', 'p.id');
            })
            ->leftJoin('distributor_prices as dp2', function ($join) {
                $join->on('dp2.product_id', '=', 'p.id')
                    ->where('dp2.status', '=', 'Published')
                    ->whereRaw('COALESCE(dp2.purchase_price, dp2.discount_price, dp2.price) = bp.best_price');
            })
            ->leftJoin('distributors as d', 'd.id', '=', 'dp2.distributor_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('p.product', 'like', "%{$q}%")
                        ->orWhere('p.article_no', 'like', "%{$q}%")
                        ->orWhere('p.model', 'like', "%{$q}%")
                        ->orWhere('b.name', 'like', "%{$q}%")
                        ->orWhere('d.name', 'like', "%{$q}%");
                });
            })
            ->groupBy(
                'p.id',
                'p.product',
                'p.model',
                'p.article_no',
                'p.price_unit',
                'p.package_unit',
                'p.measure_unit',
                'p.brand_id',
                'b.name',
                'bp.best_price',
                'd.name',
                'd.payment_terms',
                'd.cash_discount'
            )
            ->orderBy('p.product')
            ->limit($limit)
            ->get([
                'p.id',
                'p.product',
                'p.model',
                'p.article_no',
                'p.price_unit',
                'p.package_unit',
                'p.measure_unit',
                'p.brand_id',
                'b.name as brand_name',
                DB::raw('COALESCE(bp.best_price, NULL) as best_price'),
                'd.name as distributor_name',
                'd.payment_terms as payment_terms',
                'd.cash_discount as skonto',
                DB::raw('MIN(pi.image) as product_image'),
            ]);

        $items = $rows->map(function ($r) use ($imgUrlFromFile) {
            return [
                'id' => (int) $r->id,
                'product' => (string) $r->product,
                'model' => (string) ($r->model ?? ''),
                'article_no' => (string) ($r->article_no ?? ''),
                'brand_name' => (string) ($r->brand_name ?? ''),
                'distributor_name' => (string) ($r->distributor_name ?? ''),
                'best_price' => $r->best_price !== null ? (float) $r->best_price : null,
                'payment_terms' => $r->payment_terms !== null ? (int) $r->payment_terms : null,
                'skonto' => $r->skonto !== null ? (float) $r->skonto : null,
                'currency' => 'EUR',
                'image' => $imgUrlFromFile($r->product_image),
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    public function productShow(Product $product)
    {
        $product->load(['brand:id,name', 'images:id,product_id,image']);

        $best = DB::table('distributor_prices as dp')
            ->join('distributors as d', 'd.id', '=', 'dp.distributor_id')
            ->where('dp.product_id', $product->id)
            ->where('dp.status', 'Published')
            ->orderByRaw('COALESCE(dp.purchase_price, dp.discount_price, dp.price) ASC')
            ->select([
                'dp.id as distributor_price_id',
                'dp.distributor_id',
                'dp.product_id',
                'dp.discount_group_id',
                'dp.article_no',
                'dp.discount_price',
                'dp.discount_percent',
                'dp.price',
                'dp.purchase_price',
                'dp.price_date',
                'dp.availability',
                'dp.status',
                'dp.creator_id',
                'dp.notice',
                'd.name as distributor_name',
                'd.short_name as distributor_short_name',
                'd.email as distributor_email',
                'd.phone as distributor_phone',
                'd.city as distributor_city',
                'd.payment_terms as distributor_payment_terms',
                'd.cash_discount as distributor_cash_discount',
                DB::raw('COALESCE(dp.purchase_price, dp.discount_price, dp.price) as best_price'),
            ])
            ->first();

        $placeholder = asset('images/icons/placeholder.svg');

        $imgUrlFromFile = function (?string $file) use ($placeholder): string {
            if (!$file) {
                return $placeholder;
            }

            return asset('images/products/' . ltrim($file, '/'));
        };

        $imgFile = $product->images?->first()?->image;

        return response()->json([
            'id' => (int) $product->id,
            'name' => (string) $product->product,
            'product' => (string) $product->product,
            'model' => (string) ($product->model ?? ''),
            'article_no' => (string) ($product->article_no ?? ''),
            'brand_name' => optional($product->brand)->name,
            'distributor_name' => $best->distributor_name ?? null,
            'price' => $best ? (float) $best->best_price : 0.0,
            'ek' => $best ? (float) ($best->purchase_price ?? $best->best_price) : 0.0,
            'skonto' => $best ? (float) ($best->distributor_cash_discount ?? 0) : 0.0,
            'payment_terms' => $best ? (int) ($best->distributor_payment_terms ?? 14) : 14,
            'image' => $imgUrlFromFile($imgFile),
            'unit' => $product->price_unit ?: 'Stk',

            'distributor_id' => $best?->distributor_id ? (int) $best->distributor_id : null,
            'distributor_price_id' => $best?->distributor_price_id ? (int) $best->distributor_price_id : null,

            'distributor' => $best ? [
                'id' => $best->distributor_id ? (int) $best->distributor_id : null,
                'name' => (string) ($best->distributor_name ?? ''),
                'short_name' => (string) ($best->distributor_short_name ?? ''),
                'email' => (string) ($best->distributor_email ?? ''),
                'phone' => (string) ($best->distributor_phone ?? ''),
                'city' => (string) ($best->distributor_city ?? ''),
                'payment_terms' => (int) ($best->distributor_payment_terms ?? 14),
                'cash_discount' => (float) ($best->distributor_cash_discount ?? 0),
            ] : null,

            'distributor_price' => $best ? [
                'id' => $best->distributor_price_id ? (int) $best->distributor_price_id : null,
                'distributor_id' => $best->distributor_id ? (int) $best->distributor_id : null,
                'product_id' => $best->product_id ? (int) $best->product_id : null,
                'discount_group_id' => $best->discount_group_id ? (int) $best->discount_group_id : null,
                'article_no' => (string) ($best->article_no ?? ''),
                'discount_price' => $best->discount_price !== null ? (float) $best->discount_price : null,
                'discount_percent' => $best->discount_percent !== null ? (float) $best->discount_percent : null,
                'price' => $best->price !== null ? (float) $best->price : null,
                'purchase_price' => $best->purchase_price !== null ? (float) $best->purchase_price : null,
                'price_date' => $best->price_date ?? null,
                'availability' => $best->availability ?? null,
                'status' => (string) ($best->status ?? ''),
                'creator_id' => $best->creator_id ? (int) $best->creator_id : null,
                'notice' => (string) ($best->notice ?? ''),
            ] : null,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $articleGroupId = $request->integer('article_group_id') ?: null;
        $ctx = strtolower((string) $request->get('context', 'angebot'));

        $placeholder = asset('images/icons/placeholder.svg');
        $imgUrlFromFile = fn(?string $file): string => $file
            ? asset('images/products/' . ltrim($file, '/'))
            : $placeholder;

        $masterSetsRows = DB::table('master_sets as ms')
            ->join('article_groups as ag', 'ag.id', '=', 'ms.article_group_id')
            ->leftJoin('master_set_components as msc', 'msc.master_set_id', '=', 'ms.id')
            ->leftJoin('products as p', 'p.id', '=', 'msc.product_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->leftJoin('master_set_component_descriptions as mscd', function ($join) use ($ctx) {
                $join->on('mscd.master_set_component_id', '=', 'msc.id')
                    ->where('mscd.context', '=', $ctx);
            })
            ->when($articleGroupId, fn($qry) => $qry->where('ms.article_group_id', $articleGroupId))
            ->when($q !== '', function ($qry) use ($q) {
                $like = "%{$q}%";
                $qry->where(function ($w) use ($like) {
                    $w->where('ms.name', 'like', $like)
                        ->orWhere('ms.description', 'like', $like)
                        ->orWhere('ag.article_group', 'like', $like)
                        ->orWhere('ag.initial', 'like', $like)
                        ->orWhere('msc.description', 'like', $like)
                        ->orWhere('mscd.title', 'like', $like)
                        ->orWhere('mscd.text', 'like', $like)
                        ->orWhere('mscd.html', 'like', $like);
                });
            })
            ->whereNull('ms.deleted_at')
            ->groupBy(
                'ms.id',
                'ms.article_group_id',
                'ms.name',
                'ms.description',
                'ms.count_offer',
                'ms.count_copy',
                'ms.is_locked',
                'ag.article_group',
                'ag.initial',
                'ag.image'
            )
            ->selectRaw("
                ms.article_group_id as article_group_id,
                ag.article_group,
                ag.initial,
                ag.image,

                'master_set' as item_type,
                ms.id as id,
                COALESCE(NULLIF(TRIM(ms.name),''), CONCAT('MasterSet #', ms.id)) as name,

                TRIM(
                    CONCAT_WS('\n',
                        NULLIF(TRIM(ms.description), ''),
                        NULLIF(TRIM(
                            GROUP_CONCAT(
                                DISTINCT NULLIF(TRIM(msc.description), '')
                                ORDER BY msc.sort_order ASC
                                SEPARATOR '\n'
                            )
                        ), ''),
                        NULLIF(TRIM(
                            GROUP_CONCAT(
                                DISTINCT NULLIF(TRIM(mscd.text), '')
                                ORDER BY msc.sort_order ASC, mscd.sort_order ASC, mscd.id ASC
                                SEPARATOR '\n'
                            )
                        ), '')
                    )
                ) as description,

                ms.count_offer,
                ms.count_copy,
                ms.is_locked,
                COUNT(DISTINCT msc.id) as components_count,
                MIN(pi.image) as ms_thumb_image
            ")
            ->orderBy('ag.article_group')
            ->orderByDesc('ms.id')
            ->get();

        $groups = [];
        foreach ($masterSetsRows as $r) {
            $gid = (int) $r->article_group_id;

            if (!isset($groups[$gid])) {
                $groups[$gid] = [
                    'article_group_id' => $gid,
                    'article_group' => (string) $r->article_group,
                    'initial' => (string) $r->initial,
                    'image' => $r->image,
                    'master_sets' => [],
                    'products' => [],
                ];
            }

            $groups[$gid]['master_sets'][] = [
                'item_type' => 'master_set',
                'id' => (int) $r->id,
                'name' => (string) $r->name,
                'description' => (string) ($r->description ?? ''),
                'count_offer' => (int) ($r->count_offer ?? 0),
                'count_copy' => (int) ($r->count_copy ?? 0),
                'is_locked' => (int) ($r->is_locked ?? 1),
                'components_count' => (int) $r->components_count,
                'label' => "SET • {$r->name} ({$r->components_count} Teile)",
                'image_url' => $imgUrlFromFile($r->ms_thumb_image),
            ];
        }

        /*
        |----------------------------------------------------------------------
        | Important:
        | We pick ONE best distributor price row per product.
        | This avoids random MIN(dp.article_no) from a different distributor.
        |----------------------------------------------------------------------
        */
        $bestDpSub = DB::table('distributor_prices as dp')
            ->selectRaw("
                dp.id,
                dp.product_id,
                dp.distributor_id,
                dp.article_no,
                dp.purchase_price,
                dp.discount_price,
                dp.price,
                dp.status,
                ROW_NUMBER() OVER (
                    PARTITION BY dp.product_id
                    ORDER BY COALESCE(dp.purchase_price, dp.discount_price, dp.price) ASC, dp.id ASC
                ) as rn
            ")
            ->where('dp.status', 'Published');

        $productsRows = DB::table('products as p')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'p.article_group')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->leftJoinSub($bestDpSub, 'bdp', function ($join) {
                $join->on('bdp.product_id', '=', 'p.id')
                    ->where('bdp.rn', '=', 1);
            })
            ->leftJoin('distributors as d', 'd.id', '=', 'bdp.distributor_id')
            ->when($articleGroupId, fn($qry) => $qry->where('p.article_group', $articleGroupId))
            ->when($q !== '', function ($qry) use ($q) {
                $like = "%{$q}%";
                $qry->where(function ($w) use ($like) {
                    $w->where('p.product', 'like', $like)
                        ->orWhere('p.model', 'like', $like)
                        ->orWhere('p.article_no', 'like', $like)
                        ->orWhere('p.ean', 'like', $like)
                        ->orWhere('ag.article_group', 'like', $like)
                        ->orWhere('ag.initial', 'like', $like)
                        ->orWhere('bdp.article_no', 'like', $like)
                        ->orWhere('d.name', 'like', $like)
                        ->orWhere('d.short_name', 'like', $like);
                });
            })
            ->where('p.status', 'active')
            ->groupBy(
                'p.id',
                'p.product',
                'p.article_no',
                'p.model',
                'p.category',
                'p.article_group',
                'ag.article_group',
                'ag.initial',
                'ag.image',
                'bdp.id',
                'bdp.distributor_id',
                'bdp.article_no',
                'd.payment_terms',
                'd.cash_discount'
            )
            ->selectRaw("
                p.id,
                p.product as name,
                p.article_no,
                p.model,
                p.category,
                p.article_group as article_group_id,
                ag.article_group,
                ag.initial,
                ag.image as article_group_image,
                MIN(pi.image) as product_image,

                bdp.id as distributor_price_id,
                bdp.distributor_id as distributor_id,
                bdp.article_no as distributor_article_no,

                d.payment_terms as payment_terms,
                d.cash_discount as skonto
            ")
            ->orderBy('ag.article_group')
            ->orderByDesc('p.id')
            ->limit(200)
            ->get();

        foreach ($productsRows as $p) {
            $gid = (int) $p->article_group_id;

            if (!isset($groups[$gid])) {
                $groups[$gid] = [
                    'article_group_id' => $gid,
                    'article_group' => (string) $p->article_group,
                    'initial' => (string) $p->initial,
                    'image' => $p->article_group_image,
                    'master_sets' => [],
                    'products' => [],
                ];
            }

            $groups[$gid]['products'][] = [
                'item_type' => 'product',
                'id' => (int) $p->id,
                'name' => (string) $p->name,

                // Hersteller-Nr
                'article_no' => (string) ($p->article_no ?? ''),

                // Lieferanten-Nr
                'distributor_article_no' => (string) ($p->distributor_article_no ?? ''),

                'distributor_id' => $p->distributor_id ? (int) $p->distributor_id : null,
                'distributor_price_id' => $p->distributor_price_id ? (int) $p->distributor_price_id : null,

                'model' => (string) ($p->model ?? ''),
                'category' => (string) ($p->category ?? ''),
                'payment_terms' => $p->payment_terms !== null ? (int) $p->payment_terms : null,
                'skonto' => $p->skonto !== null ? (float) $p->skonto : null,
                'image_url' => $imgUrlFromFile($p->product_image),
                'label' => "PROD • {$p->name}",
            ];
        }

        $groupsList = array_values($groups);
        usort($groupsList, fn($a, $b) => strcasecmp($a['article_group'] ?? '', $b['article_group'] ?? ''));

        return response()->json([
            'q' => $q,
            'context' => $ctx,
            'groups' => $groupsList,
        ]);
    }

    public function showJson(Request $request, MasterSet $masterSet)
    {
        $ctx = strtolower((string) $request->get('context', 'angebot'));

        $masterSet->load([
            'articleGroup',
            'creator:id,name,lastname',
            'components' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'components.product.images',
            'components.position:id,position,description,qualification_id,qualification,price',
            'components.distributor:id,name,short_name,email,phone,city,image,status,payment_terms,cash_discount',
            'components.distributorPrice:id,distributor_id,product_id,discount_group_id,article_no,discount_price,discount_percent,price,purchase_price,price_date,availability,status,creator_id,notice',
            'components.descriptionVariants' => function ($q) use ($ctx) {
                $q->orderBy('sort_order')->orderBy('id');
            },
            'components.children' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'components.children.product.images',
            'components.children.position:id,position,description,qualification_id,qualification,price',
            'components.children.distributor:id,name,short_name,email,phone,city,image,status,payment_terms,cash_discount',
            'components.children.distributorPrice:id,distributor_id,product_id,discount_group_id,article_no,discount_price,discount_percent,price,purchase_price,price_date,availability,status,creator_id,notice',
            'components.children.descriptionVariants' => function ($q) use ($ctx) {
                $q->orderBy('sort_order')->orderBy('id');
            },
            'labor' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'labor.qualification',
        ]);

        $placeholder = asset('images/icons/placeholder.svg');

        $imgUrlFromFile = function (?string $file) use ($placeholder): string {
            if (!$file) {
                return $placeholder;
            }

            return asset('images/products/' . ltrim($file, '/'));
        };

        $productImageUrl = function ($p) use ($imgUrlFromFile): string {
            if (!$p) {
                return $imgUrlFromFile(null);
            }

            $img = $p->images?->first()?->image;
            return $imgUrlFromFile($img);
        };

        $componentUnit = function ($component, $p): string {
            if ($component && !empty($component->measure)) {
                return (string) $component->measure;
            }

            if (!$p) {
                return 'Stk';
            }

            return (string) ($p->price_unit ?? $p->measure_unit ?? 'Stk');
        };

        $positionLabel = function ($pos): string {
            if (!$pos) {
                return '';
            }

            return trim((string) ($pos->name ?? $pos->title ?? ''));
        };

        $productLabel = function ($p): string {
            if (!$p) {
                return '';
            }

            return trim((string) ($p->product ?? $p->model ?? $p->article_no ?? $p->sku ?? ''));
        };

        $mapVariants = function ($component) use ($ctx) {
            $all = $component->descriptionVariants ?? collect();
            $byContext = $all->groupBy(fn($d) => strtolower((string) $d->context));

            $contexts = [];
            foreach ($byContext as $c => $items) {
                $contexts[$c] = $items->values()->map(fn($d) => [
                    'id' => (int) $d->id,
                    'context' => (string) $d->context,
                    'title' => (string) ($d->title ?? ''),
                    'sort_order' => (int) ($d->sort_order ?? 0),
                    'delta' => $d->delta,
                    'html' => (string) ($d->html ?? ''),
                    'text' => (string) ($d->text ?? ''),
                ])->all();
            }

            $defaultVariant = $contexts[$ctx][0] ?? null;

            return [$contexts, $defaultVariant];
        };

        $mapDistributor = function ($distributor) {
            if (!$distributor) {
                return null;
            }

            return [
                'id' => (int) $distributor->id,
                'name' => (string) ($distributor->name ?? ''),
                'short_name' => (string) ($distributor->short_name ?? ''),
                'email' => (string) ($distributor->email ?? ''),
                'phone' => (string) ($distributor->phone ?? ''),
                'city' => (string) ($distributor->city ?? ''),
                'image' => $distributor->image ?? null,
                'status' => (string) ($distributor->status ?? ''),
                'payment_terms' => (int) ($distributor->payment_terms ?? 0),
                'cash_discount' => (float) ($distributor->cash_discount ?? 0),
            ];
        };

        $mapDistributorPrice = function ($price) {
            if (!$price) {
                return null;
            }

            return [
                'id' => (int) $price->id,
                'distributor_id' => $price->distributor_id ? (int) $price->distributor_id : null,
                'product_id' => $price->product_id ? (int) $price->product_id : null,
                'discount_group_id' => $price->discount_group_id ? (int) $price->discount_group_id : null,
                'article_no' => (string) ($price->article_no ?? ''),
                'discount_price' => $price->discount_price !== null ? (float) $price->discount_price : null,
                'discount_percent' => $price->discount_percent !== null ? (float) $price->discount_percent : null,
                'price' => $price->price !== null ? (float) $price->price : null,
                'purchase_price' => $price->purchase_price !== null ? (float) $price->purchase_price : null,
                'price_date' => $price->price_date ?? null,
                'availability' => $price->availability ?? null,
                'status' => (string) ($price->status ?? ''),
                'creator_id' => $price->creator_id ? (int) $price->creator_id : null,
                'notice' => (string) ($price->notice ?? ''),
            ];
        };

        $mapComponent = function ($c) use ($productLabel, $positionLabel, $productImageUrl, $componentUnit, $mapVariants, $mapDistributor, $mapDistributorPrice) {
            $pName = $productLabel($c->product);
            $pos = $positionLabel($c->position);

            $name = trim(implode(' • ', array_filter([$pName, $pos])))
                ?: ($c->description ?: 'Komponente');

            $qty = (float) ($c->qty ?? 0);
            $vk = (float) ($c->unit_price ?? 0);
            $ek = (float) ($c->purchase_price ?? 0);

            [$descVariantsByContext, $defaultVariant] = $mapVariants($c);

            $resolvedSkonto = $c->skonto !== null
                ? (float) $c->skonto
                : (float) ($c->distributor?->cash_discount ?? 0);

            $resolvedPaymentTerms = $c->payment_terms !== null
                ? (int) $c->payment_terms
                : (int) ($c->distributor?->payment_terms ?? 14);

            $manufacturerArticleNo = (string) (
                $c->article_no
                ?? $c->product?->article_no
                ?? ''
            );

            $distributorArticleNo = (string) (
                $c->distributor_article_no
                ?? $c->distributorPrice?->article_no
                ?? ''
            );

            return [
                'type' => 'component',
                'id' => (int) $c->id,
                'name' => $name,
                'description' => (string) ($c->description ?? ''),
                'description_variants' => $descVariantsByContext,
                'description_default' => $defaultVariant,

                'parent_id' => $c->parent_id ? (int) $c->parent_id : null,
                'product_id' => $c->product_id ? (int) $c->product_id : null,
                'position_id' => $c->position_id ? (int) $c->position_id : null,
                'position_name' => $positionLabel($c->position),

                // Hersteller-Nr
                'article_no' => $manufacturerArticleNo,

                // Lieferanten-Nr
                'distributor_article_no' => $distributorArticleNo,

                'distributor_id' => $c->distributor_id ? (int) $c->distributor_id : null,
                'distributor_price_id' => $c->distributor_price_id ? (int) $c->distributor_price_id : null,
                'distributor' => $mapDistributor($c->distributor),
                'distributor_price' => $mapDistributorPrice($c->distributorPrice),

                'qty' => $qty,
                'unit' => $componentUnit($c, $c->product),
                'measure' => (string) ($c->measure ?? $componentUnit($c, $c->product)),
                'price_unit' => (string) ($c->price_unit ?? $c->product?->price_unit ?? ''),
                'vpe' => (float) ($c->vpe ?? 1),

                'unit_price' => $vk,
                'purchase_price' => $ek,
                'margin' => (float) ($c->margin ?? 0),
                'skonto' => $resolvedSkonto,
                'payment_terms' => $resolvedPaymentTerms,

                'availability' => $c->availability,
                'component_type' => (string) ($c->type ?? 'haupt'),
                'is_stammartikel' => (bool) ($c->is_stammartikel ?? false),
                'is_favorite' => (bool) ($c->is_favorite ?? false),

                'total' => (float) ($vk * $qty),
                'image_url' => $productImageUrl($c->product),
                'children' => [],
                'sort_order' => (int) ($c->sort_order ?? 0),
                'sort_key' => sprintf('C-%06d-%06d', (int) ($c->sort_order ?? 0), (int) $c->id),
            ];
        };

        $componentItems = $masterSet->components
            ->whereNull('parent_id')
            ->values()
            ->map(function ($c) use ($mapComponent) {
                $row = $mapComponent($c);

                $row['children'] = $c->children
                    ? $c->children->values()->map(fn($cc) => $mapComponent($cc))->all()
                    : [];

                return $row;
            });

        $laborChildren = $masterSet->labor
            ->values()
            ->map(function ($l) {
                $hours = (float) ($l->hours ?? 0);
                $qualName = (string) ($l->qualification?->name ?? 'Qualifikation');
                $qualPrice = (float) ($l->qualification?->default_price ?? 0);

                $rate = (float) ($l->hourly_rate ?? 0);
                if ($rate <= 0) {
                    $rate = $qualPrice;
                }

                $margin = $qualPrice > 0 ? (($rate - $qualPrice) / $qualPrice) * 100 : 100;

                return [
                    'type' => 'labor_row',
                    'id' => (int) $l->id,
                    'qualification_id' => (int) ($l->qualification_id ?? 0),
                    'qualification_name' => $qualName,
                    'qualification_price' => $qualPrice,
                    'ek' => $qualPrice,
                    'margin_percent' => $margin,
                    'hours' => $hours,
                    'qty' => $hours,
                    'rate' => $rate,
                    'hourly_rate' => $rate,
                    'total' => (float) ($rate * $hours),
                    'sort_order' => (int) ($l->sort_order ?? 0),
                    'sort_key' => sprintf('L-%06d-%06d', (int) ($l->sort_order ?? 0), (int) $l->id),
                ];
            })
            ->sortBy('sort_key')
            ->values();

        $laborCount = (int) $laborChildren->count();
        $firstLaborSort = $laborCount ? (int) ($laborChildren->min('sort_order') ?? 999999) : 999999;

        $laborGroupItem = $laborCount
            ? collect([
                [
                    'type' => 'labor',
                    'id' => 0,
                    'name' => 'Arbeitsleistung',
                    'qty' => $laborCount,
                    'hours' => (float) $laborChildren->sum('hours'),
                    'unit' => 'Std',
                    'unit_price' => null,
                    'rate' => null,
                    'total' => (float) $laborChildren->sum('total'),
                    'children' => $laborChildren->all(),
                    'sort_order' => $firstLaborSort,
                    'sort_key' => sprintf('L-%06d-GROUP', $firstLaborSort),
                ]
            ])
            : collect([]);

        $items = $componentItems
            ->concat($laborGroupItem)
            ->sortBy('sort_key')
            ->values()
            ->all();

        return response()->json([
            'context' => $ctx,
            'id' => (int) $masterSet->id,
            'name' => (string) $masterSet->name,
            'description' => (string) ($masterSet->description ?? ''),
            'status' => (string) ($masterSet->status ?? 'Published'),

            'creator_id' => $masterSet->creator_id ? (int) $masterSet->creator_id : null,
            'creator_name' => $masterSet->creator
                ? trim(($masterSet->creator->name ?? '') . ' ' . ($masterSet->creator->lastname ?? ''))
                : null,

            'count_copy' => (int) ($masterSet->count_copy ?? 0),
            'count_offer' => (int) ($masterSet->count_offer ?? 0),
            'is_locked' => (int) ($masterSet->is_locked ?? 1),

            'article_group' => (string) ($masterSet->articleGroup?->name ?? $masterSet->articleGroup?->article_group ?? ''),
            'items' => $items,
        ]);
    }
}
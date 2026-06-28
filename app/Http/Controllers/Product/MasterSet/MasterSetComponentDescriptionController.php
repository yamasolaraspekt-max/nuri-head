<?php

namespace App\Http\Controllers\Product\MasterSet;

use App\Http\Controllers\Controller;
use App\Models\MasterSetComponent;
use App\Models\MasterSetComponentDescription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterSetComponentDescriptionController extends Controller
{
  public function index(Request $request, MasterSetComponent $component)
  {
    // Eager load product for fallback
    $component->load(['product', 'descriptionVariants']);

    $context = (string)($request->get('context') ?: 'angebot');

    $variants = $component->descriptionVariants()
      ->where('context', $context)
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();

    // Fallback sources if nothing saved:
    // 1) variant table (if exists)
    // 2) component.description (master_set_components.description)
    // 3) product.short_description
    $fallback = [
      'component_description' => $component->description,
      'product_short_description' => $component->product?->short_description,
    ];

    return response()->json([
      'status' => 'ok',
      'data' => [
        'component' => [
          'id' => $component->id,
          'master_set_id' => $component->master_set_id,
          'product_id' => $component->product_id,
          'product_name' => $component->product?->product,
          'is_sub' => !is_null($component->parent_id),
          'parent_id' => $component->parent_id,
        ],
        'context' => $context,
        'variants' => $variants,
        'fallback' => $fallback,
      ],
    ]);
  }

 public function store(Request $request, MasterSetComponent $component)
{
    $rawData = $request->all();
    
    // Manual decoding if delta is sent as a string
    if (isset($rawData['delta']) && is_string($rawData['delta'])) {
        $rawData['delta'] = json_decode($rawData['delta'], true);
    }

    $request->merge($rawData); // Put it back into the request

    $data = $request->validate([
      'context' => ['required','string','max:50'],
      'title'   => ['nullable','string','max:140'],
      'delta'   => ['nullable','array'], // Now this will pass
      'html'    => ['nullable','string'],
      'text'    => ['nullable','string'],
    ]);

    $data['master_set_component_id'] = $component->id;
    $data['sort_order'] = $data['sort_order'] ?? ($component->descriptionVariants()
      ->where('context', $data['context'])
      ->max('sort_order') + 1);

    $row = MasterSetComponentDescription::create($data);

    return response()->json(['status' => 'ok', 'data' => $row]);
  }

  public function update(Request $request, MasterSetComponentDescription $desc)
  {
    $data = $request->validate([
      'context' => ['sometimes','string','max:50'],
      'title'   => ['nullable','string','max:140'],
      'sort_order' => ['nullable','integer','min:0'],
      'delta'   => ['nullable','array'],
      'html'    => ['nullable','string'],
      'text'    => ['nullable','string'],
    ]);

    $desc->update($data);

    return response()->json(['status' => 'ok', 'data' => $desc->fresh()]);
  }

  public function destroy(MasterSetComponentDescription $desc)
  {
    $desc->delete();
    return response()->json(['status' => 'ok']);
  }

  public function reorder(Request $request, MasterSetComponent $component)
  {
    $data = $request->validate([
      'context' => ['required','string','max:50'],
      'ordered_ids' => ['required','array','min:1'],
      'ordered_ids.*' => [
        'integer',
        Rule::exists('master_set_component_descriptions', 'id'),
      ],
    ]);

    $ids = array_values(array_map('intval', $data['ordered_ids']));

    // Ensure all belong to this component + context
    $count = MasterSetComponentDescription::query()
      ->where('master_set_component_id', $component->id)
      ->where('context', $data['context'])
      ->whereIn('id', $ids)
      ->count();

    if ($count !== count($ids)) {
      return response()->json(['status' => 'error', 'message' => 'Invalid reorder payload'], 422);
    }

    foreach ($ids as $i => $id) {
      MasterSetComponentDescription::where('id', $id)->update(['sort_order' => $i]);
    }

    return response()->json(['status' => 'ok']);
  }
}

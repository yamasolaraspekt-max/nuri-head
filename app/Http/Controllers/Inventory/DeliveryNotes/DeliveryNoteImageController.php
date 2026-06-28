<?php

namespace App\Http\Controllers\Inventory\DeliveryNotes;

use App\Http\Controllers\Controller;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DeliveryNoteImageController extends Controller
{
    public function index(Request $request, DeliveryNote $deliveryNote)
    {
        $search = trim((string) $request->query('search', ''));
        $sort   = trim((string) $request->query('sort', 'latest'));

        $query = DeliveryNoteImage::query()
            ->where('delivery_note_id', $deliveryNote->id);

        if ($search !== '') {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $images = $query->paginate(20)->appends($request->query());

        return view('admin.product.delivery.image', [
            'deliveryNote' => $deliveryNote->load(['branch', 'handoverEmployee']),
            'images'       => $images,
        ]);
    }

    public function store(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate([
            'product'           => 'required|array|min:1',
            'product.*.title'   => 'nullable|string|max:255',
            'product.*.image'   => 'required|image|max:5120',
        ]);

        try {
            foreach ($request->product as $value) {
                $image = new DeliveryNoteImage();
                $image->delivery_note_id = $deliveryNote->id;
                $image->name = $value['title'] ?? null;

                if (!empty($value['image'])) {
                    $file = $value['image'];
                    $filename = uniqid('delivery_note_img_') . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/delivery_note'), $filename);
                    $image->image = $filename;
                }

                $image->save();
            }

            return redirect()
                ->back()
                ->with('save_msg', 'Bilder wurden erfolgreich gespeichert.');
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->with('delete_msg', 'Fehler beim Hochladen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, DeliveryNoteImage $image)
    {
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        try {
            $image->name = $request->name;

            if ($request->hasFile('image')) {
                $this->deletePhoto($image->image);

                $imageName = uniqid('delivery_note_img_') . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images/delivery_note'), $imageName);
                $image->image = $imageName;
            }

            $image->save();

            return redirect()
                ->back()
                ->with('save_msg', 'Bild wurde erfolgreich aktualisiert.');
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->with('delete_msg', 'Aktualisierung fehlgeschlagen: ' . $e->getMessage());
        }
    }

    public function destroy(DeliveryNoteImage $image)
    {
        try {
            $this->deletePhoto($image->image);
            $image->delete();

            return redirect()
                ->back()
                ->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht.');
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->with('delete_msg', 'Löschen fehlgeschlagen: ' . $e->getMessage());
        }
    }

    protected function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $photoPath = public_path('images/delivery_note/' . $photo);

        if (file_exists($photoPath)) {
            @unlink($photoPath);
        }
    }
}
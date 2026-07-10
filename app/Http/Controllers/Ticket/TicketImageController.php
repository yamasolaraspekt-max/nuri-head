<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketImageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,delete')->only(['destroy']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'exists:problems,id'],
            'stage' => ['nullable', 'string', 'max:120'],
            'file' => ['required', 'file', 'mimes:jpeg,jpg,png,bmp,gif,svg,webp,pdf,doc,docx,xls,xlsx', 'max:12288'],
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/tickets', 'public');

        $image = TicketImage::create([
            'ticket_id' => $request->ticket_id,
            'stage' => $request->stage ?: 'ticket',
            'image' => $path,
            'image_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datei wurde hochgeladen.',
            'file' => $this->formatFile($image),
            'image' => $this->formatFile($image),
        ]);
    }

    public function list($ticket_id)
    {
        $files = TicketImage::where('ticket_id', $ticket_id)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->latest()
            ->get()
            ->map(fn($file) => $this->formatFile($file))
            ->values();

        return response()->json($files);
    }

    public function destroy($id)
    {
        $image = TicketImage::findOrFail($id);

        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Datei wurde gelöscht.',
        ]);
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $image = TicketImage::findOrFail($id);
        $image->image_name = $request->name;
        $image->save();

        return response()->json([
            'success' => true,
            'file' => $this->formatFile($image),
        ]);
    }

    private function formatFile(TicketImage $file): array
    {
        $mime = (string) ($file->file_type ?? '');
        $name = (string) ($file->image_name ?? '');
        $url = $file->image ? asset('storage/' . ltrim($file->image, '/')) : null;

        return [
            'id' => $file->id,
            'ticket_id' => $file->ticket_id,
            'stage' => $file->stage,
            'image' => $file->image,
            'image_name' => $name,
            'file_type' => $mime,
            'status' => $file->status,
            'url' => $url,
            'file_url' => $url,
            'is_image' => str_starts_with($mime, 'image/'),
            'is_pdf' => str_contains($mime, 'pdf') || str_ends_with(strtolower($name), '.pdf'),
            'created_at_human' => optional($file->created_at)->diffForHumans(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PersonalNote;
use App\Models\NoteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AdminPersonalNoteController extends Controller
{
    // Helper to get Employee ID from auth()->user()->name
    private function getEmployeeId() {
        return (int) auth()->user()->name;
    }

   public function fetchNotes(Request $request)
    {
        $empId = $this->getEmployeeId();

        // 1. Ensure Default Category Exists
        $categoryCount = NoteCategory::where('user', $empId)->count();
        if ($categoryCount === 0) {
            NoteCategory::create([
                'category_name' => 'Allgemein',
                'type' => 'general',
                'color' => '#74b2d4',
                'icon' => 'ri-sticky-note-line',
                'user' => $empId
            ]);
        }

        // 2. Fetch Notes
        $query = PersonalNote::where('user_id', $empId)
            ->with('category')
            ->where('is_done', false) // <--- THIS LINE HIDES COMPLETED NOTES
            ->orderBy('order_by', 'ASC')
            ->orderBy('created_at', 'DESC');

        // Search Filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        // Category Filter
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }

        $notes = $query->get();
        $categories = NoteCategory::where('user', $empId)->get();

        return response()->json([
            'success' => true,
            'notes' => $notes,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:note_categories,id',
        ]);

        $empId = $this->getEmployeeId();

        // Calculate next order position
        $maxOrder = PersonalNote::where('user_id', $empId)->max('order_by');

        $note = PersonalNote::create([
            'title' => $request->title,
            'note' => $request->note,
            'category_id' => $request->category_id,
            'user_id' => $empId,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'order_by' => $maxOrder + 1,
            'is_done' => false
        ]);

        return response()->json(['success' => true, 'message' => 'Notiz erstellt', 'note' => $note]);
    }

    public function update(Request $request)
    {
        $note = PersonalNote::find($request->id);
        
        if (!$note || $note->user_id != $this->getEmployeeId()) {
            return response()->json(['success' => false, 'message' => 'Not found']);
        }

        if ($request->has('is_done')) {
            $note->is_done = $request->is_done;
            $note->done_date = $request->is_done ? Carbon::now() : null;
        }

        $note->fill($request->except(['id', 'user_id']));
        $note->save();

        return response()->json(['success' => true, 'message' => 'Aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $note = PersonalNote::find($request->id);
        if ($note && $note->user_id == $this->getEmployeeId()) {
            $note->delete();
            return response()->json(['success' => true, 'message' => 'Gelöscht']);
        }
        return response()->json(['success' => false, 'message' => 'Fehler']);
    }

    public function reorder(Request $request)
    {
        $order = $request->order; // Array of IDs in new order
        if(is_array($order)) {
            foreach($order as $index => $id) {
                PersonalNote::where('id', $id)
                    ->where('user_id', $this->getEmployeeId())
                    ->update(['order_by' => $index]);
            }
        }
        return response()->json(['success' => true]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['category_name' => 'required|string|max:50']);
        
        $cat = NoteCategory::create([
            'category_name' => $request->category_name,
            'user' => $this->getEmployeeId(),
            'color' => $request->color ?? '#8fc73e',
            'type' => 'custom'
        ]);

        return response()->json(['success' => true, 'category' => $cat]);
    }
    
    public function getCategories() {
        $categories = NoteCategory::where('user', $this->getEmployeeId())->get();
        return response()->json(['success' => true, 'categories' => $categories]);
    }


     public function empStore(Request $request, int $employeeId)
    {
        // If you want to ensure the URL employeeId matches the logged-in employee:
        // abort_unless(Auth::id() === $employeeId, 403);

        $validated = $request->validate([
            'title'       => ['nullable', 'string', 'max:255'],
            'note'        => ['required', 'string', 'max:2000'], // adjust max as you want
            'category_id' => ['nullable', 'integer', 'exists:note_categories,id'],
        ]);

        $note = new PersonalNote();
        $note->title       = filled($validated['title'] ?? null) ? $validated['title'] : 'Neue Notiz';
        $note->note        = $validated['note'];
        $note->category_id = $validated['category_id'] ?? null; // allow null if you make DB column nullable
        $note->user_id     = $employeeId;

        // defaults from migration will apply automatically:
        // is_done=false, add_calendar=false, etc.

        $note->save();

        // If your form is embedded in a page: go back with flash message
        return back()->with('success', 'Notiz wurde erstellt.');
    }

    public function toggleDone(Request $request, PersonalNote $note): JsonResponse
    {
        // If you use a different guard, replace auth()->id()
        $employeeId = auth()->user()->name;

        // Ensure only the owner can toggle
        abort_unless((int) $note->user_id === (int) $employeeId, 403);

        $now = Carbon::now();

        $note->is_done = ! (bool) $note->is_done;
        $note->done_date = $note->is_done ? $now : null;

        $note->save();

        return response()->json([
            'ok'        => true,
            'id'        => $note->id,
            'is_done'   => (bool) $note->is_done,
            'done_date' => $note->done_date ? $note->done_date->toDateTimeString() : null,
        ]);
    }

    public function delete(Request $request, PersonalNote $note): JsonResponse
    {
        $employeeId = auth()->user()->name;

        // Ensure only the owner can delete
        abort_unless((int) $note->user_id === (int) $employeeId, 403);

        $note->delete(); // softDeletes enabled in migration

        return response()->json([
            'ok' => true,
            'id' => $note->id,
        ]);
    }
}
<?php

namespace App\Http\Controllers\Employee\Note;
use App\Http\Controllers\Controller;

use App\Models\PersonalNote;
use App\Models\NoteCategory;
use Illuminate\Http\Request;
use DB;
use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Notifications\PersonalTaskNotification;
use App\Notifications\LeadResponsibleChange;
use App\Models\EmployeesPersonalTask;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class PersonalNoteController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function index(Request $request)
        {
            $status   = $request->get('status', 'open');   // 'open' | 'done' | 'all'
            $userName = Auth::user()->employeeId();               // you used name in filter/search

            $query = DB::table('personal_notes')
                ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
                ->select('personal_notes.*', 'note_categories.category_name')
                ->where('personal_notes.user_id', $userName)
                ->whereNull('personal_notes.deleted_at');

            // status filter (same logic everywhere)
            if ($status === 'open') {
                $query->where(function ($q) {
                    $q->whereNull('personal_notes.is_done')
                    ->orWhere('personal_notes.is_done', 0);
                });
            } elseif ($status === 'done') {
                $query->where('personal_notes.is_done', 1);
            } // 'all' => no extra condition

            $notes = $query
                ->orderBy('personal_notes.order_by', 'asc')
                ->orderBy('personal_notes.created_at', 'desc')
                ->get();

            return response()->json([
                'notes' => $notes,
            ]);
        }

    public function list_index()
    {
        $notes = DB::table('personal_notes')
            ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
            ->select('personal_notes.*', 'note_categories.category_name')
            ->where('personal_notes.user_id', auth()->user()->employeeId()) // Use the user's ID
            ->whereNull('personal_notes.deleted_at') 
            ->orderBy('personal_notes.order_by', 'asc') // Order by "order_by" first
            ->orderBy('personal_notes.created_at', 'desc') // Then by "created_at"
            ->get();
        

        return response()->json([
            'notes' => $notes,
        ]);
    }


   public function trash()
    {
        $notes = DB::table('personal_notes')
            ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
            ->select('personal_notes.*', 'note_categories.category_name')
            ->where('personal_notes.user_id', auth()->user()->employeeId())
            ->whereNotNull('personal_notes.deleted_at') // Only fetch trashed notes
            ->orderBy('personal_notes.order_by', 'asc')
            ->orderBy('personal_notes.created_at', 'desc')
            ->get();

        return response()->json([
            'notes' => $notes,
        ]);
    }


    public function recover($id)
        {
            $note = PersonalNote::withTrashed()->find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Notiz nicht gefunden.'], 404);
            }

            $note->restore();
            $note->update(['order_by' => 1]);

            return response()->json(['message' => 'Die Notiz wurde erfolgreich wiederhergestellt.']);
        }


        public function permanentDelete($id)
        {
                $note = PersonalNote::withTrashed()->find($id);
                $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

                if (!$note) {
                    return response()->json(['message' => 'Notiz nicht gefunden.'], 404);
                }

                $note->forceDelete();

                return response()->json(['message' => 'Die Notiz wurde dauerhaft gelöscht.']);
            }

    public function filter(Request $request)
    {
        $request->validate([
            'filter' => 'required|string|in:date,sort,calendar,reminder,repeat',
            'status' => 'nullable|string|in:open,done,all',
        ]);

        $user   = Auth::user();
        $filter = $request->filter;
        $status = $request->input('status', 'open');

        $query = DB::table('personal_notes')
            ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
            ->select('personal_notes.*', 'note_categories.category_name')
            ->where('personal_notes.user_id', $user->name)
            ->whereNull('personal_notes.deleted_at');

        // same status logic as index()
        if ($status === 'open') {
            $query->where(function ($q) {
                $q->whereNull('personal_notes.is_done')
                ->orWhere('personal_notes.is_done', 0);
            });
        } elseif ($status === 'done') {
            $query->where('personal_notes.is_done', 1);
        }

        // functional filter
        switch ($filter) {
            case 'date':
                $query->orderBy('personal_notes.created_at', 'desc');
                break;

            case 'sort':
                $query->orderBy('personal_notes.order_by', 'asc');
                break;

            case 'calendar':
                $query->whereNotNull('personal_notes.add_calendar');
                break;

            case 'reminder':
                $query->whereNotNull('personal_notes.reminder_date');
                break;

            case 'repeat':
                $query->whereNotNull('personal_notes.repeat');
                break;
        }

        $notes = $query->get();

        return response()->json([
            'notes'   => $notes,
            'message' => 'Filter erfolgreich angewendet.',
        ]);
    }


   public function store(Request $request)
    {
        // Validate the required fields
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'category_id' => 'nullable|exists:note_categories,id',
            'priority' => 'nullable|string|in:normal,medium,high',
            'reminder_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'priority.in' => 'Die Priorität muss entweder low, medium oder high sein.',
            'reminder_time.date_format' => 'Die Erinnerungszeit muss im Format HH:mm sein.',
            'color.regex' => 'Die Farbe muss ein gültiger Hexadezimalwert sein.',
        ]);

        // Check if add_calendar is enabled
        if ($request->add_calendar) {
            // Ensure both deadline and add_calendar_date are provided
            if (!$request->deadline || !$request->add_calendar_date) {
                return response()->json([
                    'message' => 'Bitte geben Sie sowohl das Fälligkeitsdatum als auch das Kalenderdatum an.',
                ], 422);
            }

            // Check availability only if check_availability is false
            if ($request->check_availability === 'false') {
                $calendar_date = $request->deadline;
                $end_date = $request->add_calendar_date;
                $conflicts = $this->checkAvailability($calendar_date, $end_date);

                if ($conflicts && $conflicts !== 'false') {
                    // Return conflicts with a 409 status code
                    return response()->json([
                        'message' => 'Es gibt bestehende Aufgaben im angegebenen Zeitraum.',
                        'availability' => $conflicts, // Send conflict data back
                    ], 409);
                }
            }
        }

        // Set default category if none provided
        $category_id = $validatedData['category_id'] ?? NoteCategory::firstOrCreate(
            ['category_name' => 'Standard'],
            [
                'user' => auth()->user()->employeeId(),
                'color' => '#8fc73e',
                'type' => 'Normal',
            ]
        )->id;

        // If add_calendar is enabled, create a task
        if ($request->add_calendar) {
            $appointment = now();
            $by = auth()->user()->employeeId();
            $this->make_task(
                $request->title,
                $by,
                $request->note,
                $appointment,
                $request->priority,
                $request->color,
                $request->end_time
            );
        }

        // Create the note
        $note = PersonalNote::create([
            'title' => $validatedData['title'],
            'note' => $validatedData['note'],
            'category_id' => $category_id,
            'user_id' => auth()->user()->employeeId(),
            'is_done' => $request->is_done ?? false,
            'deadline' => $request->deadline,
            'end_time' => $request->end_time,
            'repeat' => $request->repeat,
            'order_by' => $request->order_by,
            'priority' => $validatedData['priority'] ?? 'medium',
            'reminder_date' => $validatedData['reminder_date'] ?? null,
            'reminder_time' => $validatedData['reminder_time'] ?? null,
            'color' => $validatedData['color'] ?? '#8fc73e',
            'add_calendar' => $request->add_calendar ? 1 : 0,
            'add_calendar_date' => $request->add_calendar_date,
        ]);

        \Log::info('Created Note:', $note->toArray());

        return response()->json(['note' => $note, 'message' => 'Notiz erfolgreich erstellt.']);
    }
 

        private function checkAvailability($start_date, $end_date){

            $user= auth()->user()->employeeId();
            $check = DB::table('personal_tasks')
                        ->join('employees_personal_tasks as emp_task', 'emp_task.task_id', '=', 'personal_tasks.id')
                        ->leftJoin('employees', 'employees.id', '=', 'emp_task.employee_id')
                        ->select('personal_tasks.task_title', 'personal_tasks.start_date', 'personal_tasks.end_date', 'employees.name', 'employees.lastname')
                        ->where('emp_task.employee_id', '=', $user)
                        ->where('personal_tasks.start_date' ,'>=', $start_date)
                        ->where('personal_tasks.start_date' ,'<=', $end_date)
                        ->get();

            if($check){
                return $check;
            }
            else {
                return 'false';
            }
        }



        private function make_task($title, $by, $note, $appointment, $priority, $color, $end_time)
        {
            $start_time = '09:00:00';
            $end_time_default = $end_time ?? '10:00:00';

            $task = PersonalTask::create([
                'task_id' => mt_rand(1000000, 9999999),
                'task_title' => $title,
                'description' => 'Persönliche Aufgabe wurde aus persönlicher Notiz übertragen',
                'priority' => $priority ?? 'medium',
                'color' => $color ?? '#8fc73e',
                'assigned_by' => $by,
                'progress' => 40, // Default progress
                'task_status' => 'on_going',
                'start_date' => now(),
                'end_date' => $appointment,
                'start_time' => $start_time,
                'end_time' => $end_time_default,
                'total_time' => round(\Carbon\Carbon::parse($start_time)->diffInMinutes(\Carbon\Carbon::parse($end_time_default)) / 60, 2),
            ]);

            EmployeesPersonalTask::create([
                'employee_id' => $by,
                'task_id' => $task->id,
                'status' => 'accept',
            ]);

            $tasks = [
                $note,
                'Melde dich, wenn es fertig ist',
            ];

            foreach ($tasks as $taskDescription) {
                PersonalTaskKey::create([
                    'personal_task_id' => $task->id,
                    'task' => $taskDescription,
                    'done_by' => null,
                ]);
            }

            $emp = DB::table('employees')
                ->select('name', 'lastname')
                ->where('id', auth()->user()->employeeId())
                ->first();

            if ($emp) {
                $emp_name = $emp->name . ' ' . $emp->lastname;

                Notification::send(auth()->user(), new PersonalTaskNotification([
                    'title' => 'Aufgabe erstellt',
                    'message' => 'Eine neue Aufgabe wurde von ' . $emp_name . ' erstellt',
                    'task_id' => $task->id,
                ]));
            }
        }


        public function done(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            // Set the done_date based on is_done
            $done_date = $request->is_done ? now() : null;

            $note->update([
                'is_done' => $request->is_done ? 1 : 0,
                'done_date' => $done_date,
            ]);

            return response()->json($note);
        }
 
        public function no_repeat($id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'repeat' => null,
            ]);

            return response()->json($note);
        }
 
        public function no_reminder($id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'reminder_date' => null,
                'reminder_time' => null,
            ]);

            return response()->json($note);
        }
        public function name(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'title' => $request->title,
            ]);

            return response()->json($note);
        }

        public function note(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'note' => $request->note,
            ]);

            return response()->json($note);
        }

        public function changeColor(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'color' => $request->color,
            ]);

            return response()->json($note);
        }

        public function changeDate(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'deadline' => $request->deadline,
            ]);

            return response()->json($note);
        }


        public function changeTime(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id);

            $validatedData = $request->validate([
                'end_time' => 'required|date_format:H:i',
            ], [
                'end_time.required' => 'Bitte wählen Sie eine gültige Uhrzeit aus.',
                'end_time.date_format' => 'Die Uhrzeit muss im Format HH:mm sein.',
            ]);

            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Notiz nicht gefunden.'], 404);
            }

            $note->update([
                'end_time' => $validatedData['end_time'],
            ]);

            return response()->json(['message' => 'Die Uhrzeit wurde erfolgreich aktualisiert.', 'note' => $note]);
        }


        public function updateSettings(Request $request, $id)
        {
            \Log::info('Updating note with ID: ' . $id); 
            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Notiz nicht gefunden.'], 404);
            }

            if ($request->add_calendar_date) {
                // Ensure both deadline and add_calendar_date are provided
                if (!$request->deadline || !$request->add_calendar_date) {
                    return response()->json([
                        'message' => 'Bitte geben Sie sowohl das Fälligkeitsdatum als auch das Kalenderdatum an.',
                    ], 422);
                }

                // Check availability only if check_availability is false
                if ($request->check_emp === 'false') {
                    $calendar_date = $request->deadline;
                    $end_date = $request->add_calendar_date;
                    $conflicts = $this->checkAvailability($calendar_date, $end_date);

                    if ($conflicts && $conflicts !== 'false') {
                        // Return conflicts with a 409 status code
                        return response()->json([
                            'message' => 'Es gibt bestehende Aufgaben im angegebenen Zeitraum.',
                            'availability' => $conflicts, // Send conflict data back
                        ], 409);
                    }
                }
            }


            // If add_calendar is enabled, create a task
                if ($request->add_calendar) {
                    $appointment = now();
                    $by = auth()->user()->employeeId();
                    $this->make_task(
                        $note->title,
                        $by,
                        $note->note,
                        $appointment,
                        $request->priority,
                        $note->color,
                        $note->end_time ?? ''
                    );
                }

            $note->update([
                'add_calendar_date' => $request->add_calendar_date,
                'repeat' => $request->repeat,
                'priority' => $request->priority,
                'reminder_date' => $request->reminder_date,
                'reminder_time' =>  $request->reminder_time,
                'title' => $request->task_title,
                'note'  =>  $request->note,
                'category_id'   => $request->category_id
            ]);

            return response()->json(['message' => 'Die Uhrzeit wurde erfolgreich aktualisiert.', 'note' => $note]);
        }
 
        public function show($id)
        {
            $note = PersonalNote::find($id);
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Notiz nicht gefunden.'], 404);
            }

            return response()->json(['note' => $note]);
        }


        public function destroy($id)
        {
            $note = PersonalNote::find($id); // Use find() instead of findOrFail()
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }

            $note->delete();

            return response()->json(['success' => true]);
        }

        public function getCategory()
        {
            $data = DB::table('note_categories')
                ->where('user', auth()->user()->employeeId())
                ->whereNull('deleted_at') // optional, if you want only active categories
                ->get();

            return response()->json($data);
        }


        public function updateCategory($id,$category_id){
        
            $note = PersonalNote::find($id); // Use find() instead of findOrFail()
            $this->authorizeNoteOwner($note); // P1-IDOR Buendel-1: Owner-Gate

            if (!$note) {
                    return response()->json(['message' => 'Note not found'], 404);
            }

            $note->update([ 
                'category_id' => $category_id,
            ]);

            return response()->json($note);

        }

        public function updateOrder(Request $request)
        {
            $order = $request->input('order'); // Get the new order array of IDs

            if (is_array($order)) {
                foreach ($order as $index => $id) {
                    // Update each note's order_by based on its position in the array
                    PersonalNote::where('id', $id)->update(['order_by' => $index + 1]);
                }

                return response()->json(['message' => 'Die Notenposition wurde erfolgreich geändert'], 200);
            }

            return response()->json(['message' => 'Invalid data submitted.'], 400);
        }


        public function details()
        {
            $data['data'] = DB::table('personal_notes')
                ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
                ->select('personal_notes.*', 'note_categories.category_name')
                ->where('personal_notes.user_id', auth()->user()->employeeId()) // Use the user's ID
                ->whereNull('personal_notes.deleted_at')
                ->orderBy('personal_notes.order_by', 'asc') // Order by "order_by" first
                ->orderBy('personal_notes.created_at', 'desc') // Then by "created_at"
                ->get();

            $data['category'] = DB::table('note_categories')
                                    ->where('user', auth()->user()->employeeId())
                                    ->whereNull('deleted_at')
                                    ->get(); 
            return view('admin.notes.notes.note', $data);
        }

        public function search(Request $request)
        {
            $searchQuery = $request->input('search');
            $status      = $request->input('status', 'open'); // open | done | all
            $userName    = auth()->user()->employeeId();

            // optional: interpret words as done/open
            $isDoneFilter = null;
            if (in_array(strtolower($searchQuery), ['complete', 'done', 'abgeschlossen'])) {
                $isDoneFilter = 1;
            } elseif (in_array(strtolower($searchQuery), ['not complete', 'offen'])) {
                $isDoneFilter = 0;
            }

            $notes = DB::table('personal_notes')
                ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
                ->where('personal_notes.user_id', $userName)
                ->whereNull('personal_notes.deleted_at')

                // status from tab
                ->when($status === 'open', function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNull('personal_notes.is_done')
                            ->orWhere('personal_notes.is_done', 0);
                    });
                })
                ->when($status === 'done', function ($q) {
                    $q->where('personal_notes.is_done', 1);
                })

                // additional is_done keywords from search text
                ->when($isDoneFilter !== null, function ($q) use ($isDoneFilter) {
                    $q->where('personal_notes.is_done', $isDoneFilter);
                })

                // normal text search when no special keyword
                ->when($isDoneFilter === null, function ($q) use ($searchQuery) {
                    $q->where(function ($subQuery) use ($searchQuery) {
                        $subQuery->where('personal_notes.title', 'LIKE', "%{$searchQuery}%")
                            ->orWhere('personal_notes.note', 'LIKE', "%{$searchQuery}%")
                            ->orWhere('note_categories.category_name', 'LIKE', "%{$searchQuery}%");
                    });
                })
                ->select('personal_notes.*', 'note_categories.category_name')
                ->orderBy('personal_notes.order_by', 'asc')
                ->orderBy('personal_notes.created_at', 'desc')
                ->get();

            return response()->json([
                'notes'   => $notes,
                'message' => 'Search results fetched successfully.',
            ]);
        }

 
        public function category_search(Request $request)
        {
            $searchQuery = $request->input('category_id');

            // Fetch notes based on the search query, excluding deleted ones
            $notes = DB::table('personal_notes')
                ->join('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
                ->where('personal_notes.user_id', auth()->user()->employeeId()) // Use user ID instead of name for security
                ->whereNull('personal_notes.deleted_at') // Exclude deleted notes
                ->where('note_categories.id', '=', $searchQuery)
                ->select('personal_notes.*', 'note_categories.category_name')
                ->get();

            return response()->json([
                'notes' => $notes,
                'message' => 'Search results fetched successfully.',
            ]);
        }

      public function processRepeatingNotes()
        {
            // Fetch notes with a valid `repeat` value
            $notes = DB::table('personal_notes')
                ->whereNotNull('repeat')
                ->whereIn('repeat', ['minute', 'hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
                ->whereNull('deleted_at') 
                ->where('user_id', auth()->user()->employeeId()) 
                ->get();

            $newNotes = [];
            $repeatedNoteDetails = [];

            foreach ($notes as $note) {
                // Determine the next duplication time based on the `repeat` value
                $nextTime = match ($note->repeat) {
                    'minute' => now()->addMinute(),
                    'hourly' => now()->addHour(),
                    'daily' => now()->addDay(),
                    'weekly' => now()->addWeek(),
                    'monthly' => now()->addMonth(),
                    'quarterly' => now()->addMonths(3),
                    'yearly' => now()->addYear(),
                    default => null
                };

                if ($nextTime) {
                    $newNotes[] = [
                        'user_id' => $note->user_id,
                        'title' => $note->title,
                        'note' => $note->note,
                        'category_id' => $note->category_id,
                        'color' => $note->color,
                        'deadline' => $nextTime,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $repeatedNoteDetails[] = [
                        'id' => $note->id,
                        'title' => $note->title,
                        'next_deadline' => $nextTime->format('Y-m-d H:i:s')
                    ];
                }
            }

            // Insert duplicated notes if any
            if (!empty($newNotes)) {
                DB::table('personal_notes')->insert($newNotes);
            }

            return response()->json([
                'duplicated_notes' => count($newNotes),
                'repeated_note_details' => $repeatedNoteDetails
            ]);
        }


        public function stopRepeatingForAll(Request $request)
        {
            $noteIds = $request->input('note_ids'); // Get note IDs from the request

            if (empty($noteIds)) {
                return response()->json(['message' => 'No notes selected.'], 400);
            }

            DB::table('personal_notes')
                ->whereIn('id', $noteIds)
                ->where('user_id', auth()->user()->employeeId())
                ->update(['repeat' => null]);

            return response()->json(['message' => 'Repeats stopped for selected notes.']);
        }



    public function noteStore(Request $request, $employeeId)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'note'        => 'nullable|string',
            'category_id' => 'nullable|exists:note_categories,id',
            'color'       => 'nullable|string|max:20',
            'deadline'    => 'nullable|date',
        ]);

        $now = Carbon::now();

        // ------------------------------------------------------
        // 1. Ensure category exists (if none selected)
        // ------------------------------------------------------
        if (empty($data['category_id'])) {

            // Check if a default category already exists for this employee
            $defaultCategory = DB::table('note_categories')
                ->where('user', $employeeId)
                ->where('category_name', 'Default')
                ->first();

            // If not exists → create one
            if (!$defaultCategory) {
                $categoryId = DB::table('note_categories')->insertGetId([
                    'category_name' => 'Default',
                    'type'          => 'general',
                    'color'         => '#fef9c3',
                    'icon'          => 'note',
                    'status'        => 'published',
                    'user'          => $employeeId,    // belongs to employee
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            } else {
                $categoryId = $defaultCategory->id;
            }

        } else {
            $categoryId = $data['category_id'];
        }

        // ------------------------------------------------------
        // 2. Insert the note
        // ------------------------------------------------------
        DB::table('personal_notes')->insert([
            'title'            => $data['title'] ?: 'Neue Notiz',
            'note'             => $data['note'] ?? null,
            'is_done'          => false,
            'done_date'        => null,
            'add_calendar'     => false,
            'add_calendar_date'=> null,
            'order_by'         => null,
            'deadline'         => $data['deadline'] ?? null,
            'end_time'         => null,
            'repeat'           => null,
            'reminder_date'    => null,
            'reminder_time'    => null,
            'priority'         => null,
            'color'            => $data['color'] ?? '#fef9c3',
            'is_notified'      => null,
            'user_id'          => $employeeId,
            'category_id'      => $categoryId,     // guaranteed safe
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        return back()->with('save_msg', 'Notiz wurde angelegt.');
    }

    public function toggleDone($noteId)
    {
        $note = DB::table('personal_notes')
            ->where('id', $noteId)
            ->first();

        if (!$note) {
            return back()->with('save_msg', 'Notiz nicht gefunden.');
        }

        $isDone   = !(bool) $note->is_done;
        $doneDate = $isDone ? Carbon::now() : null;

        DB::table('personal_notes')
            ->where('id', $noteId)
            ->update([
                'is_done'   => $isDone,
                'done_date' => $doneDate,
                'updated_at'=> Carbon::now(),
            ]);

        return back();
    }

    public function noteDestroy($noteId)
    {
        DB::table('personal_notes')
            ->where('id', $noteId)
            ->delete();

        return back()->with('save_msg', 'Notiz gelöscht.');
    }


    /** MASTER-01 P1-IDOR Buendel-1: nur der Eigentuemer (user_id=Employee-ID) darf seine Notiz aendern/loeschen. */
    private function authorizeNoteOwner(?PersonalNote $note): void
    {
        if ((bool) (auth()->user()->is_admin ?? false)) { return; }
        if ($note !== null && (string) $note->user_id !== (string) $this->getEmployeeId()) {
            abort(403, 'Keine Berechtigung fuer diese Notiz.');
        }
    }

    private function getEmployeeId() {
        return auth()->user()->employeeId(); 
    }

    /**
     * Fetch notes for the sidebar list (AJAX)
     */
    public function fetchNotes(Request $request)
    {
        $empId = $this->getEmployeeId();
        $search = $request->input('search');
        $filter = $request->input('filter'); // color or category ID

        $query = DB::table('personal_notes')
            ->leftJoin('note_categories', 'personal_notes.category_id', '=', 'note_categories.id')
            ->select('personal_notes.*', 'note_categories.category_name', 'note_categories.color as cat_color')
            ->where('personal_notes.user_id', $empId)
            ->where('personal_notes.is_done', false) // Only show unfinished notes
            ->whereNull('personal_notes.deleted_at')
            ->orderBy('personal_notes.order_by', 'asc')
            ->orderBy('personal_notes.created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('personal_notes.title', 'like', "%{$search}%")
                  ->orWhere('personal_notes.note', 'like', "%{$search}%");
            });
        }

        if ($filter && $filter !== 'all') {
            $query->where('personal_notes.color', $filter);
        }

        $notes = $query->get();

        return response()->json(['notes' => $notes]);
    }

    /**
     * Store a new note
     */
    public function saves(Request $request)
    {
        $empId = $this->getEmployeeId();

        // 1. Ensure a default category exists
        $category = DB::table('note_categories')
            ->where('user', $empId)
            ->where('category_name', 'Allgemein')
            ->first();

        if (!$category) {
            $catId = DB::table('note_categories')->insertGetId([
                'category_name' => 'Allgemein',
                'type' => 'default',
                'color' => '#74b2d4',
                'user' => $empId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $catId = $category->id;
        }

        // 2. Create Note
        $noteId = DB::table('personal_notes')->insertGetId([
            'title' => $request->title ?? 'Neue Notiz',
            'note' => $request->note,
            'user_id' => $empId,
            'category_id' => $request->category_id ?? $catId,
            'color' => $request->color ?? '#ffffff',
            'priority' => $request->priority ?? 'normal',
            'deadline' => $request->deadline,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Notiz erstellt', 'id' => $noteId]);
    }

    /**
     * Update note details (inline edit)
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'note' => 'nullable|string',
            'category_id' => 'nullable|exists:note_categories,id',
            'priority' => 'nullable|string|in:normal,medium,high',
            'deadline' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'repeat' => 'nullable|string|in:minute,hourly,daily,weekly,monthly,quarterly,yearly',
            'reminder_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'add_calendar_date' => 'nullable|date',
        ]);

        $note = PersonalNote::where('id', $id)
            ->where('user_id', auth()->user()->employeeId())
            ->whereNull('deleted_at')
            ->first();

        if (!$note) {
            return response()->json([
                'message' => 'Notiz nicht gefunden.'
            ], 404);
        }

        $categoryId = $validatedData['category_id'] ?? $note->category_id;

        if (!$categoryId) {
            $categoryId = NoteCategory::firstOrCreate(
                [
                    'category_name' => 'Standard',
                    'user' => auth()->user()->employeeId(),
                ],
                [
                    'color' => '#8fc73e',
                    'type' => 'Normal',
                ]
            )->id;
        }

        if ($request->add_calendar) {
            if (!$request->deadline || !$request->add_calendar_date) {
                return response()->json([
                    'message' => 'Bitte geben Sie sowohl das Fälligkeitsdatum als auch das Kalenderdatum an.',
                ], 422);
            }

            if ($request->check_availability === 'false') {
                $conflicts = $this->checkAvailability($request->deadline, $request->add_calendar_date);

                if ($conflicts && $conflicts !== 'false' && count($conflicts) > 0) {
                    return response()->json([
                        'message' => 'Es gibt bestehende Aufgaben im angegebenen Zeitraum.',
                        'availability' => $conflicts,
                    ], 409);
                }
            }
        }

        $note->update([
            'title' => $validatedData['title'],
            'note' => $validatedData['note'] ?? null,
            'category_id' => $categoryId,
            'deadline' => $validatedData['deadline'] ?? null,
            'end_time' => $validatedData['end_time'] ?? null,
            'repeat' => $validatedData['repeat'] ?? null,
            'priority' => $validatedData['priority'] ?? 'normal',
            'reminder_date' => $validatedData['reminder_date'] ?? null,
            'reminder_time' => $validatedData['reminder_time'] ?? null,
            'color' => $validatedData['color'] ?? '#fff59d',
            'add_calendar' => $request->add_calendar ? 1 : 0,
            'add_calendar_date' => $validatedData['add_calendar_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notiz wurde erfolgreich aktualisiert.',
            'note' => $note->fresh(),
        ]);
    }

    /**
     * Mark as Done
     */
    public function markDone($id)
    {
        DB::table('personal_notes')->where('id', $id)->update([
            'is_done' => true,
            'done_date' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Notiz erledigt!']);
    }

    /**
     * Soft Delete
     */
 
    /**
     * Reorder notes (Drag and Drop)
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order'); // Array of IDs
        
        foreach ($order as $index => $id) {
            DB::table('personal_notes')->where('id', $id)->update(['order_by' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get Categories for Dropdown
     */
    public function getCategories()
    {
        $empId = $this->getEmployeeId();
        $categories = DB::table('note_categories')
            ->where('user', $empId)
            ->whereNull('deleted_at')
            ->get();
            
        return response()->json($categories);
    }

}

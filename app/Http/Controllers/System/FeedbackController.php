<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackImage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function employeeId(): int
    {
        return (int) auth()->user()->name;
    }

    private function isProgrammer(): bool
    {
        return DB::table('user_rolls')
            ->where('user_id', auth()->user()->name)
            ->where('item_id', 'Programmer')
            ->where(function ($query) {
                $query->where('is_read', 'on')
                    ->orWhere('is_read', 1)
                    ->orWhere('is_update', 'on')
                    ->orWhere('is_update', 1);
            })
            ->exists();
    }

    private function baseFeedbackQuery()
    {
        $query = DB::table('feedback')
            ->leftJoin('employees', 'employees.id', '=', 'feedback.employee_id')
            ->select(
                'feedback.*',
                'employees.name',
                'employees.lastname',
                'employees.image as employee_image'
            );

        if (!$this->isProgrammer()) {
            $query->where('feedback.employee_id', $this->employeeId());
        }

        return $query;
    }

    public function index()
    {
        $isProgrammer = $this->isProgrammer();

        $data = $this->baseFeedbackQuery()
            ->orderByDesc('feedback.id')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = DB::table('feedback');

        if (!$isProgrammer) {
            $statsQuery->where('employee_id', $this->employeeId());
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'fixed' => (clone $statsQuery)->where('status', 'fixed')->count(),
            'progress' => (clone $statsQuery)->where('status', 'progress')->count(),
            'open' => (clone $statsQuery)->whereNull('status')->count(),
        ];

        $employees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.feedback.feedback', [
            'data' => $data,
            'employees' => $employees,
            'stats' => $stats,
            'isProgrammer' => $isProgrammer,
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', 'all'));

        $query = $this->baseFeedbackQuery();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $lowerSearch = mb_strtolower($search);

                $q->where('employees.name', 'LIKE', "%{$search}%")
                    ->orWhere('employees.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('feedback.title', 'LIKE', "%{$search}%")
                    ->orWhere('feedback.ticket_no', 'LIKE', "%{$search}%");

                if (in_array($lowerSearch, ['abgeschlossen', 'fixed', 'fertig'], true)) {
                    $q->orWhere('feedback.status', 'fixed');
                }

                if (in_array($lowerSearch, ['bearbeitung', 'progress', 'prozess'], true)) {
                    $q->orWhere('feedback.status', 'progress');
                }

                if (in_array($lowerSearch, ['reste', 'neu', 'open', 'offen'], true)) {
                    $q->orWhereNull('feedback.status');
                }
            });
        }

        if ($status === 'fixed') {
            $query->where('feedback.status', 'fixed');
        } elseif ($status === 'progress') {
            $query->where('feedback.status', 'progress');
        } elseif ($status === 'open') {
            $query->whereNull('feedback.status');
        }

        $items = $query
            ->orderByDesc('feedback.id')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = DB::table('feedback');

        if (!$this->isProgrammer()) {
            $statsQuery->where('employee_id', $this->employeeId());
        }

        return response()->json([
            'success' => true,
            'items' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
            'stats' => [
                'total' => (clone $statsQuery)->count(),
                'fixed' => (clone $statsQuery)->where('status', 'fixed')->count(),
                'progress' => (clone $statsQuery)->where('status', 'progress')->count(),
                'open' => (clone $statsQuery)->whereNull('status')->count(),
            ],
            'isProgrammer' => $this->isProgrammer(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'editor_text' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte prüfen Sie Ihre Eingaben.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $feedback = Feedback::create([
                'employee_id' => $this->employeeId(),
                'ticket_no' => $this->generateTicketNo(),
                'title' => $request->title,
                'description' => $request->editor_text,
                'status' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback wurde erfolgreich gespeichert.',
                'data' => $feedback,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        if (!$this->isProgrammer()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Berechtigung.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:feedback,id'],
            'response_text' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte geben Sie eine Antwort ein.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $feedback = Feedback::findOrFail($request->id);

            $feedback->update([
                'response' => $request->response_text,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Antwort wurde gespeichert.',
                'data' => $feedback,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Antwort konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus(Request $request, int $id): JsonResponse
    {
        if (!$this->isProgrammer()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Berechtigung.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:fixed,progress,open'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültiger Status.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $feedback = Feedback::findOrFail($id);

            if ($request->status === 'fixed') {
                $feedback->status = 'fixed';
                $feedback->fixed_date = now();
            } elseif ($request->status === 'progress') {
                $feedback->status = 'progress';
                $feedback->progress_date = now();
            } else {
                $feedback->status = null;
                $feedback->fixed_date = null;
                $feedback->progress_date = null;
            }

            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Status wurde aktualisiert.',
                'data' => $feedback,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status konnte nicht aktualisiert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feedback_id' => ['required', 'exists:feedback,id'],
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bild konnte nicht hochgeladen werden.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $feedback = Feedback::findOrFail($request->feedback_id);

            if (!$this->isProgrammer() && (int) $feedback->employee_id !== $this->employeeId()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Berechtigung.',
                ], 403);
            }

            $uploadPath = public_path('images/feedback');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('file');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $imageName);

            $feedbackImage = FeedbackImage::create([
                'feedback_id' => $feedback->id,
                'title' => $request->title ?: $imageName,
                'image' => asset('images/feedback/' . $imageName),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bild wurde hochgeladen.',
                'data' => $feedbackImage,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bild konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->isProgrammer()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Berechtigung.',
            ], 403);
        }

        try {
            $feedback = Feedback::findOrFail($id);

            FeedbackImage::where('feedback_id', $feedback->id)->get()->each(function ($image) {
                $this->deleteImageFile($image->image);
                $image->delete();
            });

            $feedback->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feedback wurde gelöscht.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback konnte nicht gelöscht werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateTicketNo(): int
    {
        do {
            $ticketNo = random_int(10000, 99999);
        } while (Feedback::where('ticket_no', $ticketNo)->exists());

        return $ticketNo;
    }

    private function deleteImageFile(?string $image): void
    {
        if (!$image) {
            return;
        }

        $filename = basename($image);
        $path = public_path('images/feedback/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
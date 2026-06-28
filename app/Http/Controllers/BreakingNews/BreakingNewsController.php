<?php

namespace App\Http\Controllers\BreakingNews;
use App\Http\Controllers\Controller;

use App\Models\BreakingNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BreakingNewsController extends Controller
{
    public function index()
    {
        $breakingNews = BreakingNews::with('creator')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.breaking-news.index', compact('breakingNews'));
    }

    /**
     * In your setup: auth()->user()->name holds the employee->id
     */
    protected function currentEmployeeId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        return is_numeric($user->name) ? (int) $user->name : null;
    }

        protected function validateData(Request $request): array
        {
            return $request->validate([
                'title'     => ['required', 'string', 'max:255'],
                'message'   => ['required', 'string'],
                'type'      => ['required', 'in:info,warning,danger,success'],
                'icon'      => ['nullable', 'string', 'max:50'],
                'starts_at' => ['nullable', 'date'],
                'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],
                'is_active' => ['nullable', 'boolean'],

                // NEW: audio validation – allow webm + the other types
                'audio'     => [
                    'nullable',
                    'file',
                    'mimes:webm,mp3,wav,ogg,m4a',
                    // optional size limit (20MB):
                    // 'max:20480',
                ],
            ]);
        }


    /**
     * Helper: build public URL for stored audio (storage/app/public/...)
     */
    protected function audioUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    protected function serialize(BreakingNews $news): array
    {
        return [
            'id'        => $news->id,
            'title'     => $news->title,
            'message'   => $news->message,
            'type'      => $news->type,
            'icon'      => $news->icon,
            'is_active' => (bool) $news->is_active,

            'starts_at'           => $news->starts_at?->format('Y-m-d H:i'),
            'ends_at'             => $news->ends_at?->format('Y-m-d H:i'),
            'starts_at_formatted' => $news->starts_at?->format('d.m.Y H:i'),
            'ends_at_formatted'   => $news->ends_at?->format('d.m.Y H:i'),

            'audio_url' => $news->audio_path
                ? asset('storage/'.$news->audio_path)
                : ($news->audio_url ?? null),

            'creator'   => $news->creator
                ? [
                    'id'   => $news->creator->id,
                    'name' => $news->creator->name,
                ]
                : null,
        ];
    }

   public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['is_active'] = $request->boolean('is_active');

        $employeeId = $this->currentEmployeeId();
        if ($employeeId === null) {
            abort(403, 'Kein Mitarbeiter verknüpft.');
        }
        $data['created_by'] = $employeeId;

        // handle audio file
        if ($request->hasFile('audio')) {
            $path = $request->file('audio')->store('breaking-news-audio', 'public');
            $data['audio_path'] = $path;
        }

        $news = BreakingNews::create($data);
        $news->load('creator');

        return response()->json([
            'success' => true,
            'data'    => $this->serialize($news),
        ]);
    }

    public function update(Request $request, BreakingNews $breakingNews)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('audio')) {
            $path = $request->file('audio')->store('breaking-news-audio', 'public');
            $data['audio_path'] = $path;
        }

        $breakingNews->update($data);
        $breakingNews->load('creator');

        return response()->json([
            'success' => true,
            'data'    => $this->serialize($breakingNews),
        ]);
    }

    
    public function toggle(BreakingNews $breakingNews)
    {
        $breakingNews->is_active = ! $breakingNews->is_active;
        $breakingNews->save();

        return response()->json([
            'success'   => true,
            'is_active' => (bool) $breakingNews->is_active,
        ]);
    }

    public function toggleStatus(BreakingNews $breakingNews)
    {
        return $this->toggle($breakingNews);
    }

    public function destroy(BreakingNews $breakingNews)
    {
        // Delete audio file if exists
        if ($breakingNews->audio_path && Storage::disk('public')->exists($breakingNews->audio_path)) {
            Storage::disk('public')->delete($breakingNews->audio_path);
        }

        $breakingNews->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function active()
    {
        $now = now()->timezone('Europe/Berlin');

        $items = BreakingNews::with('creator')
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            })
            ->orderByRaw('COALESCE(starts_at, created_at) DESC')
            ->get()
            ->map(function (BreakingNews $news) {
                $creator = $news->relationLoaded('creator') ? $news->creator : null;

                return [
                    'id'           => $news->id,
                    'title'        => $news->title,
                    'message'      => $news->message,
                    'type'         => strtolower($news->type ?? 'info'),
                    'scope'        => 'breaking',

                    'performed_at'       => $news->starts_at
                        ? $news->starts_at->toDateTimeString()
                        : $news->created_at->toDateTimeString(),
                    'performed_at_human' => $news->starts_at
                        ? $news->starts_at->diffForHumans()
                        : $news->created_at->diffForHumans(),

                    'creator_name'       => $creator
                        ? trim(($creator->name ?? '').' '.($creator->lastname ?? ''))
                        : null,
                    'creator_image_url'  => ($creator && $creator->image)
                        ? asset('images/employee/'.$creator->image)
                        : null,

                    // For the top-of-dashboard bar you can also play audio:
                    'audio_url'          => $this->audioUrl($news->audio_path ?? null),
                ];
            })
            ->values();

        return response()->json([
            'breakingNews' => $items,
        ]);
    }
}

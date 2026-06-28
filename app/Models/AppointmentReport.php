<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentReport extends Model
{
    protected $fillable = [
        'employee_id',
        'appointment_id',
        'task_id',
        'type',
        'report',
        'report_date',
        'next_step',
        'due_date',
        'report_by',
        'comments',   // JSON: {"items":[{employee_id, text, created_at}, ...]}
        'likes',      // simple int counter (optional)
        'dislikes',   // simple int counter (optional)
        'meta',       // JSON: {"items":[...], "likes":[employee_ids], "dislikes":[employee_ids]}
    ];

    protected $casts = [
        'report_date' => 'date',
        'due_date'    => 'date',
        'comments'    => 'array',  // own JSON: {"items":[...]}
        'meta'        => 'array',  // own JSON: {"items":[...], "likes":[...], "dislikes":[...]}
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function appointment()
    {
        return $this->belongsTo(MainAppointment::class, 'appointment_id');
    }

    public function employee()
    {
        // "owner" of the report (who it belongs to)
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function author()
    {
        // explicit author column if you separate it from employee_id
        return $this->belongsTo(Employee::class, 'report_by');
    }

    public function reporter()
    {
        // same as author(), keep whichever naming you use in code
        return $this->belongsTo(Employee::class, 'report_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers for JSON fields
    |--------------------------------------------------------------------------
    | NOTE: do NOT override getMetaAttribute() here, because we already cast
    | 'meta' => 'array' above. If you override it, you lose the automatic cast.
    |--------------------------------------------------------------------------
    */

    /**
     * Return meta as always-array (even if null).
     */
    public function safeMeta(): array
    {
        return is_array($this->meta) ? $this->meta : [];
    }

    /**
     * Return comments JSON as always-array (even if null).
     */
    public function safeComments(): array
    {
        return is_array($this->comments) ? $this->comments : [];
    }

    /**
     * All comment items (from comments JSON, NOT from meta).
     * Expected structure: {"items":[{employee_id, text, created_at}, ...]}
     */
    public function getCommentItemsAttribute(): array
    {
        $comments = $this->safeComments();
        $items    = $comments['items'] ?? [];

        return is_array($items) ? $items : [];
    }

    /**
     * Items from meta (e.g. if you also store rich history there).
     * Expected structure: {"items":[{employee_id, text, created_at}, ...], "likes":[...], "dislikes":[...]}
     */
    public function getMetaItemsAttribute(): array
    {
        $meta  = $this->safeMeta();
        $items = $meta['items'] ?? [];

        return is_array($items) ? $items : [];
    }

    /**
     * Array of employee_ids that liked this report (from meta['likes']).
     */
    public function getLikesListAttribute(): array
    {
        $meta  = $this->safeMeta();
        $likes = $meta['likes'] ?? [];

        return is_array($likes) ? $likes : [];
    }

    /**
     * Array of employee_ids that disliked this report (from meta['dislikes']).
     */
    public function getDislikesListAttribute(): array
    {
        $meta      = $this->safeMeta();
        $dislikes  = $meta['dislikes'] ?? [];

        return is_array($dislikes) ? $dislikes : [];
    }

    /**
     * Computed count of likes based on meta['likes'].
     * (You can still keep the integer 'likes' column as a cached counter if you want.)
     */
    public function getLikesCountAttribute(): int
    {
        return count($this->likes_list);
    }

    /**
     * Computed count of dislikes based on meta['dislikes'].
     */
    public function getDislikesCountAttribute(): int
    {
        return count($this->dislikes_list);
    }

    /**
     * What is the reaction of this employee? 'like', 'dislike', or null.
     */
    public function reactionOf(?int $employeeId): ?string
    {
        if (!$employeeId) {
            return null;
        }

        if (in_array($employeeId, $this->likes_list, true)) {
            return 'like';
        }

        if (in_array($employeeId, $this->dislikes_list, true)) {
            return 'dislike';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Convenience mutators (optional)
    |--------------------------------------------------------------------------
    | Call these from your controller when user clicks "Like"/"Dislike".
    |--------------------------------------------------------------------------
    */

    public function toggleLike(int $employeeId): void
    {
        $meta  = $this->safeMeta();
        $likes = $meta['likes'] ?? [];
        $dislikes = $meta['dislikes'] ?? [];

        // Remove from dislikes if present
        $dislikes = array_values(array_diff($dislikes, [$employeeId]));

        if (in_array($employeeId, $likes, true)) {
            // already liked -> remove like
            $likes = array_values(array_diff($likes, [$employeeId]));
        } else {
            // add like
            $likes[] = $employeeId;
        }

        $meta['likes']    = array_values(array_unique($likes));
        $meta['dislikes'] = $dislikes;

        $this->meta = $meta;
        // Optionally sync integer columns
        $this->likes    = count($meta['likes']);
        $this->dislikes = count($meta['dislikes']);
        $this->save();
    }

    public function toggleDislike(int $employeeId): void
    {
        $meta  = $this->safeMeta();
        $likes = $meta['likes'] ?? [];
        $dislikes = $meta['dislikes'] ?? [];

        // Remove from likes if present
        $likes = array_values(array_diff($likes, [$employeeId]));

        if (in_array($employeeId, $dislikes, true)) {
            // already disliked -> remove
            $dislikes = array_values(array_diff($dislikes, [$employeeId]));
        } else {
            // add dislike
            $dislikes[] = $employeeId;
        }

        $meta['likes']    = $likes;
        $meta['dislikes'] = array_values(array_unique($dislikes));

        $this->meta = $meta;
        $this->likes    = count($meta['likes']);
        $this->dislikes = count($meta['dislikes']);
        $this->save();
    }

    /**
     * Append a new comment item into comments JSON: {"items":[...]}
     */
    public function addCommentItem(int $employeeId, string $text): void
    {
        $comments = $this->safeComments();
        $items    = $comments['items'] ?? [];

        $items[] = [
            'employee_id' => $employeeId,
            'text'        => $text,
            'created_at'  => now()->toIso8601String(),
        ];

        $comments['items'] = $items;
        $this->comments    = $comments;
        $this->save();
    }
}

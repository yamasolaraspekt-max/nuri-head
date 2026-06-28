<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $table = 'inquiries';

    protected $fillable = [
        'pre_type',
        'source',
        'title',
        'type',
        'type_extra',

        'firma',
        'lastname',
        'name',

        'street',
        'full_address',
        'postcode',
        'city',

        'latitude',
        'longitude',
        'elevation',

        'polygon_height',
        'polygon_width',
        'polygon_area',

        'phone',
        'telephone',
        'email',

        'description',
        'note',
        'reason',

        'junk_reason',
        'junk_note',
        'junk_marked_at',
        'junk_marked_by',

        'status',
        'periority',
        'next_step',
        'due_date',

        'branch_id',
        'contact_person',
        'direct_to',
        'department_id',
        'personal_task_id',

        'verify_by',
        'verify_date',

        'is_draft',
        'draft_uuid',
        'last_autosaved_at',
    ];

    protected $casts = [
        'latitude'          => 'decimal:30',
        'longitude'         => 'decimal:30',
        'elevation'         => 'decimal:30',
        'polygon_height'    => 'decimal:4',
        'polygon_width'     => 'decimal:4',
        'polygon_area'      => 'decimal:4',

        'verify_date'       => 'date',
        'due_date'          => 'date',
        'junk_marked_at'    => 'datetime',
        'last_autosaved_at' => 'datetime',

        'is_draft'          => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'display_type',
        'is_junk',
        'is_published',
        'is_unpublished',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function comments()
    {
        return $this->hasMany(InquiryComment::class, 'inquiry_id');
    }

    public function products()
    {
        return $this->hasMany(InquiryProductList::class, 'inquiry_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function contactEmployee()
    {
        return $this->belongsTo(Employee::class, 'contact_person');
    }

    public function directEmployee()
    {
        return $this->belongsTo(Employee::class, 'direct_to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function personalTask()
    {
        return $this->belongsTo(PersonalTask::class, 'personal_task_id');
    }

    public function verifier()
    {
        return $this->belongsTo(Employee::class, 'verify_by');
    }

    public function junkMarker()
    {
        return $this->belongsTo(Employee::class, 'junk_marked_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(($this->name ?? '') . ' ' . ($this->lastname ?? ''));
    }

    public function getDisplayTypeAttribute(): ?string
    {
        return $this->pre_type ?: $this->type;
    }

    public function getIsJunkAttribute(): bool
    {
        return strtolower(trim((string) $this->status)) === 'junk';
    }

    public function getIsPublishedAttribute(): bool
    {
        return strtolower(trim((string) $this->status)) === 'published';
    }

    public function getIsUnpublishedAttribute(): bool
    {
        return strtolower(trim((string) $this->status)) === 'unpublished';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->whereNull($this->getTable() . '.deleted_at');
    }

    public function scopeJunk(Builder $query): Builder
    {
        return $query->whereRaw('TRIM(LOWER(status)) = ?', ['junk']);
    }

    public function scopeNotJunk(Builder $query): Builder
    {
        return $query->whereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['junk']);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereRaw('TRIM(LOWER(status)) = ?', ['published']);
    }

    public function scopeUnpublished(Builder $query): Builder
    {
        return $query->whereRaw('TRIM(LOWER(status)) = ?', ['unpublished']);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'Draft');

            if (\Schema::hasColumn($this->getTable(), 'is_draft')) {
                $q->orWhere('is_draft', true);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull($this->getTable() . '.deleted_at')
            ->whereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['junk']);
    }

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['published'])
              ->orWhereNull('verify_by')
              ->orWhereNull('verify_date');
        });
    }

    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeByPreType(Builder $query, ?string $preType): Builder
    {
        if (!$preType) {
            return $query;
        }

        return $query->where('pre_type', $preType);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $like = '%' . $search . '%';
        $digits = preg_replace('/\D+/', '', $search);

        return $query->where(function (Builder $q) use ($like, $digits) {
            $q->where('id', 'LIKE', $like)
                ->orWhere('name', 'LIKE', $like)
                ->orWhere('lastname', 'LIKE', $like)
                ->orWhereRaw("CONCAT(COALESCE(name,''), ' ', COALESCE(lastname,'')) LIKE ?", [$like])
                ->orWhere('firma', 'LIKE', $like)
                ->orWhere('email', 'LIKE', $like)
                ->orWhere('phone', 'LIKE', $like)
                ->orWhere('telephone', 'LIKE', $like)
                ->orWhere('street', 'LIKE', $like)
                ->orWhere('postcode', 'LIKE', $like)
                ->orWhere('city', 'LIKE', $like)
                ->orWhere('full_address', 'LIKE', $like)
                ->orWhere('note', 'LIKE', $like)
                ->orWhere('reason', 'LIKE', $like)
                ->orWhere('junk_reason', 'LIKE', $like)
                ->orWhere('junk_note', 'LIKE', $like)
                ->orWhere('source', 'LIKE', $like)
                ->orWhere('status', 'LIKE', $like)
                ->orWhere('periority', 'LIKE', $like)
                ->orWhere('pre_type', 'LIKE', $like)
                ->orWhere('type', 'LIKE', $like)
                ->orWhere('type_extra', 'LIKE', $like)
                ->orWhereRaw("DATE_FORMAT(created_at, '%d.%m.%Y') LIKE ?", [$like])
                ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", [$like])
                ->orWhereRaw("DATE_FORMAT(verify_date, '%d.%m.%Y') LIKE ?", [$like]);

            if ($digits !== '') {
                $q->orWhereRaw("REGEXP_REPLACE(COALESCE(phone,''), '[^0-9]+', '') LIKE ?", ["%{$digits}%"])
                  ->orWhereRaw("REGEXP_REPLACE(COALESCE(telephone,''), '[^0-9]+', '') LIKE ?", ["%{$digits}%"]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | State helpers
    |--------------------------------------------------------------------------
    */

    public function markJunk(?string $junkReason = null, ?string $junkNote = null, ?int $employeeId = null): self
    {
        $this->status = 'Junk';
        $this->junk_reason = $junkReason;
        $this->junk_note = $junkNote;
        $this->junk_marked_at = now();
        $this->junk_marked_by = $employeeId;
        $this->save();

        return $this;
    }

    public function markUnjunk(): self
    {
        $this->status = 'Unpublished';
        $this->junk_reason = null;
        $this->junk_note = null;
        $this->junk_marked_at = null;
        $this->junk_marked_by = null;
        $this->save();

        return $this;
    }

    public function markVerified($by): self
    {
        $this->status = 'Published';
        $this->verify_by = $by;
        $this->verify_date = now();
        $this->save();

        return $this;
    }

    public function markPublished($by = null): self
    {
        $this->status = 'Published';

        if ($by !== null) {
            $this->verify_by = $by;
        }

        if (empty($this->verify_date)) {
            $this->verify_date = now();
        }

        $this->save();

        return $this;
    }

    public function markUnpublished(): self
    {
        $this->status = 'Unpublished';
        $this->save();

        return $this;
    }

    public function markDraft(): self
    {
        $this->status = 'Draft';

        if (\Schema::hasColumn($this->getTable(), 'is_draft')) {
            $this->is_draft = true;
        }

        if (\Schema::hasColumn($this->getTable(), 'last_autosaved_at')) {
            $this->last_autosaved_at = now();
        }

        $this->save();

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility helpers
    |--------------------------------------------------------------------------
    */

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    public function isVerified(): bool
    {
        return !empty($this->verify_by) && !empty($this->verify_date) && $this->is_published;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadStage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'color',
        'icon',
        'sort_order',
        'is_default',
        'is_protected',
        'is_closed',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_default' => 'boolean',
        'is_protected' => 'boolean',
        'is_closed' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeadStage $stage) {
            $stage->name = trim((string) $stage->name);

            if (blank($stage->key)) {
                $stage->key = static::makeKey($stage->name);
            } else {
                $stage->key = static::makeKey((string) $stage->key);
            }

            if (blank($stage->color)) {
                $stage->color = '#74b2d4';
            }

            if (blank($stage->icon)) {
                $stage->icon = 'circle';
            }

            if ($stage->sort_order === null) {
                $stage->sort_order = ((int) static::query()->max('sort_order')) + 10;
            }

            $stage->is_active = $stage->is_active ?? true;
            $stage->is_default = $stage->is_default ?? false;
            $stage->is_protected = $stage->is_protected ?? false;
            $stage->is_closed = $stage->is_closed ?? false;
        });

        static::saving(function (LeadStage $stage) {
            $stage->name = trim((string) $stage->name);

            /*
             |--------------------------------------------------------------------------
             | Critical rule
             |--------------------------------------------------------------------------
             | lead_product_lists.status stores this key.
             | Therefore an existing stage key must never change during rename.
             |
             | Example:
             | id=16 key=accepted name=Annehmen
             | Rename to "Gewonnen" must save:
             | id=16 key=accepted name=Gewonnen
             |
             | This also protects against old JS accidentally sending a key value.
             */
            if ($stage->exists && filled($stage->getOriginal('key'))) {
                $stage->key = (string) $stage->getOriginal('key');
            } else {
                $stage->key = static::makeKey((string) ($stage->key ?: $stage->name));
            }

            if (blank($stage->color)) {
                $stage->color = '#74b2d4';
            }

            if (blank($stage->icon)) {
                $stage->icon = 'circle';
            }
        });

        /*
         * Do NOT add a saved() hook that makes all other is_default rows false.
         *
         * In this table, is_default means "built-in/system phase".
         * Multiple rows are default/system phases:
         * lead, offer, follow_up, accepted, deal, project, completed, archive, junk.
         *
         * Only LeadStageSubStage may have "one default per parent stage".
         */
    }

    public static function makeKey(string $name): string
    {
        $key = Str::slug($name, '_');
        $key = strtolower(trim($key, '_'));

        return $key !== '' ? $key : 'stage';
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function subStages()
    {
        return $this->hasMany(LeadStageSubStage::class, 'lead_stage_id', 'id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeSubStages()
    {
        return $this->hasMany(LeadStageSubStage::class, 'lead_stage_id', 'id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function defaultSubStage()
    {
        return $this->hasOne(LeadStageSubStage::class, 'lead_stage_id', 'id')
            ->where('is_default', true);
    }

    public function taskPhases()
    {
        return $this->hasMany(TaskPhase::class, 'lead_stage_id', 'id');
    }

    public function usageCount(): int
    {
        if (DB::getSchemaBuilder()->hasColumn('task_phases', 'lead_stage_id')) {
            return DB::table('task_phases')
                ->whereNull('deleted_at')
                ->where('lead_stage_id', $this->id)
                ->count();
        }

        $key = strtolower(trim((string) $this->key));

        $aliases = match ($key) {
            'lead' => ['lead', 'open', 'new', 'neu', 'neue', ''],
            'offer' => ['offer', 'angebot', 'verkauf'],
            'follow_up' => ['follow_up', 'followup', 'nachfassen'],
            'accepted' => ['accepted', 'annehmen', 'annemen', 'angenommen'],
            'deal' => ['deal', 'auftrag'],
            'project' => ['project', 'projekt', 'montage'],
            'completed' => ['completed', 'complete', 'abschluss'],
            'archive' => ['archive', 'archiv'],
            'junk' => ['junk', 'reject', 'rejeck'],
            default => [$key],
        };

        $normalAliases = array_values(array_filter($aliases, fn($value) => $value !== ''));

        return DB::table('lead_product_lists')
            ->where(function ($query) use ($normalAliases, $aliases) {
                $query->whereIn(
                    DB::raw('LOWER(COALESCE(NULLIF(status, ""), "lead"))'),
                    $normalAliases
                );

                if (in_array('', $aliases, true)) {
                    $query->orWhereNull('status')->orWhere('status', '');
                }
            })
            ->count();
    }
}

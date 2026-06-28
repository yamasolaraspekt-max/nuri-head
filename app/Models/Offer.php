<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\AuditableLead;
use Illuminate\Database\Eloquent\HasOne;
use Illuminate\Support\Facades\DB;

class Offer extends Model
{
    use SoftDeletes, Notifiable;
    use AuditableLead;

    protected $fillable = [
        'offer_no',
        'customer_id',
        'product_id',
        'alternative_id',
        'service_id',
        'department_id',
        'service',
        'created_by',
        'created_for',
        'status',
        'status_msg',
    ];

   protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            if (blank($offer->offer_no)) {
                $offer->offer_no = static::generateOfferNo(
                    $offer->customer_id,
                    $offer->department_id
                );
            }
        });

        static::updating(function (Offer $offer) {
            if (blank($offer->offer_no)) {
                $offer->offer_no = static::generateOfferNo(
                    $offer->customer_id,
                    $offer->department_id
                );
            }
        });
    }
    public static function generateOfferNo(?int $customerId = null, ?int $departmentId = null): string
    {
        return DB::transaction(function () use ($customerId, $departmentId) {
            $branchPrefix = static::resolveOfferPrefix($customerId, $departmentId);
            $year = date('y');
            $basePrefix = "{$branchPrefix}-AN{$year}";

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Use withTrashed(), because soft-deleted offers still keep unique offer_no.
            |--------------------------------------------------------------------------
            */
            $lastOfferNo = static::withTrashed()
                ->where('offer_no', 'like', $basePrefix . '%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(offer_no, ?) AS UNSIGNED) DESC', [strlen($basePrefix) + 1])
                ->value('offer_no');

            $nextNumber = 1;

            if ($lastOfferNo && preg_match('/^' . preg_quote($basePrefix, '/') . '(\d{3,})$/', $lastOfferNo, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            /*
            |--------------------------------------------------------------------------
            | Extra safety loop:
            | If number exists for any reason, move to next one.
            |--------------------------------------------------------------------------
            */
            do {
                $offerNo = sprintf('%s%03d', $basePrefix, $nextNumber);

                $exists = static::withTrashed()
                    ->where('offer_no', $offerNo)
                    ->exists();

                if ($exists) {
                    $nextNumber++;
                }
            } while ($exists);

            return $offerNo;
        }, 3);
    }
    public static function resolveOfferPrefix(?int $customerId = null, ?int $departmentId = null): string
    {
        /*
        |--------------------------------------------------------------------------
        | Choose branch source here
        |--------------------------------------------------------------------------
        | Priority:
        | 1. Department name/short_name if present
        | 2. Customer branchRel if present
        | 3. fallback = OF
        */

        if ($departmentId) {
            $department = \App\Models\Department::query()
                ->select('id', 'department_name')
                ->find($departmentId);

            if ($department) {
                $departmentText = trim((string) ($department->department_name ?? ''));

                if ($departmentText !== '') {
                    return static::mapBranchNameToPrefix($departmentText);
                }
            }
        }

        if ($customerId) {
            $customer = \App\Models\NewLeads::query()
                ->with('branchRel')
                ->find($customerId);

            $branchText = trim((string) (
                $customer?->branchRel?->short_name
                ?? $customer?->branchRel?->name
                ?? ''
            ));

            if ($branchText !== '') {
                return static::mapBranchNameToPrefix($branchText);
            }
        }

        return 'AN';
    }

    protected static function mapBranchNameToPrefix(string $branchName): string
    {
        $normalized = mb_strtolower(trim($branchName));

        return match ($normalized) {
            'solar aspekt', 'solaraspekt', 'sa'     => 'SA',
            'werk-studio', 'werk studio', 'ws'      => 'WS',
            default                                 => static::makePrefixFromText($branchName),
        };
    }

  protected static function makePrefixFromText(string $text): string
    {
        $text = trim($text);

        $words = preg_split('/[\s\-_]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $letters = '';

        foreach ($words as $word) {
            $letters .= mb_substr($word, 0, 1);
        }

        $letters = strtoupper($letters);

        if (strlen($letters) >= 2) {
            return substr($letters, 0, 2);
        }

        $fallback = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $text), 0, 2));

        return $fallback !== '' ? str_pad($fallback, 2, 'X') : 'AN';
    }

    // 💼 Relationships

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function detail()
    {
        return $this->hasOne(OfferDetail::class, 'offer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function folderAttachments()
    {
        return $this->hasMany(\App\Models\OfferFolderAttachment::class, 'offer_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function servicePhase()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function productGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(Employee::class, 'created_for');
    }

    public function service()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function responsible()
    {
        return $this->belongsTo(Employee::class, 'created_for');
    }

    public function comments()
    {
        return $this->hasMany(OfferComment::class, 'product_id', 'product_id')
            ->whereColumn('customer_id', 'offers.customer_id')
            ->whereColumn('alternative_id', 'offers.alternative_id');
    }

    public function scopeWithAllForConfig($q)
    {
        return $q->with([
            'customer',
            'customer.mainAddress',
            'customer.addresses',
            'customer.contact',
            'customer.branchRel',
            'alternative',
            'productGroup',
            'creator',
            'assignee',
            'service',
            'department',
        ]);
    }

    public function folders()
    {
        return $this->hasMany(OfferFolder::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(OfferDetail::class);
    }

    public function detailForFolder(int $folderId)
    {
        return $this->details()->where('folder_id', $folderId);
    }

    public function productLists()
    {
        return $this->hasMany(OfferProductList::class);
    }

    public function assetLists()
    {
        return $this->hasMany(OfferAssetList::class);
    }

    public function pdfPrints()
    {
        return $this->hasMany(OfferPdfPrint::class, 'offer_id')->orderByDesc('created_at');
    }

    public function leadProductList()
    {
        return $this->hasOne(\App\Models\LeadProductList::class, 'customer_id', 'customer_id')
            ->whereColumn('lead_product_lists.product_id', 'offers.product_id')
            ->whereColumn('lead_product_lists.alternative_id', 'offers.alternative_id');
    }

    public function getTeamEmployeeIdsAttribute(): array
    {
        $teams = $this->leadProductList?->teams ?? [];

        return collect($teams)
            ->pluck('employee_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }


}
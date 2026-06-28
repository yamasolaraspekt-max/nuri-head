<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\LeadActivityBroadcast;

trait AuditableLead
{
    public static function bootAuditableLead()
    {
        static::created(function ($model) {
            self::logChange($model, 'created', [
                'attributes' => $model->getAttributes()
            ]);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $logPayload = [];

            foreach ($changes as $key => $newValue) {
                if (in_array($key, ['updated_at', 'created_at', 'deleted_at'])) {
                    continue;
                }

                $logPayload[$key] = [
                    'from' => $model->getOriginal($key),
                    'to' => $newValue,
                ];
            }

            if (!empty($logPayload)) {
                self::logChange($model, 'updated', $logPayload);
            }
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted', [
                'attributes' => $model->getAttributes()
            ]);
        });
    }

    protected static function logChange($model, string $event, array $data): void
    {
        $leadId = $model->customer_id
            ?? $model->lead_id
            ?? ($model instanceof \App\Models\NewLeads ? $model->id : null);

        $altId = $model->alternative_id
            ?? ($model instanceof \App\Models\LeadAlternativeAdd ? $model->id : null);

        $prodId = $model->product_id
            ?? ($model instanceof \App\Models\ArticleGroup ? $model->id : null);

        $employeeId = Auth::check() ? Auth::user()->name : null;
        $employeeName = 'System';

        if (Auth::check() && Auth::user()->employee) {
            $emp = Auth::user()->employee;
            $employeeName = trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? ''));
        } elseif ($employeeId) {
            $employeeName = 'Mitarbeiter #' . $employeeId;
        }

        $customerName = 'Unbekannter Kunde';

        if ($leadId) {
            $customer = \App\Models\NewLeads::find($leadId);

            if ($customer) {
                $customerName = trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''));

                if ($customerName === '') {
                    $customerName = $customer->firma ?: '#' . $leadId;
                }
            }
        }

        $productName = 'Allgemein';

        if ($prodId) {
            $product = \App\Models\ArticleGroup::find($prodId);

            if ($product) {
                $productName = $product->article_group;
            }
        }

        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $leadId,
            'alternative_id' => $altId,
            'product_id' => $prodId,
            'user_id' => Auth::id(),
            'user_name' => $employeeId ?? 'System',
            'event_type' => $event,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => json_encode($data),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classNameRaw = class_basename($model);

        $classNameDe = match ($classNameRaw) {
            'CustomerNote' => 'Notizen',
            'NewLeads' => 'Kunde',
            'LeadAlternativeAdd' => 'Objekt / Adresse',
            'LeadProductList' => 'Prozess',
            'Problem' => 'Ticket',
            'Appointment' => 'Termin',
            'MaintenanceAsset' => 'Wartungsanlage',
            default => $classNameRaw,
        };

        $actionDe = match ($event) {
            'created' => 'erstellt',
            'updated' => 'aktualisiert',
            'deleted' => 'gelöscht',
            default => $event,
        };

        $detailText = "Eintrag {$actionDe}";

        if ($classNameRaw === 'CustomerNote') {
            $noteText = $model->description ?? 'Kein Text';

            if (mb_strlen($noteText) > 60) {
                $noteText = mb_substr($noteText, 0, 60) . '...';
            }

            $detailText = $noteText;
        }

        if (class_exists(LeadActivityBroadcast::class)) {
            broadcast(new LeadActivityBroadcast([
                'customer_id' => $leadId,
                'product_id' => $prodId,
                'employee_id' => $employeeId,
                'action' => $event,
                'model_de' => $classNameDe,
                'customer_name' => $customerName,
                'product_name' => $productName,
                'creator_name' => $employeeName,
                'detail_text' => $detailText,
                'time' => now()->format('H:i'),
            ]))->toOthers();
        }
    }
}
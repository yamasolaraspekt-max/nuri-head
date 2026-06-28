<?php

namespace App\Notifications;

use App\Models\GoodsReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoodsReceiptActionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public GoodsReceipt $goodsReceipt,
        public string $action,          // create|update|quick_status|issue|delete
        public ?int $employeeId,        // from auth()->user()->name
        public array $payload = []      // old/new/filter meta
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'module' => 'goods_receipts',
            'action' => $this->action,

            'goods_receipt_id' => $this->goodsReceipt->id,
            'code' => $this->goodsReceipt->code,
            'status' => $this->goodsReceipt->status,
            'inspection_status' => $this->goodsReceipt->inspection_status,

            // who did it (your custom auth mapping)
            'employee_id' => $this->employeeId,

            // useful extra info for UI
            'title' => $this->title(),
            'message' => $this->message(),
            'payload' => $this->payload,

            'created_at' => now()->toDateTimeString(),
        ];
    }

    protected function title(): string
    {
        return match ($this->action) {
            'create' => 'Wareneingang erstellt',
            'update' => 'Wareneingang bearbeitet',
            'quick_status' => 'Status geändert',
            'issue' => 'Warenausgang gebucht',
            'delete' => 'Wareneingang gelöscht',
            default => 'Wareneingang Aktion',
        };
    }

    protected function message(): string
    {
        return match ($this->action) {
            'create' => "Eintrag {$this->goodsReceipt->code} wurde erstellt.",
            'update' => "Eintrag {$this->goodsReceipt->code} wurde aktualisiert.",
            'quick_status' => "Status von {$this->goodsReceipt->code} wurde geändert.",
            'issue' => "Eintrag {$this->goodsReceipt->code} wurde ausgebucht.",
            'delete' => "Eintrag {$this->goodsReceipt->code} wurde gelöscht.",
            default => "Aktion auf {$this->goodsReceipt->code}.",
        };
    }
}
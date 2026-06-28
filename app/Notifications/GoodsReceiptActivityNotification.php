<?php

namespace App\Notifications;

use App\Models\GoodsReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoodsReceiptActivityNotification extends Notification
{
    use Queueable;

    protected GoodsReceipt $goodsReceipt;
    protected string $action;
    protected ?int $actorEmployeeId;
    protected ?string $actorEmployeeName;
    protected ?string $title;
    protected ?string $message;
    protected ?array $oldValues;
    protected ?array $newValues;
    protected ?array $meta;

    public function __construct(
        GoodsReceipt $goodsReceipt,
        string $action,
        ?int $actorEmployeeId = null,
        ?string $actorEmployeeName = null,
        ?string $title = null,
        ?string $message = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $meta = null
    ) {
        $this->goodsReceipt = $goodsReceipt;
        $this->action = $action;
        $this->actorEmployeeId = $actorEmployeeId;
        $this->actorEmployeeName = $actorEmployeeName;
        $this->title = $title;
        $this->message = $message;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->meta = $meta;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title ?: 'Warenbewegung',
            'message' => $this->message ?: 'Eine Warenbewegung wurde ausgeführt.',
            'action' => $this->action,

            'goods_receipt_id' => $this->goodsReceipt->id,
            'goods_receipt_code' => $this->goodsReceipt->code,

            'actor_employee_id' => $this->actorEmployeeId,
            'actor_employee_name' => $this->actorEmployeeName,

            'status' => $this->goodsReceipt->status,
            'inspection_status' => $this->goodsReceipt->inspection_status,

            'happened_at' => now()->toDateTimeString(),

            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,

            'meta' => $this->meta,
        ];
    }
}
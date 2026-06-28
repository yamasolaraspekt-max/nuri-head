<?php

namespace App\Events;

use App\Models\CustomerNote;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerNoteChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CustomerNote $note,
        public string $action,
        public array $extra = []
    ) {
    }

    public function broadcastOn(): array
    {
        $base = 'customer-notes.' .
            $this->note->customer_id . '.' .
            $this->note->alternative_id . '.' .
            ($this->note->lead_product_list_id ?: 'general');

        $channels = [new PrivateChannel($base)];

        if ($this->note->lead_stage_sub_stage_id) {
            $channels[] = new PrivateChannel($base . '.sub-stage.' . $this->note->lead_stage_sub_stage_id);
        } elseif ($this->note->lead_stage_key || $this->note->stage) {
            $channels[] = new PrivateChannel($base . '.stage.' . ($this->note->lead_stage_key ?: $this->note->stage));
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'customer-note.changed';
    }

    public function broadcastWith(): array
    {
        $this->note->loadMissing([
            'creator:id,name,lastname,image',
            'leadStage:id,key,name,color,icon',
            'leadStageSubStage:id,lead_stage_id,key,name,color,icon',
        ]);

        return [
            'action' => $this->action,
            'note' => [
                'id' => $this->note->id,
                'customer_id' => $this->note->customer_id,
                'alternative_id' => $this->note->alternative_id,
                'product_id' => $this->note->product_id,
                'lead_product_list_id' => $this->note->lead_product_list_id,
                'parent_id' => $this->note->parent_id,
                'description' => $this->note->description,
                'due_date' => optional($this->note->due_date)->format('Y-m-d'),
                'color' => $this->note->color,
                'order_no' => $this->note->order_no,
                'stage' => $this->note->stage,
                'type' => $this->note->type,

                'lead_stage_id' => $this->note->lead_stage_id,
                'lead_stage_key' => $this->note->lead_stage_key,
                'lead_stage_name' => $this->note->lead_stage_name ?: optional($this->note->leadStage)->name,
                'lead_stage_color' => $this->note->lead_stage_color ?: optional($this->note->leadStage)->color,
                'lead_stage_sub_stage_id' => $this->note->lead_stage_sub_stage_id,
                'lead_stage_sub_stage_name' => $this->note->lead_stage_sub_stage_name ?: optional($this->note->leadStageSubStage)->name,
                'lead_stage_sub_stage_color' => $this->note->lead_stage_sub_stage_color ?: optional($this->note->leadStageSubStage)->color,
                'stage_context' => $this->note->stage_context,

                'created_by' => $this->note->created_by,
                'created_at' => optional($this->note->created_at)->toDateTimeString(),
                'updated_at' => optional($this->note->updated_at)->toDateTimeString(),
                'deleted_at' => optional($this->note->deleted_at)->toDateTimeString(),
                'history' => $this->note->history ?? [],
                'read_by' => $this->note->read_by ?? [],
                'last_read_at' => optional($this->note->last_read_at)->toDateTimeString(),
                'creator' => $this->note->creator ? [
                    'id' => $this->note->creator->id,
                    'name' => $this->note->creator->name,
                    'lastname' => $this->note->creator->lastname,
                    'image' => $this->note->creator->image,
                ] : null,
            ],
            'extra' => $this->extra,
        ];
    }
}

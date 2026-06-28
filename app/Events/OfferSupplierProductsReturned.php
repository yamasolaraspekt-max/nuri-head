<?php

namespace App\Events;

use App\Models\OfferFolder;
use App\Models\SupplierImportLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferSupplierProductsReturned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public OfferFolder $folder,
        public SupplierImportLog $log,
        public array $items = [],
        public ?int $targetSectionIndex = null
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('offer-folder.' . $this->folder->id);
    }

    public function broadcastAs(): string
    {
        return 'supplier.products.imported';
    }

    public function broadcastWith(): array
    {
        return [
            'folder_id' => (int) $this->folder->id,
            'offer_id' => (int) ($this->folder->offer_id ?: $this->folder->offer?->id),
            'log_id' => (int) $this->log->id,
            'target_section_index' => $this->targetSectionIndex,
            'items' => $this->items,
            'message' => count($this->items) . ' Lieferantenartikel wurden übernommen.',
        ];
    }
}

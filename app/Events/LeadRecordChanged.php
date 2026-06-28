<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class LeadRecordChanged
{
    use SerializesModels;

    public $model;
    public $eventType;
    public $changes;

    public function __construct(Model $model, string $eventType, array $changes = [])
    {
        $this->model = $model;
        $this->eventType = $eventType;
        $this->changes = $changes;
    }
}
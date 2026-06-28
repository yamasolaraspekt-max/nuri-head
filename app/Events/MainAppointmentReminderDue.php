<?php

namespace App\Events;

use App\Models\MainAppointment;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MainAppointmentReminderDue implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public MainAppointment $appointment;
    public int $employeeId;

    public function __construct(MainAppointment $appointment, int $employeeId)
    {
        $this->appointment = $appointment->loadMissing(['customer']);
        $this->employeeId = $employeeId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('employee-appointment.' . $this->employeeId);
    }

    public function broadcastAs(): string
    {
        return 'main.appointment.reminder';
    }

    public function broadcastWith(): array
    {
        $customer = $this->appointment->customer;

        $customerName = trim(
            ($customer->lastname ?? '') . ' ' . ($customer->name ?? '')
        );

        if (!$customerName && !empty($customer?->firma)) {
            $customerName = $customer->firma;
        }

        $address = $this->appointment->full_address;

        if (!$address) {
            $address = trim(
                ($this->appointment->street ?? '') . ', ' .
                ($this->appointment->postcode ?? '') . ' ' .
                ($this->appointment->city ?? '')
            );
        }

        return [
            'id' => $this->appointment->id,
            'title' => $this->appointment->name ?: 'Termin',
            'note' => $this->appointment->note,
            'appointment_type' => $this->appointment->appointment_type,
            'execution_type' => $this->appointment->execution_type,
            'start_date' => $this->appointment->start_date
                ? Carbon::parse($this->appointment->start_date)->format('Y-m-d')
                : null,
            'start_time' => $this->appointment->start_time,
            'end_time' => $this->appointment->end_time,
            'customer_id' => $this->appointment->customer_id,
            'customer_name' => $customerName ?: 'Unbekannter Kunde',
            'phone' => $this->appointment->phone ?: ($customer->phone ?? null),
            'email' => $this->appointment->email ?: ($customer->email ?? null),
            'address' => $address ?: 'Keine Adresse hinterlegt',
            'link' => $this->appointment->link,
            'url' => url('customer/appointments?appointment_id=' . $this->appointment->id),
        ];
    }
}
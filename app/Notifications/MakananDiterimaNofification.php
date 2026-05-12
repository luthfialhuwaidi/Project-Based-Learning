<?php

namespace App\Notifications;

use App\Models\Delivery;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MakananDiterimaNofification extends Notification
{
    use Queueable;

    public function __construct(
        public Delivery $delivery,
        public Student $student
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'makanan_diterima',
            'delivery_id' => $this->delivery->id,
            'kode_pengiriman' => $this->delivery->kode_pengiriman,
            'student_name' => $this->student->name,
            'school_name' => $this->delivery->school->name,
            'message' => 'Makanan bergizi untuk ' . $this->student->name . ' telah diterima di ' . $this->delivery->school->name,
            'timestamp' => now()->toISOString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}

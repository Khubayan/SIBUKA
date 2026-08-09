<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PointThresholdNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Student $student,
        public string $level,
        public int $totalPoin
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->student->loadMissing('kelas');

        $levelLabel = strtoupper($this->level);

        return (new MailMessage)
            ->subject("Peringatan Poin Siswa — {$levelLabel}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Siswa **{$this->student->nama}** ({$this->student->kelas?->nama}) mencapai ambang batas poin **{$levelLabel}**.")
            ->line("Total poin saat ini: **{$this->totalPoin}**")
            ->action('Lihat Profil Siswa', route('students.show', $this->student));
    }

    public function toArray(object $notifiable): array
    {
        $this->student->loadMissing('kelas');

        return [
            'type' => 'point_threshold',
            'student_id' => $this->student->id,
            'student_name' => $this->student->nama,
            'kelas' => $this->student->kelas?->nama,
            'level' => $this->level,
            'total_poin' => $this->totalPoin,
            'message' => "Siswa {$this->student->nama} mencapai ambang {$this->level} (poin: {$this->totalPoin})",
        ];
    }
}

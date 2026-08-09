<?php

namespace App\Notifications;

use App\Models\CaseRecord;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CaseRecord $case) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->case->loadMissing('student');

        return (new MailMessage)
            ->subject('Pemberitahuan Kasus Siswa — SIBUKA')
            ->greeting("Yth. {$notifiable->name},")
            ->line("Anak Anda **{$this->case->student->nama}** tercatat kasus baru:")
            ->line('Kategori: '.ucfirst($this->case->kategori->value))
            ->line("Judul: {$this->case->judul}")
            ->line("Poin: {$this->case->poin}")
            ->line('Tanggal: '.$this->case->tanggal_kejadian->format('d/m/Y'))
            ->action('Lihat Detail', route('portal.child', $this->case->student_id));
    }

    public function toArray(object $notifiable): array
    {
        $this->case->loadMissing('student');

        return [
            'type' => 'new_case',
            'case_id' => $this->case->id,
            'student_id' => $this->case->student_id,
            'student_name' => $this->case->student->nama,
            'judul' => $this->case->judul,
            'kategori' => $this->case->kategori->value,
            'poin' => $this->case->poin,
            'message' => "Kasus baru dikonfirmasi: {$this->case->judul}",
        ];
    }
}

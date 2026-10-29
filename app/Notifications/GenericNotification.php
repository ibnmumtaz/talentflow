<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $title, public string $message, public ?string $url = null, public ?string $subject = null) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $m = (new MailMessage)->subject($this->subject ?? $this->title)->line($this->message);
        if ($this->url) {
            $m->action('View in TalentFlow', $this->url);
        }

        return $m->line('Thank you for using TalentFlow.');
    }

    public function toArray($notifiable): array
    {
        return ['type' => 'general', 'title' => $this->title, 'message' => $this->message, 'url' => $this->url];
    }
}

<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Interview $interview) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject("Interview scheduled: {$this->interview->job->title}")
            ->line("An interview has been scheduled for {$this->interview->scheduled_at->format('l, d M Y H:i')} ({$this->interview->timezone}).")
            ->line($this->interview->meeting_link ? "Meeting link: {$this->interview->meeting_link}" : '')
            ->action('View interview', route('candidate.interviews.index'));
    }

    public function toArray($notifiable): array
    {
        return ['type' => 'interview', 'title' => 'Interview scheduled', 'message' => "Interview for {$this->interview->job->title} on {$this->interview->scheduled_at->format('d M Y H:i')}.", 'interview_id' => $this->interview->id, 'url' => route('candidate.interviews.index')];
    }
}

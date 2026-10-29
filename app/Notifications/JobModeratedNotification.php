<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobModeratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Job $job) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject("Your job posting was {$this->job->status}")
            ->line("Your job \"{$this->job->title}\" is now {$this->job->status}.")
            ->action('View job', route('employer.jobs.show', $this->job));
    }

    public function toArray($notifiable): array
    {
        return ['type' => 'job_moderation', 'title' => "Job {$this->job->status}", 'message' => "Your job \"{$this->job->title}\" is now {$this->job->status}.", 'job_id' => $this->job->id, 'url' => route('employer.jobs.show', $this->job)];
    }
}

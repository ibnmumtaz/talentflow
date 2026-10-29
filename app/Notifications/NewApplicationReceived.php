<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->subject("New application: {$this->application->job->title}")
            ->line("{$this->application->user->name} applied for {$this->application->job->title}.")
            ->action('Review applicant', route('employer.applicants.show', $this->application));
    }

    public function toArray($notifiable): array
    {
        return ['type' => 'new_application', 'title' => 'New application received', 'message' => "{$this->application->user->name} applied for {$this->application->job->title}.", 'application_id' => $this->application->id, 'url' => route('employer.applicants.show', $this->application)];
    }
}

<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application, public string $from, public string $to) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your application for {$this->application->job->title} moved to {$this->application->stageLabel()}")
            ->line("Good news — your application for {$this->application->job->title} at {$this->application->job->company->name} is now: {$this->application->stageLabel()}.")
            ->action('View application', route('candidate.applications.show', $this->application))
            ->line('Thank you for using TalentFlow.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'application_status',
            'title' => "Application {$this->application->stageLabel()}",
            'message' => "Your application for {$this->application->job->title} moved from {$this->from} to {$this->to}.",
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'url' => route('candidate.applications.show', $this->application),
        ];
    }
}

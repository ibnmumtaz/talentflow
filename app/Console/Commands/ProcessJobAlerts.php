<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\JobAlert;
use App\Notifications\GenericNotification;
use Illuminate\Console\Command;

class ProcessJobAlerts extends Command
{
    protected $signature = 'talentflow:job-alerts';

    protected $description = 'Send job-alert notifications for newly published jobs and expire old jobs.';

    public function handle(): int
    {
        $expired = Job::where('status', 'published')->whereNotNull('expires_at')->where('expires_at', '<', now()->toDateString())->update(['status' => 'closed']);
        $this->info("Expired $expired job(s).");

        $recent = Job::visible()->where('published_at', '>=', now()->subDay())->with('company')->get();
        $sent = 0;
        foreach (JobAlert::where('is_active', true)->with('user')->get() as $alert) {
            $matches = $recent->filter(fn (Job $job) => $alert->matches($job));
            if ($matches->isEmpty()) {
                continue;
            }
            if ($alert->user->notifyPreference('job_alerts', true)) {
                $alert->user->notify(new GenericNotification(
                    $matches->count().' new job(s) match your alert',
                    $matches->take(3)->map(fn ($j) => $j->title.' at '.$j->company->name)->join('; '),
                    route('jobs.index'),
                    'New jobs matching your TalentFlow alert'
                ));
                $sent++;
            }
            $alert->update(['last_sent_at' => now()]);
        }
        $this->info("Sent $sent job alert notification(s).");

        return self::SUCCESS;
    }
}

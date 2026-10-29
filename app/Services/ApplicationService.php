<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\AuditLog;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    public static function transition(Application $application, string $to, $user, ?string $note = null): Application
    {
        if (! Application::canTransition($application->status, $to)) {
            throw new \InvalidArgumentException("Cannot move an application from {$application->status} to {$to}.");
        }

        return DB::transaction(function () use ($application, $to, $user, $note) {
            $from = $application->status;
            $application->update(['status' => $to]);
            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'from_status' => $from,
                'to_status' => $to,
                'changed_by_id' => $user->id,
                'note' => $note,
            ]);
            AuditLog::record($user, 'application.status_changed', $application, ['status' => $to], ['status' => $from]);
            if ($application->user->notifyPreference('application_status', true)) {
                $application->user->notify(new ApplicationStatusChanged($application, $from, $to));
            }

            return $application->fresh();
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('shared.settings.index', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'timezone' => ['required', 'string', 'max:60'],
            'notification_preferences' => ['nullable', 'array'],
        ]);
        $prefs = [];
        foreach (['application_status', 'new_application', 'interview', 'job_alerts', 'marketing'] as $key) {
            $prefs[$key] = $request->boolean("notification_preferences.$key", false) || ($request->input("notification_preferences.$key") === '1');
            // Default on unless explicitly unchecked in a submitted form: handled by checkbox presence.
            $prefs[$key] = $request->has("notification_preferences.$key");
        }
        $user->update(['name' => $data['name'], 'phone' => $data['phone'] ?? null, 'timezone' => $data['timezone'], 'notification_preferences' => $prefs]);

        return back()->with('status', 'Account settings saved.');
    }

    public function password(Request $request)
    {
        $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', Password::min(8)]]);
        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Password updated.');
    }

    public function export(Request $request)
    {
        $user = $request->user()->load(['candidateProfile', 'applications.job', 'savedJobs.job', 'jobAlerts']);
        $data = [
            'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
            'profile' => $user->candidateProfile?->only(['headline', 'summary', 'city', 'current_title', 'years_of_experience']),
            'applications' => $user->applications->map(fn ($a) => ['job' => $a->job->title, 'status' => $a->status, 'applied_at' => $a->created_at->toDateTimeString()]),
            'saved_jobs' => $user->savedJobs->map(fn ($s) => $s->job->title),
        ];

        return response()->json($data, 200, ['Content-Disposition' => 'attachment; filename=talentflow-my-data.json']);
    }

    public function deleteRequest(Request $request)
    {
        $request->validate(['confirmation' => ['accepted']]);
        $user = $request->user();
        AuditLog::record($user, 'account.deletion_requested', $user, []);
        Mail::raw("Account deletion requested by {$user->name} ({$user->email}). Review in admin.", function ($m) {
            $m->to(config('mail.from.address'))->subject('TalentFlow account deletion request');
        });

        return back()->with('status', 'Deletion request recorded. An administrator will review it. Your account remains active until then.');
    }
}

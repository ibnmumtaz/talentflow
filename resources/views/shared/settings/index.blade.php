@extends('layouts.dashboard', ['heading' => 'Account settings', 'subheading' => 'Name, contact, time zone, notification preferences, password and your data.'])
@section('dashboard')
@php $prefs = $user->notification_preferences ?? []; @endphp
<div class="grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('settings.update') }}" class="glass space-y-4 rounded-2xl p-6">
        @csrf @method('PUT')
        <h2 class="font-semibold">Profile &amp; preferences</h2>
        <div><label class="text-sm" for="name">Name</label><input id="name" name="name" required value="{{ old('name', $user->name) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="timezone">Time zone</label><select id="timezone" name="timezone" class="input-glass mt-1">@foreach(['Asia/Karachi','Asia/Dubai','Europe/London','America/New_York','UTC'] as $tz)<option @selected(old('timezone', $user->timezone) == $tz)>{{ $tz }}</option>@endforeach</select></div>
        <fieldset><legend class="text-sm font-medium">Email &amp; in-app notifications</legend>
            <div class="mt-2 space-y-2 text-sm">
                @foreach(['application_status' => 'Application status changes', 'new_application' => 'New applications (employers)', 'interview' => 'Interview invitations', 'job_alerts' => 'Job alert matches', 'marketing' => 'Marketing emails'] as $key => $label)
                    <label class="flex items-center gap-2"><input type="checkbox" name="notification_preferences[{{ $key }}]" value="1" @checked($prefs[$key] ?? true)> {{ $label }}</label>
                @endforeach
            </div>
        </fieldset>
        <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Save settings</button>
    </form>
    <div class="space-y-6">
        <form method="POST" action="{{ route('settings.password') }}" class="glass space-y-3 rounded-2xl p-6">
            @csrf @method('PUT')
            <h2 class="font-semibold">Change password</h2>
            <div><label class="text-sm" for="current_password">Current password</label><input id="current_password" type="password" name="current_password" required class="input-glass mt-1" autocomplete="current-password"></div>
            <div><label class="text-sm" for="password">New password</label><input id="password" type="password" name="password" required class="input-glass mt-1" autocomplete="new-password"></div>
            <div><label class="text-sm" for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" required class="input-glass mt-1" autocomplete="new-password"></div>
            <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Update password</button>
        </form>
        <div class="glass space-y-3 rounded-2xl p-6">
            <h2 class="font-semibold">Your data</h2>
            <p class="text-sm text-slate-400">Export the personal data TalentFlow holds about you (profile, applications, saved jobs and alerts) as JSON.</p>
            <a href="{{ route('settings.export') }}" class="btn-ghost inline-block rounded-xl px-4 py-2 text-sm">Export my data (JSON)</a>
            <form method="POST" action="{{ route('settings.delete-request') }}" data-confirm="Request account deletion? An administrator will review this request.">@csrf
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="confirmation" value="1" required> I understand this requests deletion and will be reviewed.</label>
                <button class="mt-3 rounded-xl border border-rose-400/40 px-4 py-2 text-sm text-rose-300">Request account deletion</button>
            </form>
        </div>
    </div>
</div>
@endsection

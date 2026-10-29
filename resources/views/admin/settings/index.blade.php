@extends('layouts.dashboard', ['heading' => 'Platform settings', 'subheading' => 'Branding, moderation and upload limits. Notification templates use Laravel notifications (see app/Notifications).'])
@section('dashboard')
<form method="POST" action="{{ route('admin.settings.update') }}" class="glass max-w-xl space-y-4 rounded-2xl p-6">
    @csrf @method('PUT')
    <div><label class="text-sm" for="site_name">Site name</label><input id="site_name" name="site_name" required value="{{ old('site_name', $settings['site_name'] ?? 'TalentFlow') }}" class="input-glass mt-1"></div>
    <div><label class="text-sm" for="site_tagline">Tagline</label><input id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="input-glass mt-1"></div>
    <div><label class="text-sm" for="support_email">Support email</label><input id="support_email" type="email" name="support_email" required value="{{ old('support_email', $settings['support_email'] ?? '') }}" class="input-glass mt-1"></div>
    <div><label class="text-sm" for="max_resume_size_kb">Max CV size (KB)</label><input id="max_resume_size_kb" type="number" name="max_resume_size_kb" required value="{{ old('max_resume_size_kb', $settings['max_resume_size_kb'] ?? 5120) }}" class="input-glass mt-1"></div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="jobs_require_moderation" value="1" @checked(($settings['jobs_require_moderation'] ?? '1') === '1')> Jobs require moderation before publication</label>
    <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Save settings</button>
</form>
<div class="glass mt-6 max-w-xl rounded-2xl p-6">
    <h2 class="font-semibold">Notification templates</h2>
    <p class="mt-2 text-sm text-slate-400">Email and in-app notifications are implemented as Laravel notification classes in <code>app/Notifications/</code>: application status changes, new applications, interview invitations, job moderation and company verification. Mail uses the configured mailer (log by default locally); see INSTALLATION.md to configure SMTP.</p>
</div>
@endsection

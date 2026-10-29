@extends('layouts.app')
@section('title', 'Contact us')
@section('content')
<div class="mt-8 max-w-2xl">
    <h1 class="text-3xl font-bold tracking-tight">Contact us</h1>
    <p class="mt-1 text-sm text-slate-400">We reply to genuine enquiries within two working days. For demo installations, messages are logged via the local mailer.</p>
    <form method="POST" action="{{ route('pages.contact.store') }}" class="glass mt-6 space-y-4 rounded-2xl p-6">
        @csrf
        <div><label class="text-sm" for="name">Name</label><input id="name" name="name" required value="{{ old('name') }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="email">Email</label><input id="email" type="email" name="email" required value="{{ old('email') }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="subject">Subject</label><input id="subject" name="subject" required value="{{ old('subject') }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="message">Message</label><textarea id="message" name="message" rows="5" required class="input-glass mt-1">{{ old('message') }}</textarea></div>
        <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Send message</button>
    </form>
</div>
@endsection

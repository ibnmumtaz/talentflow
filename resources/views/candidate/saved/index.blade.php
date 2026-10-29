@extends('layouts.dashboard', ['heading' => 'Saved jobs', 'subheading' => 'Jobs you bookmarked for later.'])
@section('dashboard')
@if($saved->isEmpty())<x-empty-state title="No saved jobs" message="Save jobs from any job page and they will appear here." />
@else<div class="grid gap-4 md:grid-cols-2">@foreach($saved as $s)<x-job-card :job="$s->job" />@endforeach</div><div class="mt-6">{{ $saved->links() }}</div>@endif
@endsection

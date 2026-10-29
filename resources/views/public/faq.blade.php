@extends('layouts.app')
@section('title', 'Frequently asked questions')
@section('content')
<div class="mt-8"><h1 class="text-3xl font-bold tracking-tight">Frequently asked questions</h1></div>
@foreach($faqs as $audience => $items)
    <h2 class="mt-8 text-lg font-semibold capitalize">For {{ $audience === 'all' ? 'everyone' : $audience.'s' }}</h2>
    <div class="mt-3 space-y-3">
        @foreach($items as $faq)<details class="glass rounded-2xl p-4"><summary class="cursor-pointer font-medium">{{ $faq->question }}</summary><p class="mt-2 text-sm text-slate-300">{{ $faq->answer }}</p></details>@endforeach
    </div>
@endforeach
@if($faqs->isEmpty())<div class="mt-6"><x-empty-state title="No FAQs yet" message="Check back soon." /></div>@endif
@endsection

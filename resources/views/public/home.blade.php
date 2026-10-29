@extends('layouts.app')
@section('title', 'Find jobs and hire talent')
@section('content')
<section class="mt-8 grid items-center gap-10 lg:grid-cols-2">
    <div>
        <p class="inline-flex rounded-full bg-violet-500/15 px-3 py-1 text-xs font-semibold text-violet-300">● {{ number_format($stats['jobs']) }} live jobs · {{ number_format($stats['companies']) }} verified companies</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">Find work you love.<br><span class="bg-gradient-to-r from-violet-400 via-indigo-400 to-cyan-300 bg-clip-text text-transparent">Hire people you trust.</span></h1>
        <p class="mt-4 max-w-xl text-slate-300">TalentFlow brings job seekers, recruiters and hiring teams into one clear workspace — searchable jobs, honest salary ranges, and an applicant pipeline you can actually follow.</p>
        <form method="GET" action="{{ route('jobs.index') }}" class="glass-strong mt-6 flex flex-col gap-2 rounded-2xl p-2 sm:flex-row" role="search">
            <label class="sr-only" for="q">Job title, keyword or company</label>
            <input id="q" name="q" value="{{ request('q') }}" class="input-glass flex-1" placeholder="Job title, keyword or company">
            <label class="sr-only" for="location">Location</label>
            <input id="location" name="location" value="{{ request('location') }}" class="input-glass sm:w-48" placeholder="Location">
            <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Search jobs</button>
        </form>
        <div class="mt-4 flex flex-wrap gap-2 text-sm">
            <a href="{{ route('jobs.remote') }}" class="btn-ghost rounded-full px-3 py-1.5">Remote jobs</a>
            <a href="{{ route('jobs.internships') }}" class="btn-ghost rounded-full px-3 py-1.5">Internships</a>
            <a href="{{ route('jobs.entry-level') }}" class="btn-ghost rounded-full px-3 py-1.5">Entry level</a>
            <a href="{{ route('register', ['role' => 'employer']) }}" class="btn-ghost rounded-full px-3 py-1.5">Post a job →</a>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <x-stat-card label="Live jobs" :value="number_format($stats['jobs'])" hint="Published and not expired" />
        <x-stat-card label="Verified companies" :value="number_format($stats['companies'])" hint="Reviewed by TalentFlow" />
        <x-stat-card label="Job seekers" :value="number_format($stats['candidates'])" hint="Registered candidates" />
        <x-stat-card label="Hires recorded" :value="number_format($stats['hired'])" hint="Applications marked hired" />
        <div class="glass rounded-2xl p-5 sm:col-span-2">
            <h2 class="font-semibold">How TalentFlow works</h2>
            <ol class="mt-3 grid gap-2 text-sm text-slate-300 sm:grid-cols-3">
                <li><strong class="text-white">1. Search</strong><br>Filter by role, location, salary and workplace.</li>
                <li><strong class="text-white">2. Apply</strong><br>Attach your CV, answer screening questions.</li>
                <li><strong class="text-white">3. Track</strong><br>Follow every stage, interview and offer.</li>
            </ol>
        </div>
    </div>
</section>

@if($featuredCompanies->isNotEmpty())
<section class="mt-14">
    <h2 class="text-xl font-bold">Trusted by featured employers</h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($featuredCompanies as $company)
            <a href="{{ route('companies.show', $company) }}" class="glass glass-hover rounded-2xl p-5 block">
                <p class="font-semibold">{{ $company->name }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $company->industry->name ?? '' }} · {{ $company->location->name ?? '' }}</p>
                <p class="mt-2 text-sm text-cyan-300">{{ $company->published_jobs_count }} open role(s) →</p>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="mt-14">
    <div class="flex items-end justify-between gap-3">
        <h2 class="text-xl font-bold">Featured jobs</h2>
        <a class="text-sm text-cyan-300 hover:underline" href="{{ route('jobs.featured') }}">View all featured →</a>
    </div>
    @if($featuredJobs->isEmpty())
        <x-empty-state title="No featured jobs right now" message="Check the latest jobs below — new roles are published every day." />
    @else
        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">@foreach($featuredJobs as $job)<x-job-card :job="$job" />@endforeach</div>
    @endif
</section>

<section class="mt-14">
    <h2 class="text-xl font-bold">Popular categories</h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $cat)
            <a href="{{ route('jobs.index', ['category_id' => $cat->id]) }}" class="glass glass-hover rounded-2xl p-5 block">
                <p class="font-semibold">{{ $cat->name }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $cat->jobs_count }} open job(s)</p>
            </a>
        @endforeach
    </div>
    <h2 class="mt-10 text-xl font-bold">Popular locations</h2>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach($locations as $loc)
            <a href="{{ route('jobs.index', ['location_id' => $loc->id]) }}" class="btn-ghost rounded-full px-4 py-2 text-sm">{{ $loc->name }} <span class="text-slate-400">({{ $loc->jobs_count }})</span></a>
        @endforeach
    </div>
</section>

<section class="mt-14">
    <div class="flex items-end justify-between gap-3">
        <h2 class="text-xl font-bold">Latest jobs</h2>
        <a class="text-sm text-cyan-300 hover:underline" href="{{ route('jobs.index') }}">Browse all jobs →</a>
    </div>
    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">@foreach($latestJobs as $job)<x-job-card :job="$job" />@endforeach</div>
</section>

@if($remoteJobs->isNotEmpty())
<section class="mt-14">
    <h2 class="text-xl font-bold">Remote &amp; hybrid work</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">@foreach($remoteJobs as $job)<x-job-card :job="$job" />@endforeach</div>
</section>
@endif

<section class="mt-14 grid gap-4 lg:grid-cols-2">
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-bold">For candidates</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-300">
            <li>✓ Build one profile, reuse it for every application.</li>
            <li>✓ Save jobs and get alerts when matching roles appear.</li>
            <li>✓ See your application stage, interviews and offers in one place.</li>
        </ul>
        <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Create a free candidate account</a>
    </div>
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-bold">For employers</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-300">
            <li>✓ Publish structured jobs with screening questions.</li>
            <li>✓ Move candidates through a real ATS pipeline.</li>
            <li>✓ Schedule interviews, record feedback and manage your team.</li>
        </ul>
        <a href="{{ route('register', ['role' => 'employer']) }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Hire on TalentFlow</a>
    </div>
</section>

@if($articles->isNotEmpty())
<section class="mt-14">
    <h2 class="text-xl font-bold">Career resources</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        @foreach($articles as $article)
            <a href="{{ route('resources.show', $article) }}" class="glass glass-hover rounded-2xl p-5 block">
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-300">{{ $article->category }}</p>
                <h3 class="mt-2 font-semibold">{{ $article->title }}</h3>
                <p class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $article->excerpt }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif

@if($testimonials->isNotEmpty())
<section class="mt-14">
    <h2 class="text-xl font-bold">What people say</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-3">
        @foreach($testimonials as $t)
            <figure class="glass rounded-2xl p-5">
                <p class="text-amber-300">{{ str_repeat('★', $t->rating) }}</p>
                <blockquote class="mt-2 text-sm text-slate-200">“{{ $t->quote }}”</blockquote>
                <figcaption class="mt-3 text-sm text-slate-400">{{ $t->name }} · {{ $t->role_title }}, {{ $t->company }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

<section class="mt-14 grid gap-4 lg:grid-cols-2">
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-bold">Frequently asked questions</h2>
        <div class="mt-3 space-y-3 text-sm">
            @foreach($faqs as $faq)
                <details class="rounded-xl bg-white/5 p-3"><summary class="cursor-pointer font-medium">{{ $faq->question }}</summary><p class="mt-2 text-slate-300">{{ $faq->answer }}</p></details>
            @endforeach
        </div>
        <a class="mt-4 inline-block text-sm text-cyan-300 hover:underline" href="{{ route('pages.faq') }}">See all FAQs →</a>
    </div>
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-bold">Get new jobs in your inbox</h2>
        <p class="mt-2 text-sm text-slate-400">A short weekly digest. No spam, unsubscribe anytime.</p>
        <form method="POST" action="{{ route('newsletter.store') }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
            @csrf
            <label class="sr-only" for="nl-email">Email</label>
            <input id="nl-email" name="email" type="email" required class="input-glass flex-1" placeholder="you@example.com">
            <label class="flex items-center gap-2 text-xs text-slate-400"><input type="checkbox" name="consent" value="1" required> I agree to receive emails</label>
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Subscribe</button>
        </form>
    </div>
</section>
@endsection

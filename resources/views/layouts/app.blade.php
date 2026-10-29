<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $brandName ?? 'TalentFlow') — TalentFlow</title>
    <meta name="description" content="@yield('description', 'TalentFlow — find jobs, hire talent, and manage recruitment in one premium platform.')">
    <meta property="og:title" content="@yield('title', 'TalentFlow')">
    <meta property="og:description" content="@yield('description', 'Find jobs and hire talent on TalentFlow.')">
    <meta property="og:type" content="website">
    @isset($canonical)<link rel="canonical" href="{{ $canonical }}">@endisset
    @isset($structuredData)<script type="application/ld+json">{!! $structuredData !!}</script>@endisset
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="ambient min-h-screen">
<header class="sticky top-0 z-40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <nav class="glass mt-3 flex items-center justify-between gap-3 rounded-2xl px-4 py-3" aria-label="Main">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold tracking-tight text-lg">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 via-indigo-500 to-cyan-400 text-white font-black">T</span>
                <span>Talent<span class="text-cyan-300">Flow</span></span>
            </a>
            <div class="hidden items-center gap-1 text-sm text-slate-300 lg:flex">
                <a class="rounded-lg px-3 py-2 {{ request()->is('jobs','jobs/*') && !request()->is('jobs/remote') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('jobs.index') }}">Jobs</a>
                <a class="rounded-lg px-3 py-2 {{ request()->is('companies','companies/*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('companies.index') }}">Companies</a>
                <a class="rounded-lg px-3 py-2 {{ request()->is('jobs/remote') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('jobs.remote') }}">Remote</a>
                <a class="rounded-lg px-3 py-2 {{ request()->is('resources','resources/*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('resources.index') }}">Career Resources</a>
                <a class="rounded-lg px-3 py-2 {{ request()->is('pricing') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('pages.pricing') }}">Pricing</a>
                <a class="rounded-lg px-3 py-2 {{ request()->is('employers') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" href="{{ route('pages.employers') }}">For Employers</a>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('notifications.index') }}" class="btn-ghost relative rounded-xl px-3 py-2 text-sm" aria-label="Notifications">
                        🔔
                        @if(auth()->user()->unreadNotifications()->count())
                            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[11px] font-bold">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="btn-ghost hidden rounded-xl px-3 py-2 text-sm sm:block">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-primary rounded-xl px-3 py-2 text-sm">Log out</button></form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost rounded-xl px-3 py-2 text-sm">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary rounded-xl px-3 py-2 text-sm">Sign up</a>
                @endauth
                <div class="lg:hidden" x-data="{open:false}">
                    <button @click="open=!open" class="btn-ghost rounded-xl px-3 py-2" aria-label="Menu">☰</button>
                    <div x-show="open" x-cloak class="glass-strong absolute right-4 top-16 w-56 rounded-2xl p-3 text-sm">
                        <a class="block rounded-lg px-3 py-2 {{ request()->is('jobs','jobs/*') && !request()->is('jobs/remote') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}" href="{{ route('jobs.index') }}">Jobs</a>
                        <a class="block rounded-lg px-3 py-2 {{ request()->is('companies','companies/*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}" href="{{ route('companies.index') }}">Companies</a>
                        <a class="block rounded-lg px-3 py-2 {{ request()->is('resources','resources/*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}" href="{{ route('resources.index') }}">Career Resources</a>
                        <a class="block rounded-lg px-3 py-2 {{ request()->is('pricing') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}" href="{{ route('pages.pricing') }}">Pricing</a>
                        <a class="block rounded-lg px-3 py-2 {{ request()->is('faq') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}" href="{{ route('pages.faq') }}">FAQ</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 sm:px-6 pb-16">
    @if(session('status') || session('success'))
        <div class="glass mt-6 rounded-2xl border-emerald-400/30 px-4 py-3 text-sm text-emerald-200" role="status">{{ session('status') ?? session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="glass mt-6 rounded-2xl border-rose-400/30 px-4 py-3 text-sm text-rose-200" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>

<footer class="border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-12 grid gap-10 md:grid-cols-4">
        <div>
            <p class="font-extrabold text-lg">Talent<span class="text-cyan-300">Flow</span></p>
            <p class="mt-2 text-sm text-slate-400">A premium recruitment platform for candidates and hiring teams. Find work you love, hire people you trust.</p>
            <p class="mt-4 text-xs text-slate-500">Demo data is fictional. See <a class="underline" href="{{ route('pages.privacy') }}">Privacy</a>.</p>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-slate-200">Job seekers</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                <li><a class="hover:text-white" href="{{ route('jobs.index') }}">Browse jobs</a></li>
                <li><a class="hover:text-white" href="{{ route('jobs.remote') }}">Remote jobs</a></li>
                <li><a class="hover:text-white" href="{{ route('jobs.internships') }}">Internships</a></li>
                <li><a class="hover:text-white" href="{{ route('resources.index') }}">Career resources</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-slate-200">Employers</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                <li><a class="hover:text-white" href="{{ route('pages.employers') }}">Recruitment solutions</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.pricing') }}">Pricing</a></li>
                <li><a class="hover:text-white" href="{{ route('register', ['role' => 'employer']) }}">Post a job</a></li>
                <li><a class="hover:text-white" href="{{ route('companies.index') }}">Companies</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-slate-200">Company</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                <li><a class="hover:text-white" href="{{ route('pages.about') }}">About us</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.contact') }}">Contact</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.faq') }}">FAQ</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.terms') }}">Terms</a></li>
                <li><a class="hover:text-white" href="{{ route('pages.privacy') }}">Privacy policy</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/5 py-5 text-center text-xs text-slate-500">© {{ date('Y') }} TalentFlow. Built for demonstration on Ubuntu with Laravel, MariaDB and Vite.</div>
</footer>
@stack('scripts')
</body>
</html>

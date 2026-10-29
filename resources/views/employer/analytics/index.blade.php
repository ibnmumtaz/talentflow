@extends('layouts.dashboard', ['heading' => 'Recruitment analytics', 'subheading' => 'Real data for your companies only.'])
@section('dashboard')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Total applications" :value="$totalApps" />
    <x-stat-card label="Job views" :value="$totalViews" />
    <x-stat-card label="Hired" :value="$hired" />
    <x-stat-card label="Interviews" :value="$interviews" />
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Applications by stage</h2>
        <canvas class="mt-4" height="220" data-chart='{"type":"doughnut","data":{"labels":{!! json_encode(array_keys($byStage->map(fn($v,$k)=>ucwords(str_replace("_"," ",$k)))->toArray())) !!},"datasets":[{"data":{!! json_encode(array_values($byStage->toArray())) !!},"backgroundColor":["#38bdf8","#818cf8","#a78bfa","#22d3ee","#60a5fa","#fbbf24","#34d399","#fb7185","#94a3b8"]}]},"options":{"plugins":{"legend":{"position":"bottom"}}}}'></canvas>
    </div>
    <div class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Applications — last 30 days</h2>
        <canvas class="mt-4" height="220" data-chart='{"type":"line","data":{"labels":{!! json_encode($byDay->pluck("d")->toArray()) !!},"datasets":[{"label":"Applications","data":{!! json_encode($byDay->pluck("total")->toArray()) !!},"borderColor":"#22d3ee","backgroundColor":"rgba(34,211,238,0.15)","fill":true,"tension":0.3}]},"options":{"scales":{"y":{"beginAtZero":true}}}}'></canvas>
    </div>
</div>
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[560px]"><thead><tr><th>Job</th><th>Status</th><th>Applications</th><th>Views</th><th>Conversion</th></tr></thead><tbody>
@foreach($jobs as $j)<tr><td class="font-medium">{{ $j->title }}</td><td><x-badge :status="$j->status" /></td><td>{{ $j->applications_count }}</td><td>{{ $j->views_count }}</td><td>{{ $j->views_count ? round($j->applications_count / $j->views_count * 100, 1).'%' : '—' }}</td></tr>@endforeach
</tbody></table></div>
@endsection

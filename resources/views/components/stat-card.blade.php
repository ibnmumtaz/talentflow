@props(['label', 'value', 'hint' => null, 'tone' => 'violet'])
<div class="glass rounded-2xl p-5">
    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">{{ $value }}</p>
    @if($hint)<p class="mt-1 text-sm text-slate-400">{{ $hint }}</p>@endif
</div>

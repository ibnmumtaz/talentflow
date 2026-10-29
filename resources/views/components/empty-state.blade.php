@props(['title' => 'Nothing here yet', 'message' => 'There are no records to show right now.', 'action' => null])
<div class="glass rounded-2xl p-10 text-center">
    <p class="text-4xl" aria-hidden="true">📭</p>
    <h3 class="mt-3 font-semibold">{{ $title }}</h3>
    <p class="mt-1 text-sm text-slate-400">{{ $message }}</p>
    @if($action)<div class="mt-4">{{ $action }}</div>@endif
</div>

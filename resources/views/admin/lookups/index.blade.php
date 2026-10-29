@extends('layouts.dashboard', ['heading' => 'Categories, industries, skills & locations', 'subheading' => 'Lookups used by job forms and search filters.'])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-2">
    @foreach([['industries', $industries, 'industry'], ['categories', $categories, 'category'], ['locations', $locations, 'location']] as [$label, $items, $type])
        <section class="glass rounded-2xl p-5">
            <h2 class="font-semibold capitalize">{{ $label }}</h2>
            <ul class="mt-3 flex flex-wrap gap-2 text-sm">@foreach($items as $item)<li class="flex items-center gap-2 rounded-full bg-white/5 px-3 py-1">{{ $item->name }}<form method="POST" action="{{ route('admin.lookups.destroy', ['type' => $type, 'id' => $item->id]) }}" data-confirm="Delete this lookup? Jobs using it keep a null reference.">@csrf @method('DELETE')<button class="text-rose-300">×</button></form></li>@endforeach</ul>
            <form method="POST" action="{{ route('admin.lookups.store', $type) }}" class="mt-4 flex gap-2">@csrf<input name="name" required placeholder="New {{ $type }} name" class="input-glass flex-1"><button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add</button></form>
        </section>
    @endforeach
    <section class="glass rounded-2xl p-5 lg:col-span-2">
        <h2 class="font-semibold">Skills</h2>
        <ul class="mt-3 flex flex-wrap gap-2 text-sm">@foreach($skills as $s)<li class="flex items-center gap-2 rounded-full bg-white/5 px-3 py-1">{{ $s->name }}<form method="POST" action="{{ route('admin.lookups.destroy', ['type' => 'skill', 'id' => $s->id]) }}" data-confirm="Delete this skill?">@csrf @method('DELETE')<button class="text-rose-300">×</button></form></li>@endforeach</ul>
        <form method="POST" action="{{ route('admin.lookups.store', 'skill') }}" class="mt-4 flex gap-2">@csrf<input name="name" required placeholder="New skill" class="input-glass flex-1"><button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add skill</button></form>
        <div class="mt-3">{{ $skills->links() }}</div>
    </section>
</div>
@endsection

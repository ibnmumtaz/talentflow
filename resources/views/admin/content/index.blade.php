@extends('layouts.dashboard', ['heading' => 'Content management', 'subheading' => 'Articles, FAQs, testimonials and newsletter subscribers.'])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-2">
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">New article</h2>
        <form method="POST" action="{{ route('admin.content.articles.store') }}" class="mt-3 space-y-2">@csrf
            <input name="title" required placeholder="Title" class="input-glass"><input name="category" required placeholder="Category" class="input-glass"><textarea name="excerpt" rows="2" placeholder="Excerpt" class="input-glass"></textarea><textarea name="body" required rows="4" placeholder="Body" class="input-glass"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" checked> Published</label>
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Create article</button>
        </form>
        <ul class="mt-4 space-y-2 text-sm">@foreach($articles as $a)<li class="flex justify-between gap-2"><span>{{ $a->title }} ({{ $a->is_published ? 'published' : 'draft' }})</span><form method="POST" action="{{ route('admin.content.articles.destroy', $a) }}" data-confirm="Delete this article?">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></li>@endforeach</ul>
        <div class="mt-3">{{ $articles->links() }}</div>
    </section>
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">New FAQ</h2>
        <form method="POST" action="{{ route('admin.content.faqs.store') }}" class="mt-3 space-y-2">@csrf
            <input name="question" required placeholder="Question" class="input-glass"><textarea name="answer" required rows="3" placeholder="Answer" class="input-glass"></textarea>
            <select name="audience" class="input-glass"><option value="all">Everyone</option><option value="candidate">Candidates</option><option value="employer">Employers</option></select>
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Add FAQ</button>
        </form>
        <ul class="mt-4 space-y-2 text-sm">@foreach($faqs as $f)<li class="flex justify-between gap-2"><span>{{ $f->question }}</span><form method="POST" action="{{ route('admin.content.faqs.destroy', $f) }}" data-confirm="Delete?">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></li>@endforeach</ul>
        <h2 class="mt-6 font-semibold">New testimonial</h2>
        <form method="POST" action="{{ route('admin.content.testimonials.store') }}" class="mt-3 space-y-2">@csrf
            <input name="name" required placeholder="Name" class="input-glass"><input name="role_title" placeholder="Role" class="input-glass"><input name="company" placeholder="Company" class="input-glass"><textarea name="quote" required rows="2" placeholder="Quote" class="input-glass"></textarea><input type="number" min="1" max="5" name="rating" value="5" class="input-glass">
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Add testimonial</button>
        </form>
        <ul class="mt-4 space-y-2 text-sm">@foreach($testimonials as $t)<li class="flex justify-between gap-2"><span>{{ $t->name }} — {{ $t->quote }}</span><form method="POST" action="{{ route('admin.content.testimonials.destroy', $t) }}" data-confirm="Delete?">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></li>@endforeach</ul>
    </section>
</div>
<section class="glass mt-6 rounded-2xl p-5">
    <h2 class="font-semibold">Newsletter subscribers ({{ $subscribers->total() }})</h2>
    <ul class="mt-3 space-y-1 text-sm">@foreach($subscribers as $s)<li>{{ $s->email }} @if($s->name)— {{ $s->name }}@endif <span class="text-slate-400">{{ $s->created_at->format('d M Y') }}</span></li>@endforeach</ul>
    <div class="mt-3">{{ $subscribers->links() }}</div>
</section>
@endsection

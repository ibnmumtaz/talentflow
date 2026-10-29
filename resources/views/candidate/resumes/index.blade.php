@extends('layouts.dashboard', ['heading' => 'CVs / Resumes', 'subheading' => 'PDF and DOCX files, max 5 MB, stored privately.'])
@section('dashboard')
<form method="POST" action="{{ route('candidate.resumes.store') }}" enctype="multipart/form-data" class="glass rounded-2xl p-6">
    @csrf
    <h2 class="font-semibold">Upload a new CV</h2>
    <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
        <div><label class="text-sm" for="title">CV title</label><input id="title" name="title" required placeholder="e.g. CV — Laravel Developer" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="resume">File</label><input id="resume" type="file" name="resume" required accept=".pdf,.doc,.docx" class="input-glass mt-1"></div>
        <div class="flex items-end"><button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Upload CV</button></div>
    </div>
    <p class="mt-2 text-xs text-slate-500">Only PDF, DOC and DOCX. Files are stored on a private disk and never linked publicly.</p>
</form>

@if($resumes->isEmpty())<div class="mt-6"><x-empty-state title="No CVs uploaded" message="Upload your first CV above to apply faster." /></div>
@else
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[560px]">
    <thead><tr><th>Title</th><th>File</th><th>Size</th><th>Primary</th><th>Uploaded</th><th>Actions</th></tr></thead>
    <tbody>@foreach($resumes as $r)<tr>
        <td class="font-medium">{{ $r->title }}</td><td class="text-sm text-slate-400">{{ $r->original_filename }}</td><td class="text-sm">{{ number_format($r->size_bytes / 1024) }} KB</td>
        <td>@if($r->is_primary)<span class="text-emerald-300 text-sm">Primary</span>@else<form method="POST" action="{{ route('candidate.resumes.primary', $r) }}">@csrf<button class="text-sm text-cyan-300 hover:underline">Make primary</button></form>@endif</td>
        <td class="text-sm text-slate-400">{{ $r->created_at->format('d M Y') }}</td>
        <td class="whitespace-nowrap"><a class="text-sm text-cyan-300 hover:underline" href="{{ route('candidate.resumes.download', $r) }}">Download</a>
        <form method="POST" action="{{ route('candidate.resumes.destroy', $r) }}" class="inline" data-confirm="Delete this CV? Applications that used it keep their status, but the file will be gone.">@csrf @method('DELETE')<button class="ml-3 text-sm text-rose-300">Delete</button></form></td>
    </tr>@endforeach</tbody>
</table></div>
<div class="mt-4">{{ $resumes->links() }}</div>
@endif
@endsection

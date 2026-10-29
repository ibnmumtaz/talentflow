<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        $resumes = $request->user()->resumes()->latest()->paginate(10);

        return view('candidate.resumes.index', compact('resumes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);
        $file = $request->file('resume');
        $stored = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('resumes/'.$request->user()->id, $stored, 'private');
        $resume = $request->user()->resumes()->create([
            'title' => $request->title,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $stored,
            'disk' => 'private',
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'is_primary' => ! $request->user()->resumes()->where('is_primary', true)->exists(),
        ]);

        return back()->with('status', 'CV uploaded securely.');
    }

    public function setPrimary(Request $request, CandidateResume $resume)
    {
        abort_unless($resume->user_id === $request->user()->id, 403);
        $request->user()->resumes()->update(['is_primary' => false]);
        $resume->update(['is_primary' => true]);

        return back()->with('status', 'Primary CV updated.');
    }

    public function download(Request $request, CandidateResume $resume)
    {
        $request->user()->can('view', $resume) || abort(403);
        if (! $resume->existsOnDisk()) {
            abort(404, 'This file is no longer available.');
        }

        return Storage::disk($resume->disk)->download($resume->path, $resume->original_filename);
    }

    public function destroy(Request $request, CandidateResume $resume)
    {
        abort_unless($resume->user_id === $request->user()->id, 403);
        Storage::disk($resume->disk)->delete($resume->path);
        $resume->delete();

        return back()->with('status', 'CV deleted.');
    }
}

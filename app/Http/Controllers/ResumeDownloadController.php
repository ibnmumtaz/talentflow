<?php

namespace App\Http\Controllers;

use App\Models\CandidateResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResumeDownloadController extends Controller
{
    public function download(Request $request, CandidateResume $resume)
    {
        $this->authorize('view', $resume);
        if (! $resume->existsOnDisk()) {
            abort(404, 'This file is no longer available.');
        }

        return Storage::disk($resume->disk)->download($resume->path, $resume->original_filename);
    }
}

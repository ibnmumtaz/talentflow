<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $interviews = Interview::whereHas('application', fn ($q) => $q->where('user_id', $request->user()->id))->with(['job.company', 'application'])->orderBy('scheduled_at')->paginate(12);

        return view('candidate.interviews.index', compact('interviews'));
    }

    public function respond(Request $request, Interview $interview)
    {
        abort_unless($interview->application->user_id === $request->user()->id, 403);
        $request->validate(['response' => ['required', 'in:accepted,declined']]);
        $interview->update(['candidate_response' => $request->response]);

        return back()->with('status', 'Interview response recorded.');
    }
}

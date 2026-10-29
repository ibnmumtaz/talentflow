<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\InterviewFeedback;
use App\Notifications\InterviewScheduledNotification;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $companyIds = $request->user()->activeCompanies()->pluck('companies.id');
        $interviews = Interview::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))->with(['job', 'application.user'])->orderBy('scheduled_at')->paginate(15);

        return view('employer.interviews.index', compact('interviews'));
    }

    public function store(Request $request, Application $application)
    {
        $this->authorize('update', $application);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:online,phone,in_person'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'timezone' => ['required', 'string', 'max:60'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        if (Interview::where('application_id', $application->id)->where('scheduled_at', $data['scheduled_at'])->where('status', 'scheduled')->exists()) {
            return back()->withErrors(['scheduled_at' => 'An interview is already scheduled at this time for this application.']);
        }
        $interview = Interview::create($data + ['application_id' => $application->id, 'job_id' => $application->job_id, 'scheduled_by_id' => $request->user()->id, 'status' => 'scheduled']);
        $interview->participants()->attach($request->user()->id, ['role' => 'interviewer']);
        if (Application::canTransition($application->status, 'interview_scheduled')) {
            ApplicationService::transition($application->fresh(), 'interview_scheduled', $request->user(), 'Interview scheduled.');
        }
        if ($application->user->notifyPreference('interview', true)) {
            $application->user->notify(new InterviewScheduledNotification($interview));
        }

        return back()->with('status', 'Interview scheduled and the candidate has been notified.');
    }

    public function update(Request $request, Interview $interview)
    {
        $this->authorize('update', $interview);
        $data = $request->validate(['status' => ['required', 'in:scheduled,completed,cancelled'], 'candidate_response' => ['nullable', 'in:accepted,declined']]);
        $interview->update($data);
        if ($data['status'] === 'completed' && Application::canTransition($interview->application->status, 'interview_completed')) {
            ApplicationService::transition($interview->application, 'interview_completed', $request->user(), 'Interview completed.');
        }

        return back()->with('status', 'Interview updated.');
    }

    public function feedback(Request $request, Interview $interview)
    {
        $this->authorize('update', $interview);
        $data = $request->validate(['rating' => ['nullable', 'integer', 'min:1', 'max:5'], 'feedback' => ['required', 'string', 'max:3000'], 'recommendation' => ['nullable', 'in:hire,no_hire,maybe']]);
        InterviewFeedback::create($data + ['interview_id' => $interview->id, 'author_id' => $request->user()->id]);

        return back()->with('status', 'Interview feedback saved.');
    }
}

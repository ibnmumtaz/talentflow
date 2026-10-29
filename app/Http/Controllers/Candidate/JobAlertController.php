<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\JobAlert;
use App\Models\JobCategory;
use App\Models\Location;
use Illuminate\Http\Request;

class JobAlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = $request->user()->jobAlerts()->with(['category', 'location'])->latest()->paginate(12);

        return view('candidate.alerts.index', ['alerts' => $alerts, 'categories' => JobCategory::orderBy('name')->get(), 'locations' => Location::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'keywords' => ['nullable', 'string', 'max:255'], 'category_id' => ['nullable', 'exists:job_categories,id'], 'location_id' => ['nullable', 'exists:locations,id'], 'workplace_type' => ['nullable', 'in:remote,hybrid,onsite'], 'employment_type' => ['nullable', 'in:full_time,part_time,contract,internship,freelance,temporary'], 'salary_min' => ['nullable', 'numeric', 'min:0'], 'frequency' => ['required', 'in:instant,daily,weekly']]);
        $request->user()->jobAlerts()->create($data + ['is_active' => true]);

        return back()->with('status', 'Job alert created.');
    }

    public function update(Request $request, JobAlert $alert)
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Job alert updated.');
    }

    public function destroy(Request $request, JobAlert $alert)
    {
        abort_unless($alert->user_id === $request->user()->id, 403);
        $alert->delete();

        return back()->with('status', 'Job alert deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['reportable_type' => ['required', 'in:job,company'], 'reportable_id' => ['required', 'integer'], 'reason' => ['required', 'in:spam,inappropriate,misleading,scam,other'], 'details' => ['nullable', 'string', 'max:2000']]);
        $model = $data['reportable_type'] === 'job' ? Job::class : Company::class;
        $model::findOrFail($data['reportable_id']);
        Report::create(['reporter_id' => $request->user()?->id, 'reportable_type' => $model, 'reportable_id' => $data['reportable_id'], 'reason' => $data['reason'], 'details' => $data['details'] ?? null, 'status' => 'open']);

        return back()->with('status', 'Thank you — your report has been submitted for review.');
    }
}

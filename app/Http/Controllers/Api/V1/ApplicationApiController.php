<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class ApplicationApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->isCandidate()
            ? $user->applications()->with(['job', 'user'])
            : Application::whereHas('job.company.memberships', fn ($q) => $q->where('user_id', $user->id)->where('status', 'active'))->with(['job', 'user']);

        return ApplicationResource::collection($query->latest()->paginate(15));
    }

    public function show(Request $request, Application $application)
    {
        $request->user()->can('view', $application) || abort(403);

        return new ApplicationResource($application->load(['job', 'user']));
    }

    public function transition(Request $request, Application $application)
    {
        $request->user()->can('update', $application) || abort(403);
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', Application::STAGES)], 'note' => ['nullable', 'string', 'max:1000']]);
        try {
            $application = ApplicationService::transition($application, $data['status'], $request->user(), $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new ApplicationResource($application->load(['job', 'user']));
    }
}

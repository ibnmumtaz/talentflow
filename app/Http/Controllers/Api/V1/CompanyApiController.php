<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::active()->with(['industry', 'location']);
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        return CompanyResource::collection($query->orderBy('name')->paginate(15));
    }

    public function show(Company $company)
    {
        if (! $company->is_active) {
            abort(404);
        }

        return new CompanyResource($company->load(['industry', 'location']));
    }
}

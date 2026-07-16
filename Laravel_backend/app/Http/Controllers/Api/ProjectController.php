<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::withCount('donations')->paginate(15);

        return response()->json($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json(
            $project->load('employees', 'gallery', 'news')
                ->append([]) // reserved for future computed attributes
        );
    }

    public function stats(Project $project): JsonResponse
    {
        return response()->json([
            'total_raised' => $project->totalRaised(),
            'budget' => $project->budget,
            'employees_count' => $project->employees()->count(),
            'news_count' => $project->news()->count(),
        ]);
    }
}

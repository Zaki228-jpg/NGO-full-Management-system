<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function create(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            return Project::create($data);
        });
    }

    public function assignEmployee(Project $project, int $employeeId, string $role = 'member'): void
    {
        $project->employees()->syncWithoutDetaching([
            $employeeId => ['role' => $role],
        ]);
    }

    public function budgetRemaining(Project $project): float
    {
        return (float) $project->budget - $project->totalRaised();
    }
}

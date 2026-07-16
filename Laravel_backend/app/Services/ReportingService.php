<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Donation;
use App\Models\Employee;

class ReportingService
{
    public function donationSummary(?string $from = null, ?string $to = null): array
    {
        $query = Donation::completed();

        if ($from) {
            $query->where('donated_at', '>=', $from);
        }

        if ($to) {
            $query->where('donated_at', '<=', $to);
        }

        return [
            'total_amount' => (float) $query->sum('amount'),
            'total_count' => $query->count(),
        ];
    }

    public function projectPerformance(Project $project): array
    {
        return [
            'title' => $project->title,
            'budget' => (float) $project->budget,
            'raised' => $project->totalRaised(),
            'employees' => $project->employees()->count(),
        ];
    }

    public function staffCountByDepartment(): array
    {
        return Employee::active()
            ->selectRaw('department_id, count(*) as total')
            ->groupBy('department_id')
            ->with('department:id,name')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->department?->name ?? 'Unassigned' => $row->total])
            ->toArray();
    }
}

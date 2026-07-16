<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Models\Donor;
use App\Models\Employee;
use App\Policies\ProjectPolicy;
use App\Policies\DonorPolicy;
use App\Policies\EmployeePolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Donor::class, DonorPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
    }
}

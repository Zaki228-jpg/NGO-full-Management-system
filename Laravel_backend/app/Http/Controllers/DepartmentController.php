<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('employees')->orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Department::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string'],
        ]);

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'description' => ['nullable', 'string'],
            'head_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('delete', $department);

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department removed successfully.');
    }
}

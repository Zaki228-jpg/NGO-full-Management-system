<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with('department')->active()->paginate(15);

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        $departments = Department::orderBy('name')->get();

        return view('employees.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'hire_date' => ['required', 'date'],
            'salary' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['status'] = 'active';

        $employee = Employee::create($validated);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee): View
    {
        $this->authorize('view', $employee);

        $employee->load(['department', 'projects']);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        $departments = Department::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,on_leave,terminated'],
        ]);

        $employee->update($validated);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee record removed.');
    }
}

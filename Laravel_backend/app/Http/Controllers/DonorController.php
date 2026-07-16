<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonorController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Donor::class);

        $donors = Donor::withCount('donations')->latest()->paginate(15);

        return view('donors.index', compact('donors'));
    }

    public function create(): View
    {
        $this->authorize('create', Donor::class);

        return view('donors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Donor::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:donors,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:individual,organization,government'],
            'is_recurring' => ['boolean'],
        ]);

        $donor = Donor::create($validated);

        return redirect()
            ->route('donors.show', $donor)
            ->with('success', 'Donor added successfully.');
    }

    public function show(Donor $donor): View
    {
        $this->authorize('view', $donor);

        $donor->load('donations.project');

        return view('donors.show', compact('donor'));
    }

    public function edit(Donor $donor): View
    {
        $this->authorize('update', $donor);

        return view('donors.edit', compact('donor'));
    }

    public function update(Request $request, Donor $donor): RedirectResponse
    {
        $this->authorize('update', $donor);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:donors,email,' . $donor->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:individual,organization,government'],
            'is_recurring' => ['boolean'],
        ]);

        $donor->update($validated);

        return redirect()
            ->route('donors.show', $donor)
            ->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        $this->authorize('delete', $donor);

        $donor->delete();

        return redirect()
            ->route('donors.index')
            ->with('success', 'Donor removed successfully.');
    }
}

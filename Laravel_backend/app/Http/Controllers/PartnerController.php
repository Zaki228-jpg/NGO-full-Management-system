<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(): View
    {
        $partners = Partner::orderBy('name')->get();

        return view('partners.index', compact('partners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Partner::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'type' => ['required', 'in:donor_agency,government,ngo,private_sector'],
        ]);

        Partner::create($validated);

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partner added successfully.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $this->authorize('delete', $partner);

        $partner->delete();

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partner removed successfully.');
    }
}

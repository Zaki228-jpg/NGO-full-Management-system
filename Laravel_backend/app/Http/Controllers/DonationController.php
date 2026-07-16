<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Project;
use App\Services\DonationService;
use App\Exceptions\DonationPaymentException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(protected DonationService $donationService)
    {
    }

    public function index(): View
    {
        $donations = Donation::with(['donor', 'project'])->latest('donated_at')->paginate(20);

        return view('donations.index', compact('donations'));
    }

    public function create(): View
    {
        $donors = Donor::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();

        return view('donations.create', compact('donors', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_id' => ['required', 'exists:donors,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'method' => ['required', 'in:bank_transfer,card,cash,mobile_money'],
        ]);

        try {
            $donation = $this->donationService->process($validated);
        } catch (DonationPaymentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('donations.show', $donation)
            ->with('success', 'Donation recorded successfully.');
    }

    public function show(Donation $donation): View
    {
        $donation->load(['donor', 'project']);

        return view('donations.show', compact('donation'));
    }

    public function destroy(Donation $donation): RedirectResponse
    {
        $this->authorize('delete', $donation);

        $donation->delete();

        return redirect()
            ->route('donations.index')
            ->with('success', 'Donation record deleted.');
    }
}

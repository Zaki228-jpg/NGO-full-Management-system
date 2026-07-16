<?php

namespace App\Services;

use App\Models\Donation;
use App\Exceptions\DonationPaymentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationService
{
    /**
     * Process and record a new donation.
     *
     * @throws DonationPaymentException
     */
    public function process(array $data): Donation
    {
        if ($data['amount'] <= 0) {
            throw DonationPaymentException::invalidAmount();
        }

        return DB::transaction(function () use ($data) {
            return Donation::create([
                ...$data,
                'reference' => 'DON-' . strtoupper(Str::random(10)),
                'status' => 'completed',
                'donated_at' => now(),
            ]);
        });
    }

    public function refund(Donation $donation): Donation
    {
        $donation->update(['status' => 'refunded']);

        return $donation;
    }

    public function totalForProject(int $projectId): float
    {
        return (float) Donation::where('project_id', $projectId)
            ->completed()
            ->sum('amount');
    }
}

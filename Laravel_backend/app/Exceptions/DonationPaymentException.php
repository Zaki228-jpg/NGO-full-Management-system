<?php

namespace App\Exceptions;

use Exception;

class DonationPaymentException extends Exception
{
    public static function gatewayError(string $reason): static
    {
        return new static("The donation could not be processed: {$reason}");
    }

    public static function invalidAmount(): static
    {
        return new static('The donation amount is invalid.');
    }
}

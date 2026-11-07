<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationException extends Exception
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 422,
    ) {
        parent::__construct($message);
    }

    public static function notFound(string $message = 'Transaction could not be verified.'): self
    {
        return new self($message, 404);
    }

    public static function providerUnavailable(string $message = 'Payment provider is temporarily unavailable.'): self
    {
        return new self($message, 502);
    }

    public static function failed(string $message = 'Unable to complete verification.'): self
    {
        return new self($message, 504);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}

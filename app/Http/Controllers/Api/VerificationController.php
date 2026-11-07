<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVerificationRequest;
use App\Services\VerificationService;

class VerificationController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService,
    ) {}

    public function store(StoreVerificationRequest $request)
    {
        $verification = $request->toDto();
        $result = $this->verificationService->verify($verification);

        return response()->json([
            'message' => 'Verification submitted',
            'data' => $result,
        ], 201);
    }
}

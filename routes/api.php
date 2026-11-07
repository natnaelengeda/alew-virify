<?php

use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.bearer', 'throttle:verifications', 'security.headers'])
    ->post('/verifications', [VerificationController::class, 'store']);

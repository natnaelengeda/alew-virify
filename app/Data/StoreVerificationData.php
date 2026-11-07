<?php

namespace App\Data;

use App\Enums\VerificationType;

readonly class StoreVerificationData
{
    public function __construct(
        public VerificationType $type,
        public string $transactionId,
    ) {}
}

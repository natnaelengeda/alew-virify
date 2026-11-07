<?php

namespace App\Http\Requests;

use App\Data\StoreVerificationData;
use App\Enums\VerificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(VerificationType::class)],
            'transactionId' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): StoreVerificationData
    {
        return new StoreVerificationData(
            type: $this->enum('type', VerificationType::class),
            transactionId: $this->string('transactionId'),
        );
    }
}

<?php

namespace App\Services\Telebirr;

use App\Exceptions\VerificationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TelebirrClient
{
    public function __construct(
        private readonly TelebirrReceiptParser $parser,
    ) {}

    /**
     * @return array{creditParty: ?string, debitParty: ?string, date: ?string, amount: ?string}
     */
    public function getReceiptDetails(string $transactionId): array
    {
        $transactionId = $this->normalizeTransactionId($transactionId);

        try {
            $response = $this->request()->get($this->receiptPath($transactionId));
        } catch (ConnectionException) {
            throw VerificationException::failed();
        }

        if ($response->failed()) {
            throw VerificationException::providerUnavailable(
                'Telebirr receipt lookup failed. Try again later.',
            );
        }

        $html = $response->body();

        if ($html === '') {
            throw VerificationException::notFound('Telebirr returned an empty receipt page.');
        }

        if ($this->looksLikeProviderOutage($html)) {
            throw VerificationException::providerUnavailable(
                'Telebirr receipt service is temporarily unavailable.',
            );
        }

        if ($this->looksLikeErrorPage($html)) {
            throw VerificationException::notFound(
                'Telebirr could not find a receipt for that transaction ID.',
            );
        }

        return $this->parser->parse($html);
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.telebirr.base_url'), '/'))
            ->timeout((int) config('services.telebirr.timeout', 20))
            ->withHeaders([
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'User-Agent' => (string) config('services.telebirr.user_agent'),
            ]);
    }

    private function receiptPath(string $transactionId): string
    {
        return '/receipt/'.rawurlencode($transactionId);
    }

    private function normalizeTransactionId(string $transactionId): string
    {
        $transactionId = trim($transactionId);

        if (filter_var($transactionId, FILTER_VALIDATE_URL)) {
            $path = parse_url($transactionId, PHP_URL_PATH);
            $segment = $path ? basename($path) : $transactionId;

            return $segment !== '' && $segment !== '/' ? $segment : $transactionId;
        }

        return $transactionId;
    }

    private function looksLikeProviderOutage(string $html): bool
    {
        $normalized = strtolower($html);

        return str_contains($normalized, 'gateway time-out')
            || str_contains($normalized, '502 bad gateway')
            || str_contains($normalized, '503 service unavailable');
    }

    private function looksLikeErrorPage(string $html): bool
    {
        $needles = [
            'receipt not found',
            'invalid receipt',
            'transaction not found',
            'does not exist',
        ];

        $normalized = strtolower(preg_replace('/\s+/', ' ', strip_tags($html)) ?? $html);

        foreach ($needles as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return ! str_contains(strtolower($html), 'telebirr transaction information')
            && ! str_contains(strtolower($html), 'telebirr receipt');
    }
}

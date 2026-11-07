<?php

namespace App\Services\Cbe;

use App\Exceptions\VerificationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CbeClient
{
    /**
     * @return array<string, mixed>
     */
    public function getTransactionDetail(string $receiptId): array
    {
        $receiptId = $this->normalizeReceiptId($receiptId);

        try {
            $response = $this->request()->get($this->transactionDetailPath($receiptId));
        } catch (ConnectionException) {
            throw VerificationException::failed();
        }

        if ($response->failed()) {
            throw VerificationException::providerUnavailable(
                'CBE transaction lookup failed. Try again later.',
            );
        }

        $payload = $response->json();

        if (! is_array($payload) || empty($payload['id'])) {
            $message = is_array($payload) && isset($payload['detail'])
                ? (string) $payload['detail']
                : 'CBE could not find a transaction for that receipt ID.';

            throw VerificationException::notFound($message);
        }

        return $payload;
    }

    private function request(): PendingRequest
    {
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.9,am-ET;q=0.8,am;q=0.7',
            'Connection' => 'keep-alive',
            'DNT' => '1',
            'Origin' => 'https://mbreciept.cbe.com.et',
            'Referer' => 'https://mbreciept.cbe.com.et/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1',
            'sec-ch-ua' => '"Not=A?Brand";v="99", "Google Chrome";v="151", "Chromium";v="151"',
            'sec-ch-ua-mobile' => '?1',
            'sec-ch-ua-platform' => '"iOS"',
            'x-app-id' => (string) config('services.cbe.app_id'),
            'x-app-version' => (string) config('services.cbe.app_version'),
        ];

        $cookie = config('services.cbe.cookie');

        if (is_string($cookie) && $cookie !== '') {
            $headers['Cookie'] = $cookie;
        }

        return Http::baseUrl(rtrim((string) config('services.cbe.base_url'), '/'))
            ->timeout((int) config('services.cbe.timeout', 15))
            ->withHeaders($headers);
    }

    private function transactionDetailPath(string $receiptId): string
    {
        return '/api/v1/transactions/public/transaction-detail/'.rawurlencode($receiptId);
    }

    private function normalizeReceiptId(string $transactionId): string
    {
        $transactionId = trim($transactionId);

        if (filter_var($transactionId, FILTER_VALIDATE_URL)) {
            $path = parse_url($transactionId, PHP_URL_PATH);
            $segment = $path ? basename($path) : $transactionId;

            return $segment !== '' && $segment !== '/' ? $segment : $transactionId;
        }

        return $transactionId;
    }
}

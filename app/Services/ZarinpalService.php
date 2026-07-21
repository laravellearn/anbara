<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZarinpalService
{
    private string $merchantId;
    private bool   $sandbox;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.zarinpal.merchant_id', '');
        $this->sandbox    = config('services.zarinpal.sandbox', false);
        $this->baseUrl    = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/'
            : 'https://api.zarinpal.com/pg/v4/payment/';
    }

    /**
     * ایجاد درخواست پرداخت و دریافت Authority
     * @return array{authority: string, payment_url: string}|null
     */
    public function request(int $amountRial, string $description, string $callbackUrl, string $mobile = ''): ?array
    {
        try {
            $response = Http::timeout(15)->post($this->baseUrl . 'request.json', [
                'merchant_id'  => $this->merchantId,
                'amount'       => $amountRial,
                'description'  => $description,
                'callback_url' => $callbackUrl,
                'metadata'     => ['mobile' => $mobile],
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['data']['code'] ?? null) === 100) {
                $authority  = $data['data']['authority'];
                $gatewayUrl = $this->sandbox
                    ? "https://sandbox.zarinpal.com/pg/StartPay/{$authority}"
                    : "https://www.zarinpal.com/pg/StartPay/{$authority}";

                return ['authority' => $authority, 'payment_url' => $gatewayUrl];
            }

            Log::error('Zarinpal request failed', ['response' => $data]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Zarinpal request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * تأیید پرداخت پس از بازگشت از درگاه
     * @return array{ref_id: string, card_pan: string}|null
     */
    public function verify(string $authority, int $amountRial): ?array
    {
        try {
            $response = Http::timeout(15)->post($this->baseUrl . 'verify.json', [
                'merchant_id' => $this->merchantId,
                'amount'      => $amountRial,
                'authority'   => $authority,
            ]);

            $data = $response->json();
            $code = $data['data']['code'] ?? null;

            // 100 = موفق، 101 = قبلاً تأیید شده
            if ($response->successful() && in_array($code, [100, 101])) {
                return [
                    'ref_id'   => (string)($data['data']['ref_id'] ?? ''),
                    'card_pan' => $data['data']['card_pan'] ?? '',
                ];
            }

            Log::warning('Zarinpal verify failed', ['authority' => $authority, 'response' => $data]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Zarinpal verify exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}

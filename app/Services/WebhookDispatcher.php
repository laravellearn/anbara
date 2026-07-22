<?php

namespace App\Services;

use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    /**
     * ارسال رویداد outbound به endpoint‌های خارجی
     *
     * @param string $event   نام رویداد (مثلاً: invoice.registered, document.approved)
     * @param array  $payload داده‌های ارسالی
     */
    public function dispatch(string $event, array $payload, ?int $tenantId = null): void
    {
        // آدرس‌های webhook از تنظیمات
        $endpoints = $this->getEndpoints($event, $tenantId);

        foreach ($endpoints as $url) {
            $this->send($event, $url, $payload, $tenantId);
        }
    }

    private function send(string $event, string $url, array $payload, ?int $tenantId): void
    {
        $log = WebhookLog::create([
            'direction' => 'outgoing',
            'type'      => $event,
            'url'       => $url,
            'payload'   => array_merge(['event' => $event, 'timestamp' => now()->toIso8601String()], $payload),
            'status'    => 'sent',
            'tenant_id' => $tenantId,
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json', 'X-Anbara-Event' => $event])
                ->post($url, $log->payload);

            $log->update([
                'status'   => $response->successful() ? 'sent' : 'error',
                'response' => ['status_code' => $response->status(), 'body' => substr($response->body(), 0, 500)],
            ]);
        } catch (\Throwable $e) {
            $log->update(['status' => 'error', 'response' => ['error' => $e->getMessage()]]);
            Log::warning("Webhook dispatch failed [{$event}] → {$url}: " . $e->getMessage());
        }
    }

    /**
     * دریافت لیست endpoint‌ها برای یک رویداد
     * (در نسخه‌های بعدی از جدول webhook_endpoints می‌خواند)
     */
    private function getEndpoints(string $event, ?int $tenantId): array
    {
        $urls = config('services.webhooks.' . $event, []);

        if ($tenantId) {
            $setting = \App\Models\TenantSetting::where('tenant_id', $tenantId)
                ->where('key', 'webhook_url_' . str_replace('.', '_', $event))
                ->value('value');
            if ($setting) {
                $urls[] = $setting;
            }
        }

        return array_filter($urls);
    }
}

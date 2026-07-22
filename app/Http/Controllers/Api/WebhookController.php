<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhookLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * دریافت webhook ورودی از سیستم‌های خارجی (هلو، سپیدار، رافع و...)
     */
    public function receive(Request $request, string $type): JsonResponse
    {
        $allowedTypes = ['hello', 'sepidaar', 'rafeh', 'generic'];
        if (! in_array($type, $allowedTypes)) {
            return response()->json(['success' => false, 'message' => 'نوع webhook نامعتبر'], 422);
        }

        // ذخیره لاگ ورودی
        $log = WebhookLog::create([
            'direction'  => 'incoming',
            'type'       => $type,
            'url'        => $request->fullUrl(),
            'headers'    => $request->headers->all(),
            'payload'    => $request->all(),
            'ip_address' => $request->ip(),
            'status'     => 'received',
        ]);

        // پردازش بر اساس نوع سیستم
        try {
            match ($type) {
                'hello'   => $this->processHello($request->all(), $log),
                'sepidaar'=> $this->processSepidaar($request->all(), $log),
                default   => $log->update(['status' => 'processed', 'response' => ['message' => 'دریافت شد']]),
            };
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'response' => ['error' => $e->getMessage()]]);
            Log::error("Webhook ({$type}) processing error: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'دریافت شد', 'id' => $log->id]);
    }

    private function processHello(array $data, WebhookLog $log): void
    {
        // پردازش داده‌های نرم‌افزار هلو
        $log->update(['status' => 'processed', 'response' => ['source' => 'hello', 'items' => count($data)]]);
    }

    private function processSepidaar(array $data, WebhookLog $log): void
    {
        // پردازش داده‌های نرم‌افزار سپیدار
        $log->update(['status' => 'processed', 'response' => ['source' => 'sepidaar', 'items' => count($data)]]);
    }
}

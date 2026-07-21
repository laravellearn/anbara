<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\NotificationService;
use App\Services\PlanService;
use App\Services\TenantManager;
use App\Services\ZarinpalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private ZarinpalService $zarinpal,
        private TenantManager $manager,
        private PlanService $planService,
        private NotificationService $notificationService
    ) {}

    /**
     * شروع فرایند پرداخت — ایجاد درخواست به Zarinpal
     */
    public function initiate(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $plan   = Plan::findOrFail($request->plan_id);
        $tenant = $this->manager->requireTenant();
        $user   = auth()->user();

        if (!$plan->price || $plan->price <= 0) {
            return redirect()->back()->with('toast', [
                'message' => 'این پلن رایگان است و نیاز به پرداخت ندارد.',
                'type'    => 'info', 'title'   => 'پرداخت',
            ]);
        }

        $amountRial  = (int)($plan->price * 10);  // تبدیل تومان به ریال
        $callbackUrl = route('billing.payment.callback');
        $description = "اشتراک پلن {$plan->name} — {$tenant->name}";

        $result = $this->zarinpal->request($amountRial, $description, $callbackUrl, $user->mobile ?? '');

        if (!$result) {
            return redirect()->back()->with('toast', [
                'message' => 'خطا در اتصال به درگاه پرداخت. لطفاً مجدداً تلاش کنید.',
                'type'    => 'error', 'title'   => 'خطای پرداخت',
            ]);
        }

        // ذخیره Payment به عنوان pending
        Payment::create([
            'tenant_id'   => $tenant->id,
            'gateway'     => 'zarinpal',
            'authority'   => $result['authority'],
            'amount'      => $amountRial,
            'description' => $description,
            'status'      => Payment::STATUS_PENDING,
            'payer_mobile'=> $user->mobile ?? '',
            'gateway_response' => ['plan_id' => $plan->id],
        ]);

        // ذخیره plan_id در session برای callback
        session(['pending_plan_id' => $plan->id, 'pending_authority' => $result['authority']]);

        return redirect($result['payment_url']);
    }

    /**
     * callback از Zarinpal — تأیید یا رد پرداخت
     */
    public function callback(Request $request)
    {
        $authority = $request->input('Authority', session('pending_authority', ''));
        $status    = $request->input('Status', '');
        $planId    = session('pending_plan_id');

        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return redirect()->route('core.billing.plans')->with('toast', [
                'message' => 'پرداخت یافت نشد.', 'type' => 'error', 'title' => 'خطا',
            ]);
        }

        if ($status !== 'OK') {
            $payment->update(['status' => Payment::STATUS_CANCELED, 'gateway_response' => ['Status' => $status]]);
            return redirect()->route('core.billing.plans')->with('toast', [
                'message' => 'پرداخت توسط شما لغو شد.', 'type' => 'warning', 'title' => 'لغو پرداخت',
            ]);
        }

        // تأیید با Zarinpal
        $verification = $this->zarinpal->verify($authority, $payment->amount);

        if (!$verification) {
            $payment->update(['status' => Payment::STATUS_FAILED]);
            return redirect()->route('core.billing.plans')->with('toast', [
                'message' => 'تأیید پرداخت ناموفق بود.', 'type' => 'error', 'title' => 'خطای پرداخت',
            ]);
        }

        try {
            DB::transaction(function () use ($payment, $planId, $verification) {
                $plan   = Plan::findOrFail($planId);
                $tenant = $payment->tenant;

                // لغو اشتراک قبلی
                Subscription::where('tenant_id', $tenant->id)
                    ->where('status', 'active')
                    ->update(['status' => 'canceled']);

                // ایجاد اشتراک جدید
                $subscription = Subscription::create([
                    'tenant_id'  => $tenant->id,
                    'plan_id'    => $plan->id,
                    'starts_at'  => now(),
                    'ends_at'    => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
                    'status'     => 'active',
                    'auto_renew' => false,
                ]);

                // به‌روزرسانی وضعیت پرداخت
                $payment->update([
                    'status'           => Payment::STATUS_PAID,
                    'ref_id'           => $verification['ref_id'],
                    'subscription_id'  => $subscription->id,
                    'paid_at'          => now(),
                    'gateway_response' => $verification,
                ]);

                // ارسال اعلان
                $this->notificationService->sendToTenantAdmins(
                    $tenant->id,
                    \App\Models\AppNotification::TYPE_PAYMENT_SUCCESS,
                    'پرداخت موفق',
                    "اشتراک پلن {$plan->name} با موفقیت فعال شد. کد رهگیری: {$verification['ref_id']}"
                );
            });
        } catch (\Throwable $e) {
            Log::error('Payment callback transaction failed', ['error' => $e->getMessage()]);
            return redirect()->route('core.billing.plans')->with('toast', [
                'message' => 'پرداخت تأیید شد اما خطایی در فعال‌سازی رخ داد. با پشتیبانی تماس بگیرید.',
                'type' => 'warning', 'title' => 'خطا',
            ]);
        }

        session()->forget(['pending_plan_id', 'pending_authority']);

        return redirect()->route('core.billing.license')->with('toast', [
            'message' => "پرداخت موفق. کد رهگیری: {$verification['ref_id']}",
            'type'    => 'success', 'title'   => 'پرداخت موفق',
        ]);
    }

    /** تاریخچه پرداخت‌های tenant جاری */
    public function history()
    {
        $tenantId = $this->manager->getTenantId();
        $payments = Payment::where('tenant_id', $tenantId)->latest()->paginate(20);
        return view('core.billing.payments', compact('payments'));
    }
}

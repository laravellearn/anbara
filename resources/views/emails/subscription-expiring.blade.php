<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>هشدار انقضای اشتراک — {{ config('app.name') }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; background: #f4f5f7; margin: 0; padding: 20px; direction: rtl; }
  .wrapper { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .header  { background: #ff9f43; padding: 30px 40px; text-align: center; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; }
  .body    { padding: 30px 40px; }
  .body p  { color: #555; line-height: 1.8; margin: 0 0 16px; }
  .highlight { background: #fff3cd; border-right: 4px solid #ff9f43; padding: 14px 18px; border-radius: 4px; margin: 20px 0; }
  .highlight strong { color: #d63031; font-size: 18px; }
  .btn     { display: inline-block; background: #4361ee; color: #fff !important; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-size: 15px; margin: 10px 0; }
  .footer  { background: #f8f9fa; padding: 18px 40px; text-align: center; font-size: 12px; color: #aaa; }
  .plan-box { border: 1px solid #e0e0e0; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
  .plan-box td { padding: 6px 12px; color: #555; }
  .plan-box td:first-child { font-weight: bold; color: #333; width: 120px; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <h1>⚠️ هشدار انقضای اشتراک</h1>
  </div>

  <div class="body">
    <p>{{ $tenant->name }} عزیز، سلام</p>
    <p>
      اشتراک شما در سیستم <strong>{{ config('app.name') }}</strong> به زودی منقضی خواهد شد.
      لطفاً برای ادامه استفاده از امکانات، اشتراک خود را تمدید کنید.
    </p>

    <div class="highlight">
      <strong>{{ $remainDays }} روز</strong> تا پایان اشتراک شما باقی مانده است.
    </div>

    <table class="plan-box" width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td>پلن فعلی:</td>
        <td>{{ $subscription->plan?->name ?? '—' }}</td>
      </tr>
      <tr>
        <td>وضعیت:</td>
        <td>{{ $subscription->status === 'trial' ? 'دوره آزمایشی' : 'اشتراک فعال' }}</td>
      </tr>
      <tr>
        <td>تاریخ پایان:</td>
        <td>
          @php $endsAt = $subscription->trial_ends_at ?? $subscription->ends_at; @endphp
          {{ $endsAt ? verta($endsAt)->format('Y/m/d') : 'نامحدود' }}
        </td>
      </tr>
    </table>

    <p style="text-align:center; margin-top:24px;">
      <a href="{{ route('billing.plans') }}" class="btn">تمدید اشتراک</a>
    </p>

    <p style="font-size:13px; color:#888; margin-top:24px;">
      اگر اشتراک شما منقضی شود، دسترسی به امکانات سیستم محدود خواهد شد.
      در صورت بروز هرگونه مشکل با پشتیبانی تماس بگیرید.
    </p>
  </div>

  <div class="footer">
    این ایمیل به صورت خودکار ارسال شده است — {{ config('app.name') }}
    <br>
    در صورت عدم نیاز به دریافت این ایمیل‌ها، با پشتیبانی تماس بگیرید.
  </div>

</div>
</body>
</html>

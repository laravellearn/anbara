<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>سند یافت نشد — {{ config('app.name') }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; direction: rtl; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 480px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,.1); text-align: center; }
  h1 { color: #888; }
  .uuid { font-family: monospace; font-size: 11px; color: #bbb; word-break: break-all; margin-top: 12px; }
</style>
</head>
<body>
<div class="card">
  <div style="font-size:64px;">🔍</div>
  <h1>سند یافت نشد</h1>
  <p style="color:#666; font-size:13px;">سندی با این کد شناسایی در سیستم موجود نیست یا منقضی شده است.</p>
  <div class="uuid">UUID: {{ $uuid }}</div>
  <p style="font-size:11px; color:#aaa; margin-top:20px;">{{ config('app.name') }}</p>
</div>
</body>
</html>

{{--
  پارشیال: سربرگ چاپ — لوگو + نام سازمان + رنگ برند
  استفاده: @include('warehouse.partials._print_header', ['title' => 'فاکتور فروش'])
--}}
@php
    $tenantId   = app(\App\Services\TenantManager::class)->getTenantId();
    $orgName    = \App\Models\TenantSetting::get($tenantId, 'org_name', config('app.name'));
    $orgLogo    = \App\Models\TenantSetting::get($tenantId, 'org_logo', '');
    $brandColor = \App\Models\TenantSetting::get($tenantId, 'org_brand_color', '#3B82F6');
    $orgPhone   = \App\Models\TenantSetting::get($tenantId, 'org_phone', '');
    $orgAddress = \App\Models\TenantSetting::get($tenantId, 'org_address', '');
    $orgEmail   = \App\Models\TenantSetting::get($tenantId, 'org_email', '');
@endphp
<style>
  .print-header { display:flex; align-items:center; justify-content:space-between; border-bottom:3px solid {{ $brandColor }}; padding-bottom:12px; margin-bottom:18px; }
  .print-header .org-info { text-align:right; }
  .print-header .org-info h1 { font-size:16px; font-weight:bold; margin:0 0 4px; color:{{ $brandColor }}; }
  .print-header .org-info p  { font-size:10px; color:#555; margin:2px 0; }
  .print-header .org-logo img { max-height:70px; max-width:160px; }
  .print-doc-title { text-align:center; background:{{ $brandColor }}; color:#fff; padding:7px 0; font-size:14px; font-weight:bold; border-radius:4px; margin-bottom:14px; }
</style>

<div class="print-header">
  <div class="org-info">
    <h1>{{ $orgName }}</h1>
    @if($orgPhone) <p>📞 {{ $orgPhone }}</p> @endif
    @if($orgEmail) <p>✉ {{ $orgEmail }}</p> @endif
    @if($orgAddress) <p>📍 {{ $orgAddress }}</p> @endif
  </div>
  <div class="org-logo">
    @if($orgLogo)
      <img src="{{ asset('storage/' . $orgLogo) }}" alt="{{ $orgName }}">
    @else
      <div style="width:120px;height:60px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:10px;">لوگو</div>
    @endif
  </div>
</div>

@if(!empty($title))
<div class="print-doc-title">{{ $title }}</div>
@endif

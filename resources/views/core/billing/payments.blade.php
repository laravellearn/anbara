@extends('layouts.app')
@section('title', 'تاریخچه پرداخت‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header"><h5 class="mb-0">تاریخچه پرداخت‌ها</h5></div>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr><th>تاریخ</th><th>مبلغ (ریال)</th><th>توضیح</th><th>کد رهگیری</th><th>وضعیت</th></tr>
        </thead>
        <tbody>
          @forelse($payments as $p)
          <tr>
            <td>{{ $p->created_at->format('Y-m-d') }}</td>
            <td>{{ number_format($p->amount) }}</td>
            <td>{{ $p->description ?? '—' }}</td>
            <td>{{ $p->ref_id ?? '—' }}</td>
            <td>
              @php $map = ['paid'=>['success','پرداخت شده'],'pending'=>['warning','در انتظار'],'failed'=>['danger','ناموفق'],'canceled'=>['secondary','لغو شده']]; @endphp
              <span class="badge bg-label-{{ $map[$p->status][0] ?? 'secondary' }}">{{ $map[$p->status][1] ?? $p->status }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">پرداختی ثبت نشده.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
    @endif
  </div>
</div>
@endsection

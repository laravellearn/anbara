@extends('super-admin.layouts.master')
@section('title', 'اشتراک‌های فعال')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">لیست اشتراک‌ها</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>سازمان</th>
                    <th>پلن</th>
                    <th>شروع</th>
                    <th>پایان</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->tenant?->name ?? '—' }}</td>
                    <td>{{ $sub->plan?->name ?? '—' }}</td>
                    <td>{{ $sub->starts_at?->toJalali('Y/m/d') ?? '—' }}</td>
                    <td>{{ $sub->ends_at?->toJalali('Y/m/d') ?? 'نامحدود' }}</td>
                    <td>
                        <span class="badge bg-{{ $sub->status == 'active' ? 'success' : 'danger' }}">
                            {{ $sub->status == 'active' ? 'فعال' : 'لغو شده' }}
                        </span>
                    </td>
                    <td>
                        @if($sub->status == 'active')
                        <form action="{{ route('super-admin.subscriptions.cancel', $sub) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">لغو</button>
                        </form>
                        <form action="{{ route('super-admin.subscriptions.renew', $sub) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">تمدید</button>
                        </form>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">هیچ اشتراکی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
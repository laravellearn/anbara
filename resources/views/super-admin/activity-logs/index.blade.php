@extends('super-admin.layouts.master')
@section('title', 'لاگ‌های سیستمی')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">گزارش رویدادها</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>کاربر</th>
                    <th>سازمان</th>
                    <th>عمل</th>
                    <th>توضیحات</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->user?->name ?? 'سیستم' }}</td>
                    <td>{{ $log->tenant?->name ?? '—' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ Str::limit($log->description, 80) }}</td>
                    <td>{{ $log->created_at->toJalali('Y/m/d H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">لاگی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
</div>
@endsection
@extends('layouts.master')

@section('title', 'لاگ فعالیت‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">لاگ فعالیت‌ها</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>زمان</th>
                        <th>کاربر</th>
                        <th>عملیات</th>
                        <th>شرح</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ verta($log->created_at)->format('Y/m/d H:i') }}</td>
                        <td>{{ $log->user?->name ?? '---' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description }}</td>
                        <td dir="ltr">{{ $log->ip_address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'تاریخچه اشتراک')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">تاریخچه اشتراک‌ها</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        @if(auth()->user()->isSuperAdmin())
                            <th>سازمان</th>
                        @endif
                        <th>پلن</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                    <tr>
                        @if(auth()->user()->isSuperAdmin())
                            <td>{{ $sub->tenant->name }}</td>
                        @endif
                        <td>{{ $sub->plan->name }}</td>
                        <td>{{ verta($sub->starts_at)->format('Y/m/d') }}</td>
                        <td>{{ $sub->ends_at ? verta($sub->ends_at)->format('Y/m/d') : 'نامحدود' }}</td>
                        <td>
                            @if($sub->status == 'active')
                                <span class="badge bg-success">فعال</span>
                            @elseif($sub->status == 'expired')
                                <span class="badge bg-warning">منقضی</span>
                            @else
                                <span class="badge bg-secondary">لغو شده</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection
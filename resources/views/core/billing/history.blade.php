@extends('layouts.master')

@section('title', 'تاریخچه اشتراک')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- نمایش خطاها در صورت وجود --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-none border">
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
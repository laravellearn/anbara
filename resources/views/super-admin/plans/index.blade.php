@extends('super-admin.layouts.master')
@section('title', 'تعرفه‌ها')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">پلن‌ها</h5>
        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus"></i> ایجاد تعرفه جدید
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>slug</th>
                    <th>قیمت ماهانه</th>
                    <th>قیمت سالانه</th>
                    <th>مدت (روز)</th>
                    <th>ترتیب</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>{{ $plan->slug }}</td>
                    <td>{{ number_format($plan->monthly_price, 0) }} تومان</td>
                    <td>{{ number_format($plan->yearly_price, 0) }} تومان</td>
                    <td>{{ $plan->duration_days ?? '—' }}</td>
                    <td>{{ $plan->sort_order }}</td>
                    <td>
                        <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                            {{ $plan->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-sm btn-icon btn-outline-warning">
                            <i class="bx bx-edit"></i>
                        </a>
                        <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">هیچ تعرفه‌ای وجود ندارد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
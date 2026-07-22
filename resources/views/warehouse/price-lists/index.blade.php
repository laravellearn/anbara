@extends('layouts.warehouse')
@section('title', 'کاتالوگ قیمت')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-success"></i>کاتالوگ قیمت</h4>
    @can('access','price-lists.create')
    <a href="{{ route('warehouse.price-lists.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> لیست قیمت جدید
    </a>
    @endcan
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
          <div class="small text-muted">کل لیست‌ها</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-success">{{ $stats['active'] }}</div>
          <div class="small text-muted">فعال</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>نام لیست</th>
              <th>نوع</th>
              <th class="text-center">تعداد کالا</th>
              <th>اعتبار از</th>
              <th>اعتبار تا</th>
              <th class="text-center">وضعیت</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($priceLists as $pl)
            <tr>
              <td class="fw-medium">{{ $pl->name }}</td>
              <td>
                <span class="badge bg-{{ $pl->type_color }}-subtle text-{{ $pl->type_color }}">{{ $pl->type_label }}</span>
              </td>
              <td class="text-center">{{ $pl->items_count }}</td>
              <td><small class="text-muted">{{ $pl->valid_from ? \Morilog\Jalali\Jalalian::fromCarbon($pl->valid_from)->format('Y/m/d') : '—' }}</small></td>
              <td><small class="text-muted">{{ $pl->valid_to ? \Morilog\Jalali\Jalalian::fromCarbon($pl->valid_to)->format('Y/m/d') : '—' }}</small></td>
              <td class="text-center">
                <span class="badge bg-{{ $pl->is_active ? 'success' : 'secondary' }}-subtle text-{{ $pl->is_active ? 'success' : 'secondary' }}">
                  {{ $pl->is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </td>
              <td class="text-center">
                <a href="{{ route('warehouse.price-lists.show', $pl) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="fas fa-eye"></i></a>
                @can('access','price-lists.create')
                <a href="{{ route('warehouse.price-lists.edit', $pl) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="ویرایش"><i class="fas fa-edit"></i></a>
                @endcan
                @can('access','price-lists.delete')
                <form method="POST" action="{{ route('warehouse.price-lists.destroy', $pl) }}" class="d-inline"
                  onsubmit="return confirm('این لیست قیمت حذف شود؟')">@csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
                @endcan
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">لیست قیمتی ثبت نشده است.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $priceLists->links() }}</div>
    </div>
  </div>
</div>
@endsection

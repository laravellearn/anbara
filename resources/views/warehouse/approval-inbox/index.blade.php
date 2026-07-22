@extends('layouts.master')
@section('title', 'کارتابل تأیید')

@section('content')
<div class="container-fluid py-4">

  {{-- ─── هدر ─── --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1"><i class="bx bx-check-shield me-2 text-primary"></i>کارتابل تأیید</h4>
      <p class="text-muted mb-0">اسناد و سفارشات در انتظار تأیید شما</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ request()->fullUrlWithQuery(['type'=>'all']) }}"
         class="btn btn-sm {{ $filter==='all' ? 'btn-primary' : 'btn-outline-secondary' }}">همه</a>
      <a href="{{ request()->fullUrlWithQuery(['type'=>'documents']) }}"
         class="btn btn-sm {{ $filter==='documents' ? 'btn-primary' : 'btn-outline-secondary' }}">
        اسناد انبار <span class="badge bg-danger ms-1">{{ $counts['docs'] }}</span>
      </a>
      <a href="{{ request()->fullUrlWithQuery(['type'=>'purchase_orders']) }}"
         class="btn btn-sm {{ $filter==='purchase_orders' ? 'btn-primary' : 'btn-outline-secondary' }}">
        سفارشات خرید <span class="badge bg-warning ms-1">{{ $counts['pos'] }}</span>
      </a>
    </div>
  </div>

  {{-- ─── اسناد انبار ─── --}}
  @if($filter === 'all' || $filter === 'documents')
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bx bx-file me-2"></i>اسناد انبار در انتظار تأیید</h6>
      <span class="badge bg-danger">{{ $pendingDocs->count() }}</span>
    </div>
    <div class="card-body p-0">
      @if($pendingDocs->isEmpty())
        <div class="text-center py-5 text-muted"><i class="bx bx-check-circle fs-1"></i><p>موردی در انتظار تأیید نیست</p></div>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>شماره سند</th><th>نوع</th><th>انبار</th><th>ثبت‌کننده</th><th>تاریخ</th><th>وضعیت</th><th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pendingDocs as $doc)
            <tr>
              <td><a href="{{ route('warehouse.documents.show', $doc) }}" class="fw-semibold">{{ $doc->document_number }}</a></td>
              <td>{{ $doc->type_label ?? $doc->type }}</td>
              <td>{{ $doc->warehouse?->title }}</td>
              <td>{{ $doc->creator?->name }}</td>
              <td>{{ $doc->created_at->format('Y-m-d') }}</td>
              <td><span class="badge bg-warning">در انتظار</span></td>
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  {{-- تأیید سریع --}}
                  @can('access', 'warehouse-documents.approve')
                  <form method="POST" action="{{ route('warehouse.approval-inbox.approve-document', $doc) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success" onclick="return confirm('تأیید سند؟')">
                      <i class="bx bx-check"></i>
                    </button>
                  </form>
                  {{-- رد سریع --}}
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectDocModal{{ $doc->id }}">
                    <i class="bx bx-x"></i>
                  </button>
                  @endcan
                  <a href="{{ route('warehouse.documents.show', $doc) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-show"></i>
                  </a>
                </div>
              </td>
            </tr>
            {{-- Modal رد سند --}}
            <div class="modal fade" id="rejectDocModal{{ $doc->id }}" tabindex="-1">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <form method="POST" action="{{ route('warehouse.approval-inbox.reject-document', $doc) }}">
                    @csrf
                    <div class="modal-header"><h6 class="modal-title">رد سند</h6></div>
                    <div class="modal-body">
                      <textarea name="reason" class="form-control" rows="3" placeholder="دلیل رد..."></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">انصراف</button>
                      <button type="submit" class="btn btn-danger btn-sm">رد کردن</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- ─── سفارشات خرید ─── --}}
  @if($filter === 'all' || $filter === 'purchase_orders')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bx bx-cart me-2"></i>سفارشات خرید در انتظار تأیید</h6>
      <span class="badge bg-warning text-dark">{{ $pendingPos->count() }}</span>
    </div>
    <div class="card-body p-0">
      @if($pendingPos->isEmpty())
        <div class="text-center py-5 text-muted"><i class="bx bx-check-circle fs-1"></i><p>موردی در انتظار تأیید نیست</p></div>
      @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>شماره PO</th><th>تأمین‌کننده</th><th>انبار</th><th>ثبت‌کننده</th><th>تاریخ</th><th class="text-end">مبلغ کل</th><th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pendingPos as $po)
            <tr>
              <td><a href="{{ route('warehouse.purchase-orders.show', $po) }}" class="fw-semibold">{{ $po->po_number }}</a></td>
              <td>{{ $po->supplier?->full_name }}</td>
              <td>{{ $po->warehouse?->title }}</td>
              <td>{{ $po->creator?->name }}</td>
              <td>{{ $po->order_date?->format('Y-m-d') }}</td>
              <td class="text-end">{{ number_format($po->total_amount) }} {{ $po->currency }}</td>
              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  @can('access', 'purchase-orders.confirm')
                  <form method="POST" action="{{ route('warehouse.approval-inbox.approve-po', $po) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success" onclick="return confirm('تأیید سفارش؟')">
                      <i class="bx bx-check"></i>
                    </button>
                  </form>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectPoModal{{ $po->id }}">
                    <i class="bx bx-x"></i>
                  </button>
                  @endcan
                  <a href="{{ route('warehouse.purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-show"></i>
                  </a>
                </div>
              </td>
            </tr>
            {{-- Modal رد PO --}}
            <div class="modal fade" id="rejectPoModal{{ $po->id }}" tabindex="-1">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <form method="POST" action="{{ route('warehouse.approval-inbox.reject-po', $po) }}">
                    @csrf
                    <div class="modal-header"><h6 class="modal-title">رد سفارش</h6></div>
                    <div class="modal-body">
                      <textarea name="reason" class="form-control" rows="3" placeholder="دلیل رد..."></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">انصراف</button>
                      <button type="submit" class="btn btn-danger btn-sm">رد کردن</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
  @endif

</div>
@endsection

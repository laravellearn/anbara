{{-- کامپوننت پیوست‌ها — قابل استفاده در هر سند یا سفارش --}}
{{-- پارامترها: $model (WarehouseDocument|PurchaseOrder), $modelType ('WarehouseDocument'|'PurchaseOrder') --}}

<div class="card mt-4" id="attachments-section">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="bx bx-paperclip me-2"></i>پیوست‌ها</h6>
    <span class="badge bg-secondary">{{ $model->attachments->count() }}</span>
  </div>
  <div class="card-body">

    {{-- فرم آپلود --}}
    @can('access', 'attachments.create')
    <form action="{{ route('warehouse.attachments.store') }}" method="POST" enctype="multipart/form-data" class="mb-3">
      @csrf
      <input type="hidden" name="attachable_type" value="{{ $modelType }}">
      <input type="hidden" name="attachable_id"   value="{{ $model->id }}">
      <div class="row g-2 align-items-end">
        <div class="col-md-5">
          <label class="form-label small">انتخاب فایل</label>
          <input type="file" name="file" class="form-control form-control-sm" required
                 accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.zip">
          <div class="form-text">حداکثر ۱۰ مگابایت — PDF، تصویر، Word، Excel، ZIP</div>
        </div>
        <div class="col-md-5">
          <label class="form-label small">توضیح (اختیاری)</label>
          <input type="text" name="description" class="form-control form-control-sm" placeholder="مثلاً: فاکتور اسکن‌شده">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i class="bx bx-upload me-1"></i>آپلود
          </button>
        </div>
      </div>
    </form>
    @endcan

    {{-- لیست پیوست‌ها --}}
    @if($model->attachments->isEmpty())
      <p class="text-muted text-center mb-0"><i class="bx bx-file fs-4 d-block mb-1"></i>پیوستی ثبت نشده</p>
    @else
    <ul class="list-group list-group-flush">
      @foreach($model->attachments as $att)
      <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <div class="d-flex align-items-center gap-2">
          <i class="{{ $att->icon_class }} fs-4"></i>
          <div>
            <div class="fw-semibold small">{{ $att->file_name }}</div>
            @if($att->description)<div class="text-muted" style="font-size:.78rem">{{ $att->description }}</div>@endif
            <div class="text-muted" style="font-size:.75rem">
              {{ $att->file_size_human }} — {{ $att->created_at->format('Y-m-d H:i') }}
              @if($att->uploader) — {{ $att->uploader->name }} @endif
            </div>
          </div>
        </div>
        <div class="d-flex gap-1">
          <a href="{{ route('warehouse.attachments.download', $att) }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-download"></i>
          </a>
          @can('access', 'attachments.delete')
          <form method="POST" action="{{ route('warehouse.attachments.destroy', $att) }}" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف پیوست؟')">
              <i class="bx bx-trash"></i>
            </button>
          </form>
          @endcan
        </div>
      </li>
      @endforeach
    </ul>
    @endif

  </div>
</div>

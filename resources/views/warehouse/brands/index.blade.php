@extends('layouts.master')
@section('title', 'مدیریت برندها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- آمار --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-4">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">کل برندها</span><h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3></div>
                    <span class="badge bg-label-primary rounded p-2"><i class="bx bx-purchase-tag bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">فعال</span><h3 class="mb-0 mt-1 text-success">{{ $stats['active'] }}</h3></div>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-check-circle bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">غیرفعال</span><h3 class="mb-0 mt-1">{{ $stats['inactive'] }}</h3></div>
                    <span class="badge bg-label-secondary rounded p-2"><i class="bx bx-minus-circle bx-sm"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0"><i class="bx bx-purchase-tag me-1"></i> برندها</h5>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" id="liveSearch" class="form-control form-control-sm" placeholder="جستجو..." style="width:200px">
                <select id="filterStatus" class="form-select form-select-sm" style="width:130px">
                    <option value="">همه</option>
                    <option value="active">فعال</option>
                    <option value="inactive">غیرفعال</option>
                </select>
                @can('access', 'brands.create')
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal">
                    <i class="bx bx-plus"></i> برند جدید
                </button>
                @endcan
            </div>
        </div>
        <div class="table-responsive" id="tableWrapper">
            @include('warehouse.brands._table', ['brands' => $brands])
        </div>
    </div>
</div>

{{-- مودال ایجاد / ویرایش برند --}}
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="brandForm" method="POST" action="{{ route('warehouse.brands.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">برند جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام برند <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="brandName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="brandDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="brandActive" value="1" checked>
                        <label class="form-check-label" for="brandActive">فعال</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // live search
    let timeout;
    function load() {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            $.get('{{ route("warehouse.brands.index") }}', {
                ajax: 1,
                search: $('#liveSearch').val(),
                status: $('#filterStatus').val(),
            }, function (res) {
                $('#tableWrapper').html(res.html);
            });
        }, 350);
    }
    $('#liveSearch').on('input', load);
    $('#filterStatus').on('change', load);

    // ویرایش
    $(document).on('click', '.btn-edit-brand', function () {
        const d = $(this).data();
        $('#modalTitle').text('ویرایش برند');
        $('#brandForm').attr('action', `/warehouse/brands/${d.id}`);
        $('#formMethod').val('PUT');
        $('#brandName').val(d.name);
        $('#brandDescription').val(d.description);
        $('#brandActive').prop('checked', d.is_active == '1');
        $('#brandModal').modal('show');
    });

    // reset on new
    $('[data-bs-target="#brandModal"]').on('click', function () {
        if (!$(this).hasClass('btn-edit-brand')) {
            $('#modalTitle').text('برند جدید');
            $('#brandForm').attr('action', '{{ route("warehouse.brands.store") }}');
            $('#formMethod').val('POST');
            $('#brandForm')[0].reset();
            $('#brandActive').prop('checked', true);
        }
    });

    // حذف با تأیید
    $(document).on('submit', '.delete-form', function (e) {
        e.preventDefault();
        if (confirm('آیا از حذف این برند مطمئن هستید؟')) this.submit();
    });
});
</script>
@endpush

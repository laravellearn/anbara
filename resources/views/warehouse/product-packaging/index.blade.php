@extends('layouts.master')

@section('title', 'بسته‌بندی‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل بسته‌بندی‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $packagings->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-box bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-box me-1"></i> واحدهای بسته‌بندی
                <small class="text-muted ms-2">({{ $packagings->total() }})</small>
            </h5>
            @can('access', 'item-packaging.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> بسته‌بندی جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.packagings._table', ['packagings' => $packagings])
        </div>
    </div>
</div>

@include('admin.packagings._modal', ['products' => $products, 'units' => $units])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-pkg-btn').on('click', function(){
            const btn = $(this);
            $('#packForm').attr('action', `{{ route('admin.item-packaging.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#packForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#pkg_product').val(btn.data('product'));
            $('#pkg_unit').val(btn.data('unit'));
            $('#pkg_name').val(btn.data('name'));
            $('#pkg_qty').val(btn.data('qty'));
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#packForm').attr('action', `{{ route('admin.item-packaging.store') }}`);
            $('input[name="_method"]').remove();
            $('#packForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این بسته‌بندی حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
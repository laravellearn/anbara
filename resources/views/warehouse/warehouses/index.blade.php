@extends('layouts.master')

@section('title', 'انبارها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل انبارها</span>
                            <h3 class="mb-0 mt-1">{{ $warehouses->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-buildings bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-buildings me-1"></i> لیست انبارها
                <small class="text-muted ms-2">({{ $warehouses->total() }})</small>
            </h5>
            @can('access', 'warehouses.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> انبار جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.warehouses._table', ['warehouses' => $warehouses])
        </div>
    </div>
</div>

@include('admin.warehouses._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-warehouse-btn').on('click', function(){
            const btn = $(this);
            $('#warehouseForm').attr('action', `{{ route('admin.warehouses.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#warehouseForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#wh_name').val(btn.data('name'));
            $('#wh_code').val(btn.data('code'));
            $('#wh_address').val(btn.data('address'));
            $('#wh_manager').val(btn.data('manager'));
            $('#wh_capacity').val(btn.data('capacity'));
            $('#wh_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#warehouseForm').attr('action', `{{ route('admin.warehouses.store') }}`);
            $('input[name="_method"]').remove();
            $('#warehouseForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این انبار حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
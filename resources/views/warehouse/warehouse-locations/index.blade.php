@extends('layouts.master')

@section('title', 'موقعیت‌های انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل موقعیت‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $locations->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-map-pin bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-map-pin me-1"></i> موقعیت‌ها
                <small class="text-muted ms-2">({{ $locations->total() }})</small>
            </h5>
            @can('access', 'warehouse-locations.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> موقعیت جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.locations._table', ['locations' => $locations])
        </div>
    </div>
</div>

@include('admin.locations._modal', ['warehouses' => $warehouses])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-loc-btn').on('click', function(){
            const btn = $(this);
            $('#locForm').attr('action', `{{ route('admin.warehouse-locations.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#locForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#loc_warehouse').val(btn.data('warehouse'));
            $('#loc_parent').val(btn.data('parent'));
            $('#loc_code').val(btn.data('code'));
            $('#loc_name').val(btn.data('name'));
            $('#loc_type').val(btn.data('type'));
            $('#loc_capacity').val(btn.data('capacity'));
            $('#loc_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#locForm').attr('action', `{{ route('admin.warehouse-locations.store') }}`);
            $('input[name="_method"]').remove();
            $('#locForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این موقعیت حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
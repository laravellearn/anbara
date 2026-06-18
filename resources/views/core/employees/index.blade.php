@extends('layouts.master')

@section('title', 'کارمندان')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل کارمندان</span>
                            <h3 class="mb-0 mt-1">{{ $employees->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-user-badge bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-user-badge me-1"></i> لیست کارمندان
                <small class="text-muted ms-2">({{ $employees->total() }})</small>
            </h5>
            @can('access', 'employees.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> کارمند جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('core.employees._table', ['employees' => $employees])
        </div>
    </div>
</div>

@include('core.employees._modal', ['units' => $units])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-employee-btn').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            $('#employeeForm').attr('action', `{{ route('warehouse.employees.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#employeeForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#emp_name').val(btn.data('name'));
            $('#emp_code').val(btn.data('code'));
            $('#emp_unit').val(btn.data('unit'));
            $('#emp_position').val(btn.data('position'));
            $('#emp_mobile').val(btn.data('mobile'));
            $('#emp_phone').val(btn.data('phone'));
            $('#emp_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#employeeForm').attr('action', `{{ route('warehouse.employees.store') }}`);
            $('input[name="_method"]').remove();
            $('#employeeForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این کارمند حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
@extends('layouts.master')

@section('title', 'واحدهای اندازه‌گیری')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل واحدها</span>
                            <h3 class="mb-0 mt-1">{{ $units->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-ruler bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-ruler me-1"></i> لیست واحدهای اندازه‌گیری
                <small class="text-muted ms-2">({{ $units->total() }} واحد)</small>
            </h5>
            @can('access', 'units.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> واحد جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive" id="unitsTableWrapper">
            @include('admin.units._table', ['units' => $units])
        </div>
    </div>
</div>

@include('admin.units._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-unit-btn').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            $('#unitForm').attr('action', `{{ route('admin.units.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#unitForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#unit_title').val(btn.data('title'));
            $('#unit_symbol').val(btn.data('symbol'));
            $('#unit_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#unitForm').attr('action', `{{ route('admin.units.store') }}`);
            $('input[name="_method"]').remove();
            $('#unitForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این واحد حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
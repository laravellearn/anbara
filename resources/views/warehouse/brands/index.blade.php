@extends('layouts.master')

@section('title', 'برندها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل برندها</span>
                            <h3 class="mb-0 mt-1">{{ $brands->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-copyright bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-copyright me-1"></i> لیست برندها
                <small class="text-muted ms-2">({{ $brands->total() }})</small>
            </h5>
            @can('access', 'brands.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> برند جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('warehouse.brands._table', ['brands' => $brands])
        </div>
    </div>
</div>

@include('warehouse.brands._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-brand-btn').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            $('#brandForm').attr('action', `{{ route('warehouse.brands.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#brandForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#brand_title').val(btn.data('title'));
            $('#brand_desc').val(btn.data('desc'));
            $('#brand_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#brandForm').attr('action', `{{ route('warehouse.brands.store') }}`);
            $('input[name="_method"]').remove();
            $('#brandForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این برند حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
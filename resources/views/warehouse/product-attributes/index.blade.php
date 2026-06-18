@extends('layouts.master')

@section('title', 'ویژگی‌های کالا')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل ویژگی‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $attributes->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-list-check bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-list-check me-1"></i> ویژگی‌ها
                <small class="text-muted ms-2">({{ $attributes->total() }})</small>
            </h5>
            @can('access', 'item-attributes.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> ویژگی جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.product-attributes._table', ['attributes' => $attributes])
        </div>
    </div>
</div>

@include('admin.product-attributes._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-attr-btn').on('click', function(){
            const btn = $(this);
            $('#attrForm').attr('action', `{{ route('admin.item-attributes.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#attrForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#attr_name').val(btn.data('name'));
            $('#attr_type').val(btn.data('type'));
            let opts = btn.data('options');
            if (Array.isArray(opts)) opts = opts.join(',');
            $('#attr_options').val(opts || '');
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#attrForm').attr('action', `{{ route('admin.item-attributes.store') }}`);
            $('input[name="_method"]').remove();
            $('#attrForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این ویژگی حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
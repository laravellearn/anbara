@extends('layouts.master')

@section('title', 'دسته‌بندی کالا')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل دسته‌بندی‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $categories->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-category bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-category me-1"></i> دسته‌بندی‌ها
                <small class="text-muted ms-2">({{ $categories->total() }})</small>
            </h5>
            @can('access', 'item-categories.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> دسته‌بندی جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.product-categories._table', ['categories' => $categories])
        </div>
    </div>
</div>

@include('admin.product-categories._modal', ['allCategories' => $allCategories])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-cat-btn').on('click', function(){
            const btn = $(this);
            $('#categoryForm').attr('action', `{{ route('admin.item-categories.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#categoryForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#cat_name').val(btn.data('name'));
            $('#cat_parent').val(btn.data('parent'));
            $('#cat_desc').val(btn.data('desc'));
            $('#cat_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#categoryForm').attr('action', `{{ route('admin.item-categories.store') }}`);
            $('input[name="_method"]').remove();
            $('#categoryForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این دسته‌بندی حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
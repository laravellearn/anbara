@extends('layouts.master')

@section('title', 'کالاها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل کالاها</span>
                            <h3 class="mb-0 mt-1">{{ $products->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-package bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-package me-1"></i> لیست کالاها
                <small class="text-muted ms-2">({{ $products->total() }})</small>
            </h5>
            @can('access', 'items.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> کالای جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.products._table', ['products' => $products])
        </div>
    </div>
</div>

@include('admin.products._modal', ['categories' => $categories, 'units' => $units])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-product-btn').on('click', function(){
            const btn = $(this);
            $('#productForm').attr('action', `{{ route('admin.items.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#productForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#prod_name').val(btn.data('name'));
            $('#prod_sku').val(btn.data('sku'));
            $('#prod_barcode').val(btn.data('barcode'));
            $('#prod_category').val(btn.data('category'));
            $('#prod_unit').val(btn.data('unit'));
            $('#prod_min').val(btn.data('min'));
            $('#prod_max').val(btn.data('max'));
            $('#prod_desc').val(btn.data('desc'));
            $('#prod_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#productForm').attr('action', `{{ route('admin.items.store') }}`);
            $('input[name="_method"]').remove();
            $('#productForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این کالا حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
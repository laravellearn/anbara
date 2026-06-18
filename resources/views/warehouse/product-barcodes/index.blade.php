@extends('layouts.master')

@section('title', 'بارکدها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل بارکدها</span>
                            <h3 class="mb-0 mt-1">{{ $barcodes->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-barcode bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-barcode me-1"></i> لیست بارکدها
                <small class="text-muted ms-2">({{ $barcodes->total() }})</small>
            </h5>
            @can('access', 'barcodes.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> بارکد جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.barcodes._table', ['barcodes' => $barcodes])
        </div>
    </div>
</div>

@include('admin.barcodes._modal', ['products' => $products])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-bc-btn').on('click', function(){
            const btn = $(this);
            $('#barcodeForm').attr('action', `{{ route('admin.barcodes.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#barcodeForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#bc_product').val(btn.data('product'));
            $('#bc_barcode').val(btn.data('barcode'));
            $('#bc_default').prop('checked', btn.data('default') == '1' || btn.data('default') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#barcodeForm').attr('action', `{{ route('admin.barcodes.store') }}`);
            $('input[name="_method"]').remove();
            $('#barcodeForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این بارکد حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
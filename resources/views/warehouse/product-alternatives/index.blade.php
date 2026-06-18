@extends('layouts.master')

@section('title', 'کالاهای جایگزین')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل جایگزین‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $alternatives->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-git-compare bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-git-compare me-1"></i> لیست جایگزین‌ها
                <small class="text-muted ms-2">({{ $alternatives->total() }})</small>
            </h5>
            @can('access', 'item-alternatives.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> جایگزین جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('admin.alternatives._table', ['alternatives' => $alternatives])
        </div>
    </div>
</div>

@include('admin.alternatives._modal', ['products' => $products])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-alt-btn').on('click', function(){
            const btn = $(this);
            $('#altForm').attr('action', `{{ route('admin.item-alternatives.update', ':id') }}`.replace(':id', btn.data('id')));
            if (!$('input[name="_method"]').length) $('#altForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#alt_product').val(btn.data('product'));
            $('#alt_alternative').val(btn.data('alternative'));
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#altForm').attr('action', `{{ route('admin.item-alternatives.store') }}`);
            $('input[name="_method"]').remove();
            $('#altForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این جایگزین حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
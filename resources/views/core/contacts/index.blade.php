@extends('layouts.master')

@section('title', 'مخاطبین')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل مخاطبین</span>
                            <h3 class="mb-0 mt-1">{{ $contacts->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-user-pin bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-user-pin me-1"></i> لیست مخاطبین
                <small class="text-muted ms-2">({{ $contacts->total() }})</small>
            </h5>
            @can('access', 'contacts.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> مخاطب جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive">
            @include('core.contacts._table', ['contacts' => $contacts])
        </div>
    </div>
</div>

@include('core.contacts._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-contact-btn').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            $('#contactForm').attr('action', `{{ route('warehouse.contacts.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#contactForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#contact_type').val(btn.data('type'));
            $('#contact_first_name').val(btn.data('first_name'));
            $('#contact_last_name').val(btn.data('last_name'));
            $('#contact_company_name').val(btn.data('company_name'));
            $('#contact_mobile').val(btn.data('mobile'));
            $('#contact_phone').val(btn.data('phone'));
            $('#contact_email').val(btn.data('email'));
            $('#contact_is_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });
        $('#createModal').on('hidden.bs.modal', function(){
            $('#contactForm').attr('action', `{{ route('warehouse.contacts.store') }}`);
            $('input[name="_method"]').remove();
            $('#contactForm')[0].reset();
        });
        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این مخاطب حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
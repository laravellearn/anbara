@extends('layouts.master')

@section('title', 'مراکز هزینه')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.cost-centers._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="کد یا عنوان..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-money me-1"></i> مراکز هزینه
                <small class="text-muted ms-2" id="filteredCount">({{ $costCenters->total() }})</small>
            </h5>
            @can('access', 'cost-centers.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> مرکز هزینه جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('warehouse.cost-centers._table', ['costCenters' => $costCenters])
        </div>
    </div>
</div>

@include('warehouse.cost-centers._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        let searchTimeout;
        const $tableWrapper = $('#tableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('warehouse.cost-centers.index') }}',
                data: { search, status, ajax: 1 },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 500); });
        $('#filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() { $('#liveSearch').val('').focus(); performSearch(); });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterStatus').val('');
            performSearch();
        });

        // اسکریپت‌های مودال ویرایش
        $('.edit-btn').on('click', function(){
            const btn = $(this);
            const id = btn.data('id');
            $('#ccForm').attr('action', `{{ route('warehouse.cost-centers.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#ccForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#cc_code').val(btn.data('code'));
            $('#cc_title').val(btn.data('title'));
            $('#cc_desc').val(btn.data('desc'));
            $('#cc_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });

        $('#createModal').on('hidden.bs.modal', function(){
            $('#ccForm').attr('action', `{{ route('warehouse.cost-centers.store') }}`);
            $('input[name="_method"]').remove();
            $('#ccForm')[0].reset();
        });

        $('.delete-form').on('submit', function(e){
            e.preventDefault(); const f=this;
            Swal.fire({title:'مطمئن هستید؟',text:'این مرکز هزینه حذف خواهد شد.',icon:'warning',showCancelButton:true,confirmButtonText:'بله',cancelButtonText:'لغو'}).then(r=>{if(r.isConfirmed)f.submit();});
        });
    });
</script>
@endpush
@extends('layouts.master')

@section('title', 'مخاطبین')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('core.contacts._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="نام، شرکت یا موبایل..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">نوع</label>
                    <select id="filterType" class="form-select">
                        <option value="">همه</option>
                        <option value="customer">مشتری</option>
                        <option value="supplier">تأمین‌کننده</option>
                        <option value="both">هر دو</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <i class="bx bx-user-pin me-1"></i> مخاطبین
                <small class="text-muted ms-2" id="filteredCount">({{ $contacts->total() }})</small>
            </h5>
            @can('access', 'contacts.create')
            <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> مخاطب جدید
            </a>
            @endcan
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('core.contacts._table', ['contacts' => $contacts])
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        let searchTimeout;
        const $tableWrapper = $('#tableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const type = $('#filterType').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('contacts.index') }}',
                data: {
                    search,
                    type,
                    status,
                    ajax: 1
                },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
        $('#filterType, #filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() {
            $('#liveSearch').val('').focus();
            performSearch();
        });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterType').val('');
            $('#filterStatus').val('');
            performSearch();
        });
    });


        // حذف با تأیید
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'آیا مطمئن هستید؟',
            text: "این مخاطب حذف خواهد شد.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف کن',
            cancelButtonText: 'لغو',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
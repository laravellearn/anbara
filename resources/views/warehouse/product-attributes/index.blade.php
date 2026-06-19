@extends('layouts.master')

@section('title', 'ویژگی‌های کالا')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.product-attributes._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="نام ویژگی..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">نوع</label>
                    <select id="filterType" class="form-select">
                        <option value="">همه</option>
                        <option value="text">متن</option>
                        <option value="number">عدد</option>
                        <option value="select">انتخاب</option>
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
                <i class="bx bx-list-check me-1"></i> ویژگی‌ها
                <small class="text-muted ms-2" id="filteredCount">({{ $attributes->total() }})</small>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                {{-- Export placeholder --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export"></i> خروجی
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bx-file me-1"></i> Excel (به‌زودی)</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF (به‌زودی)</a></li>
                    </ul>
                </div>

                @can('access', 'product-attributes.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bx bx-plus"></i> ویژگی جدید
                </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('warehouse.product-attributes._table', ['attributes' => $attributes])
        </div>
    </div>
</div>

@include('warehouse.product-attributes._modal')
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
            const type = $('#filterType').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('warehouse.product-attributes.index') }}',
                data: { search, type, status, ajax: 1 },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 500); });
        $('#filterType, #filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() { $('#liveSearch').val('').focus(); performSearch(); });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterType').val('');
            $('#filterStatus').val('');
            performSearch();
        });

        // ========== مودال ویرایش ==========
        $(document).on('click', '.edit-attr-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            $('#attrForm').attr('action', `{{ route('warehouse.product-attributes.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#attrForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#attr_name').val(btn.data('name'));
            $('#attr_type').val(btn.data('type'));
            let opts = btn.data('options');
            if (Array.isArray(opts)) opts = opts.join(',');
            $('#attr_options').val(opts || '');
            $('#attr_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });

        // ========== ریست فرم هنگام بسته شدن مودال ==========
        $('#createModal').on('hidden.bs.modal', function() {
            $('#attrForm').attr('action', `{{ route('warehouse.product-attributes.store') }}`);
            $('input[name="_method"]').remove();
            $('#attrForm')[0].reset();
        });

        // ========== حذف با تأیید ==========
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این ویژگی حذف خواهد شد.",
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

        // ========== نمایش خطاهای اعتبارسنجی در مودال ==========
        @if($errors->any() && session('show_create_modal'))
            $('#createModal').modal('show');
            @foreach ($errors->all() as $error)
                if (typeof showToast !== 'undefined') {
                    showToast('{{ $error }}', 'error', 'خطای اعتبارسنجی');
                }
            @endforeach
        @endif
    });
</script>
@endpush
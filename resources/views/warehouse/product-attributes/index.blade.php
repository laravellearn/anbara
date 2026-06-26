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

{{-- مودال‌ها --}}
@include('warehouse.product-attributes._modal')
@endsection

@push('scripts')
<script>
    $(function(){
        // ========== توابع کمکی برای تگ‌اینپوت ==========
        function initTagInput(wrapperSelector, tagContainerSelector, inputSelector, hiddenSelector) {
            const $wrapper = $(wrapperSelector);
            const $container = $(tagContainerSelector);
            const $input = $(inputSelector);
            const $hidden = $(hiddenSelector);

            // حذف event های قبلی برای جلوگیری از duplicate
            $input.off('keydown.tag').on('keydown.tag', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    let val = $input.val().trim().replace(/,/g, '');
                    if (val !== '') {
                        addTag(val);
                    }
                }
                // حذف آخرین تگ با Backspace اگر input خالی باشد
                if (e.key === 'Backspace' && $input.val() === '' && $container.children().length) {
                    $container.children().last().remove();
                    updateHidden();
                }
            });

            // کلیک روی wrapper فوکوس را به input می‌دهد
            $wrapper.on('click', function(e) {
                if (e.target === $wrapper[0] || $(e.target).hasClass('tag-input-wrapper')) {
                    $input.focus();
                }
            });

            function addTag(text) {
                let $tag = $('<span class="badge bg-label-primary d-flex align-items-center gap-1">' +
                    text + '<i class="bx bx-x" style="cursor:pointer;"></i></span>');
                $tag.find('.bx-x').on('click', function() {
                    $tag.remove();
                    updateHidden();
                });
                $container.append($tag);
                updateHidden();
                $input.val('').focus();
            }

            function updateHidden() {
                let tags = [];
                $container.find('.badge').each(function() {
                    tags.push($(this).contents().first().text().trim());
                });
                $hidden.val(tags.join(','));
            }

            return {
                setTags: function(tagsArray) {
                    $container.empty();
                    if (tagsArray && tagsArray.length) {
                        tagsArray.forEach(t => addTag(t));
                    }
                },
                clear: function() {
                    $container.empty();
                    $hidden.val('');
                }
            };
        }

        // ========== مودال ایجاد ==========
        const createTag = initTagInput('#createTagInputWrapper', '#createTagContainer', '#createTagInput', '#createOptionsHidden');

        $('#create_attr_type').on('change', function() {
            if ($(this).val() === 'select') {
                $('#createOptionsContainer').show();
            } else {
                $('#createOptionsContainer').hide();
                createTag.clear();
            }
        }).trigger('change');

        // ریست فرم ایجاد هنگام بسته شدن
        $('#createModal').on('hidden.bs.modal', function() {
            $('#createAttrForm')[0].reset();
            createTag.clear();
            $('#create_attr_type').trigger('change');
        });

        // ========== مودال ویرایش ==========
        const editTag = initTagInput('#editTagInputWrapper', '#editTagContainer', '#editTagInput', '#editOptionsHidden');

        $('#edit_attr_type').on('change', function() {
            if ($(this).val() === 'select') {
                $('#editOptionsContainer').show();
            } else {
                $('#editOptionsContainer').hide();
                editTag.clear();
            }
        });

        $(document).on('click', '.edit-attr-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            const name = btn.data('name');
            const type = btn.data('type');
            let options = btn.data('options'); // این آرایه است یا string
            const active = btn.data('active');

            // تنظیم action
            $('#editAttrForm').attr('action', '{{ route('warehouse.product-attributes.update', ':id') }}'.replace(':id', id));

            // پر کردن فیلدها
            $('#edit_attr_name').val(name);
            $('#edit_attr_type').val(type);
            $('#edit_attr_active').prop('checked', active == '1' || active == true);

            // گزینه‌ها
            if (type === 'select' && options) {
                // اگر options یک آرایه باشد، مستقیماً استفاده می‌کنیم وگرنه تبدیل می‌کنیم
                let optsArray = [];
                if (Array.isArray(options)) {
                    optsArray = options;
                } else if (typeof options === 'string') {
                    try {
                        optsArray = JSON.parse(options);
                    } catch (e) {
                        optsArray = options.split(',').map(o => o.trim()).filter(Boolean);
                    }
                }
                editTag.setTags(optsArray);
                $('#editOptionsContainer').show();
            } else {
                editTag.clear();
                $('#editOptionsContainer').hide();
            }

            $('#editModal').modal('show');
        });

        $('#editModal').on('hidden.bs.modal', function() {
            $('#editAttrForm')[0].reset();
            editTag.clear();
            $('#editOptionsContainer').hide();
        });

        // ========== جستجوی Ajax ==========
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

        // ========== حذف با تأیید ==========
        $(document).on('submit', '.delete-form', function(e) {
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

        // ========== نمایش خطاهای مودال‌ها ==========
        @if($errors->any() && session('show_create_modal'))
            $('#createModal').modal('show');
        @endif
        @if($errors->any() && session('show_edit_modal'))
            // در صورت خطای ویرایش، باید اطلاعات ویژگی را از session بگیریم. 
            // برای سادگی، می‌توانید با فلش کردن داده‌ها این بخش را مدیریت کنید.
            // اما معمولاً با redirect->back()->withInput() انجام می‌شود.
            // اینجا به علت پیچیدگی، یک نمایش مودال ساده داریم.
            $('#editModal').modal('show');
        @endif
    });
</script>

<style>
    .tag-input-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        cursor: text;
        min-height: 38px;
    }
    .tag-input-wrapper .badge {
        font-size: 0.85rem;
    }
    .tag-input-wrapper input {
        min-width: 120px;
    }
</style>
@endpush
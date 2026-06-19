@extends('layouts.master')

@section('title', 'ایجاد نقش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">ایجاد نقش جدید</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            @include('core.roles._form')
            <div class="card-footer text-end">
                <a href="{{ route('roles.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // ========== تغییر وضعیت چک‌باکس گروه ==========
        $(document).on('change', '.group-checkbox', function() {
            const groupSlug = $(this).data('group');
            const isChecked = $(this).prop('checked');
            // انتخاب یا حذف تمام مجوزهای این گروه
            $(`.group-${groupSlug}`).prop('checked', isChecked).trigger('change');
            // به‌روزرسانی شمارنده
            updateGroupCounter(groupSlug);
        });

        // ========== تغییر هر چک‌باکس مجوز ==========
        $(document).on('change', '.permission-checkbox', function() {
            const groupSlug = $(this).data('group');
            if (groupSlug) {
                updateGroupCounter(groupSlug);
            }
        });

        // ========== به‌روزرسانی شمارنده و وضعیت چک‌باکس گروه ==========
        function updateGroupCounter(groupSlug) {
            const $groupCheckboxes = $(`.group-${groupSlug}`);
            const total = $groupCheckboxes.length;
            const checked = $groupCheckboxes.filter(':checked').length;

            // شمارنده
            $(`#counter_${groupSlug}`).find('span.fw-bold').text(checked);

            // badge
            const $badge = $(`#badge_${groupSlug}`);
            if (checked === total && total > 0) {
                $badge.removeClass('bg-primary').addClass('bg-success').html(`✓ ${checked}/${total}`);
            } else if (checked > 0) {
                $badge.removeClass('bg-primary').addClass('bg-warning').html(`${checked}/${total}`);
            } else {
                $badge.removeClass('bg-success bg-warning').addClass('bg-primary').html(total);
            }

            // همگام‌سازی چک‌باکس گروه
            const $groupCheckbox = $(`#group_${groupSlug}`);
            if ($groupCheckbox.length) {
                $groupCheckbox.prop('checked', checked === total && total > 0);
            }
        }

        // مقداردهی اولیه شمارنده‌ها و چک‌باکس‌های گروه
        function initCounters() {
            $('.group-checkbox').each(function() {
                const groupSlug = $(this).data('group');
                updateGroupCounter(groupSlug);
            });
        }
        initCounters();
    });
</script>
@endpush
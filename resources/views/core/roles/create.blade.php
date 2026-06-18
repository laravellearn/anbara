@extends('layouts.master')

@section('title', 'ایجاد نقش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">ایجاد نقش جدید</h5>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            @include('roles._form')
            <div class="card-footer text-end">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // ========== به‌روزرسانی شمارنده هر گروه ==========
        function updateGroupCounter(groupSlug) {
            const $groupCheckboxes = $(`.group-${groupSlug}`);
            const total = $groupCheckboxes.length;
            const checked = $groupCheckboxes.filter(':checked').length;
            $(`#counter_${groupSlug}`).find('span.fw-bold').text(checked);
            
            const $badge = $(`#badge_${groupSlug}`);
            if (checked === total && total > 0) {
                $badge.removeClass('bg-primary').addClass('bg-success').html(`✓ ${checked}/${total}`);
            } else if (checked > 0) {
                $badge.removeClass('bg-primary').addClass('bg-warning').html(`${checked}/${total}`);
            } else {
                $badge.removeClass('bg-success bg-warning').addClass('bg-primary').html(total);
            }
        }
        
        // ========== به‌روزرسانی همه شمارنده‌ها ==========
        function updateAllCounters() {
            $('.permission-checkbox').each(function() {
                const groupSlug = $(this).data('group');
                if (groupSlug) updateGroupCounter(groupSlug);
            });
        }
        
        // ========== انتخاب همه مجوزهای یک گروه ==========
        $('.select-group-btn').on('click', function() {
            const groupSlug = $(this).data('group');
            $(`.group-${groupSlug}`).prop('checked', true).trigger('change');
            updateGroupCounter(groupSlug);
        });
        
        // ========== حذف همه مجوزهای یک گروه ==========
        $('.deselect-group-btn').on('click', function() {
            const groupSlug = $(this).data('group');
            $(`.group-${groupSlug}`).prop('checked', false).trigger('change');
            updateGroupCounter(groupSlug);
        });
        
        // ========== انتخاب همه گروه‌ها ==========
        $('#selectAllGroups').on('click', function() {
            $('.permission-checkbox').prop('checked', true).trigger('change');
            updateAllCounters();
        });
        
        // ========== حذف همه گروه‌ها ==========
        $('#deselectAllGroups').on('click', function() {
            $('.permission-checkbox').prop('checked', false).trigger('change');
            updateAllCounters();
        });
        
        // ========== تغییر هر چک‌باکس ==========
        $(document).on('change', '.permission-checkbox', function() {
            const groupSlug = $(this).data('group');
            if (groupSlug) {
                updateGroupCounter(groupSlug);
            }
        });
        
        // مقداردهی اولیه شمارنده‌ها
        updateAllCounters();
    });
</script>
@endpush
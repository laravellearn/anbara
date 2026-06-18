<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">کد <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $role->code ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $role->title ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description ?? '') }}</textarea>
        </div>
        <div class="col-12"><hr><h6>مجوزها</h6></div>
        
        <!-- دکمه‌های انتخاب / حذف کلی -->
        <div class="col-12 mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary me-2" id="selectAllGroups"><i class="bx bx-check-double"></i> انتخاب همه</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllGroups"><i class="bx bx-x-circle"></i> حذف همه</button>
        </div>
        
        @foreach($permissions as $group => $perms)
            @php
                $slug = \Str::slug($group);
                $total = count($perms);
                $checkedCount = 0;
                foreach($perms as $perm) {
                    if (isset($role) && in_array($perm->id, $rolePermissions ?? [])) $checkedCount++;
                }
            @endphp
            <div class="col-12 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">{{ $group }}</h6>
                    <div>
                        <button type="button" class="btn btn-xs btn-outline-success select-group-btn" data-group="{{ $slug }}"><i class="bx bx-check"></i> انتخاب همه</button>
                        <button type="button" class="btn btn-xs btn-outline-danger deselect-group-btn" data-group="{{ $slug }}"><i class="bx bx-x"></i> حذف همه</button>
                        <span class="badge bg-primary ms-2" id="badge_{{ $slug }}">{{ $total }}</span>
                        <small id="counter_{{ $slug }}">(<span class="fw-bold">{{ $checkedCount }}</span>/{{ $total }})</small>
                    </div>
                </div>
                <div class="row">
                    @foreach($perms as $perm)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox group-{{ $slug }}" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}"
                                    data-group="{{ $slug }}"
                                    {{ (isset($role) && in_array($perm->id, $rolePermissions ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->title }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
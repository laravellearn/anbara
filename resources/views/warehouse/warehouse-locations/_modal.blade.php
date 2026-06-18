<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="locForm" method="POST" action="{{ route('admin.warehouse-locations.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">موقعیت</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">انبار <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="loc_warehouse" class="form-select" required>
                                <option value="">انتخاب کنید</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">والد</label>
                            <select name="parent_id" id="loc_parent" class="form-select">
                                <option value="">بدون والد</option>
                                {{-- می‌توانید موقعیت‌های همان انبار را به صورت داینامیک لود کنید --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="loc_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام</label>
                            <input type="text" name="name" id="loc_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نوع <span class="text-danger">*</span></label>
                            <select name="type" id="loc_type" class="form-select" required>
                                <option value="aisle">راهرو</option>
                                <option value="rack">قفسه</option>
                                <option value="shelf">طبقه</option>
                                <option value="bin">پالت</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ظرفیت</label>
                            <input type="number" name="capacity" id="loc_capacity" class="form-control" step="0.01">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="loc_active" checked>
                                <label class="form-check-label">فعال</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                </div>
            </div>
        </form>
    </div>
</div>
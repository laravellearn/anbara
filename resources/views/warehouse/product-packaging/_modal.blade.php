<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="packForm" method="POST" action="{{ route('admin.item-packaging.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">بسته‌بندی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">کالا <span class="text-danger">*</span></label>
                        <select name="product_id" id="pkg_product" class="form-select" required>
                            <option value="">انتخاب کنید</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نام بسته <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="pkg_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">واحد</label>
                        <select name="unit_id" id="pkg_unit" class="form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تعداد در بسته <span class="text-danger">*</span></label>
                        <input type="number" name="quantity_per_unit" id="pkg_qty" class="form-control" step="0.01" required>
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
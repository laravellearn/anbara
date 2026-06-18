<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="productForm" method="POST" action="{{ route('admin.items.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">کالا</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام کالا <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="prod_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" id="prod_sku" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">بارکد اصلی</label>
                            <input type="text" name="barcode" id="prod_barcode" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">دسته‌بندی</label>
                            <select name="category_id" id="prod_category" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واحد اندازه‌گیری</label>
                            <select name="unit_id" id="prod_unit" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">حداقل موجودی</label>
                            <input type="number" name="min_stock" id="prod_min" class="form-control" step="0.01" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">حداکثر موجودی</label>
                            <input type="number" name="max_stock" id="prod_max" class="form-control" step="0.01" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">توضیحات</label>
                            <textarea name="description" id="prod_desc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="prod_active" checked>
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
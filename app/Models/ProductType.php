<?php

namespace App\Models;

use App\Concerns\{BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, Auditable, LogsActivity};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, Auditable, LogsActivity;

    protected $fillable = ['tenant_id', 'company_id', 'title', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // رابطه با ویژگی‌ها از طریق جدول میانی product_type_attribute
    // رابطهٔ کلیدی: یک نوع کالا ویژگی‌های زیادی دارد
    public function attributes()
    {
        return $this->belongsToMany(
            ProductAttribute::class,          // مدل مقابل
            'product_type_attribute',         // جدول میانی
            'product_type_id',                // کلید خارجی این مدل در جدول میانی
            'product_attribute_id'            // کلید خارجی مدل مقابل
        )
            ->withPivot('is_required', 'sort_order')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

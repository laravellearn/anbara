<?php
namespace App\Models;
use App\Concerns\{BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, Auditable};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, Auditable;

    protected $fillable = ['tenant_id', 'company_id', 'title', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company() { return $this->belongsTo(Company::class); }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_type_attribute')
                    ->withPivot('is_required', 'sort_order')
                    ->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
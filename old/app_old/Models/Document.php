<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;
    use SoftDeletes;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'title',

        'file_name',
        'file_path',

        'mime_type',
        'file_size',

        'created_by',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id',
        'attachable_type', 'attachable_id',
        'file_name', 'file_path', 'file_size', 'mime_type',
        'description', 'uploaded_by',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = (int)$this->file_size;
        if ($bytes < 1024)       return "{$bytes} B";
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getIconClassAttribute(): string
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf'           => 'bx bx-file-pdf text-danger',
            'jpg','jpeg','png','gif' => 'bx bx-image text-success',
            'xls','xlsx'    => 'bx bx-spreadsheet text-success',
            'doc','docx'    => 'bx bx-file-blank text-primary',
            'zip','rar'     => 'bx bx-archive text-warning',
            default         => 'bx bx-paperclip text-secondary',
        };
    }
}

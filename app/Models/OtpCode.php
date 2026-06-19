<?php

namespace App\Models;

use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'mobile',
        'code',
        'expires_at',
        'attempts',
        'is_used',
        'ip'
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
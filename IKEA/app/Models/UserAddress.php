<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'full_name', 'phone',
        'address', 'city', 'province', 'zip_code', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oneLiner(): string
    {
        return "{$this->address}, {$this->city}, {$this->province} {$this->zip_code}";
    }
}
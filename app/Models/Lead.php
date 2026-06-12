<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'email',
        'download_token',
        'first_opted_at',
        'last_opted_at',
    ];

    protected function casts(): array
    {
        return [
            'first_opted_at' => 'datetime',
            'last_opted_at' => 'datetime',
        ];
    }
}

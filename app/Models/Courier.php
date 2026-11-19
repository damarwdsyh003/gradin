<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'level', 'joined_at', 'is_active'];

    protected function casts(): array
    {
        return ['level' => 'integer', 'joined_at' => 'date:Y-m-d', 'is_active' => 'boolean'];
    }
}

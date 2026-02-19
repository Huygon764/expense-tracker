<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}

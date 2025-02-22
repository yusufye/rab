<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded =  ['id'];

    protected $casts = [
        'split_to' => 'array',
    ];

    public function category() : BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function orderMak() : HasMany {
        return $this->hasMany(OrderMak::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTitle extends Model
{
    use HasFactory;

    protected $guarded =  ['id'];

    public function order() : BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function orderMak() : BelongsTo {
        return $this->belongsTo(OrderMak::class);
    }

    public function orderItem() : HasMany {
        return $this->hasMany(OrderItem::class);
    }
}

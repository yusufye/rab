<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderMak extends Model
{
    use HasFactory;

    protected $guarded =  ['id'];

    public function order() : BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function mak() : BelongsTo {
        return $this->belongsTo(Mak::class);
    }

    public function orderTitle() : HasMany {
        return $this->hasMany(OrderTitle::class);
    }
}

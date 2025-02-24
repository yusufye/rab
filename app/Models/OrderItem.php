<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded =  ['id'];

    public function orderTitle() : BelongsTo {
        return $this->belongsTo(OrderTitle::class);
    }

    public function orderChecklist() : HasMany {
        return $this->hasMany(OrderChecklist::class);
    }
   
}

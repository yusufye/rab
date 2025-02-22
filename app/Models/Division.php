<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user():HasMany{
        return $this->hasMany(User::class);
    }

    public function splitOrders()
    {
        return $this->hasMany(OrderMak::class, 'split_to');
    }
}
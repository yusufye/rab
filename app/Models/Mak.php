<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mak extends Model
{
    use HasFactory;
    protected $fillable = ['mak_code','mak_name'];
    public function orderMak() : HasMany {
        return $this->hasMany(OrderMak::class);
    }
}
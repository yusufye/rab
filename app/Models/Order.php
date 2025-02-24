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

    public function orderChecklist() : HasMany {
        return $this->hasMany(OrderChecklist::class);
    }

    
    public function approver1()
    {
        return $this->belongsTo(User::class, 'approved_1_by');
    }

    public function approver2()
    {
        return $this->belongsTo(User::class, 'approved_2_by');
    }

    public function approver3()
    {
        return $this->belongsTo(User::class, 'approved_3_by');
    }

}
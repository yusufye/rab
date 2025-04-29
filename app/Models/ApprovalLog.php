<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'approval_logs';

    public function order():BelongsTo{
        return $this->belongsTo(Order::class);
    }
    public function user():BelongsTo{
        return $this->belongsTo(User::class,'log_by','id');
    }
}

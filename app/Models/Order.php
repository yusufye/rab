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

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'approval_rejected_by','id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function splitToDivisions()
    {
        return Division::whereIn('id', $this->split_to)->get();
    }

    public function totalSplitPerDivision()
{
        return $this->orderMak()
            ->where('is_split', 1) // Hanya order yang di-split
            ->with('orderTitle.orderItem')
            ->get()
            ->flatMap(fn ($orderMak) => $orderMak->orderTitle)
            ->flatMap(fn ($orderTitle) => $orderTitle->orderItem)
            ->groupBy(fn ($orderItem) => $orderItem->orderTitle->orderMak->split_to) // Group by division_id
            ->map(fn ($items) => $items->sum('total_price')) // Sum total_price per division
            ->toArray(); // Convert ke array
    }

    public function totalOperational()
    {
        return $this->orderMak()
            ->where('is_split', 0) // Hanya yang tidak di-split
            ->with('orderTitle.orderItem')
            ->get()
            ->flatMap(fn ($orderMak) => $orderMak->orderTitle)
            ->flatMap(fn ($orderTitle) => $orderTitle->orderItem)
            ->sum('total_price'); // Hitung total harga operasional
    }

    public function totalPerMak()
    {
        return $this->orderMak()
        ->where('is_split', 0) // Hanya yang tidak di-split
        ->with('orderTitle.orderItem')
        ->get()
        ->groupBy(fn ($orderMak) => $orderMak->mak_id) // Kelompokkan berdasarkan mak_id dari order_maks
        ->map(fn ($orderMaks) => $orderMaks->flatMap(fn ($orderMak) => $orderMak->orderTitle)
            ->flatMap(fn ($orderTitle) => $orderTitle->orderItem)
            ->sum('total_price') // Total harga dari order_items
        )
        ->toArray(); // Konversi ke array
    }

    public function totalOperationalCost()
{
    return Order::where('rev', 0) // Ambil semua order dengan rev = 0
    ->where('job_number', $this->job_number) // Kelompokkan berdasarkan job_number
    ->whereHas('orderMak', function ($query) {
        $query->where('is_split', 0); // Hanya ambil orderMak yang tidak di-split
    })
    ->with(['orderMak.orderTitle.orderItem']) // Load semua relasi yang dibutuhkan
    ->get()
    ->pluck('orderMak') // Ambil semua orderMak terkait
    ->flatten() // Rata-kan hasil collection
    ->pluck('orderTitle') // Ambil semua orderTitle terkait
    ->flatten()
    ->pluck('orderItem') // Ambil semua orderItem terkait
    ->flatten()
    ->sum('total_price'); // Hitung total harga operasional
    }

}
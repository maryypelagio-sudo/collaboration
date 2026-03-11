<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'quantity',
        'unit',
        'min_stock_level',
        'image_path',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'is_borrowed',
        'current_maintenance',
        'is_rarely_used',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function lastActivityDate()
    {
        $lastLog = $this->stockLogs()->latest()->first();
        return $lastLog instanceof \App\Models\StockLog ? $lastLog->created_at : $this->created_at;
    }

    public function getIsRarelyUsedAttribute()
    {
        // For demonstration, let's say 30 days of inactivity
        return $this->lastActivityDate()->diffInDays(now()) > 30;
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function maintenanceLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function activeBorrowings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Borrowing::class)->where('status', 'borrowed');
    }

    public function getCurrentMaintenanceAttribute()
    {
        return $this->maintenanceLogs()->whereIn('status', ['pending', 'in_progress'])->first();
    }

    public function getIsBorrowedAttribute()
    {
        return $this->activeBorrowings()->exists();
    }
}

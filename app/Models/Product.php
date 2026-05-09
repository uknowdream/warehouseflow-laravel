<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use RecordsActivityLogs;

    protected $fillable = [
        'category_id', 'unit_id', 'code', 'name', 'qr_code', 'photo', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }

    public function stockOpnameLines(): HasMany
    {
        return $this->hasMany(StockOpnameLine::class);
    }

    public function totalStock(): float
    {
        return (float) $this->stockBalances()->sum('qty');
    }
}

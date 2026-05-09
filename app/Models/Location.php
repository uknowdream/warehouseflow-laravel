<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use RecordsActivityLogs;

    protected $fillable = [
        'warehouse_id', 'code', 'name', 'area', 'rack', 'qr_code', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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
}

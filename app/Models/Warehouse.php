<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use RecordsActivityLogs;

    protected $fillable = ['code', 'name', 'address', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }

    public function stockOpnameSessions(): HasMany
    {
        return $this->hasMany(StockOpnameSession::class);
    }
}

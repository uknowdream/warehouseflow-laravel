<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    use RecordsActivityLogs;

    protected $fillable = ['product_id', 'warehouse_id', 'location_id', 'qty'];

    protected $casts = ['qty' => 'decimal:2'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}

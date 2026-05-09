<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMove extends Model
{
    use RecordsActivityLogs;

    protected $fillable = [
        'transaction_no', 'transaction_type', 'product_id', 'warehouse_id',
        'location_id', 'qty_in', 'qty_out', 'reference_no', 'note', 'created_by'
    ];

    protected $casts = ['qty_in' => 'decimal:2', 'qty_out' => 'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}

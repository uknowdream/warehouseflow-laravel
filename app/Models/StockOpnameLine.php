<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameLine extends Model
{
    use RecordsActivityLogs;

    protected $fillable = [
        'opname_session_id', 'product_id', 'location_id',
        'system_qty', 'physical_qty', 'difference_qty',
        'status', 'photo', 'counted_by', 'counted_at', 'note'
    ];

    protected $casts = [
        'system_qty' => 'decimal:2',
        'physical_qty' => 'decimal:2',
        'difference_qty' => 'decimal:2',
        'counted_at' => 'datetime',
    ];

    public function session(): BelongsTo { return $this->belongsTo(StockOpnameSession::class, 'opname_session_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function counter(): BelongsTo { return $this->belongsTo(User::class, 'counted_by'); }
}

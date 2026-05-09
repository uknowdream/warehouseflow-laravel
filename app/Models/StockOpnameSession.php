<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpnameSession extends Model
{
    protected $fillable = [
        'opname_no', 'warehouse_id', 'status', 'started_at',
        'finished_at', 'created_by', 'approved_by', 'note'
    ];

    protected $casts = ['started_at' => 'datetime', 'finished_at' => 'datetime'];

    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function lines(): HasMany { return $this->hasMany(StockOpnameLine::class, 'opname_session_id'); }
}

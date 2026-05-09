<?php

namespace App\Services;

use App\Models\StockBalance;
use App\Models\StockMove;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function moveIn(int $productId, int $warehouseId, int $locationId, float $qty, string $type = 'IN', ?string $note = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($productId, $warehouseId, $locationId, $qty, $type, $note, $userId) {
            $balance = $this->getOrCreateBalance($productId, $warehouseId, $locationId);
            $balance->increment('qty', $qty);

            StockMove::create([
                'transaction_no' => $this->transactionNo($type),
                'transaction_type' => $type,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'qty_in' => $qty,
                'qty_out' => 0,
                'note' => $note,
                'created_by' => $userId,
            ]);
        });
    }

    public function moveOut(int $productId, int $warehouseId, int $locationId, float $qty, string $type = 'OUT', ?string $note = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($productId, $warehouseId, $locationId, $qty, $type, $note, $userId) {
            $balance = $this->getOrCreateBalance($productId, $warehouseId, $locationId);

            if ((float) $balance->qty < $qty) {
                throw new RuntimeException('Stok tidak mencukupi.');
            }

            $balance->decrement('qty', $qty);

            StockMove::create([
                'transaction_no' => $this->transactionNo($type),
                'transaction_type' => $type,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'qty_in' => 0,
                'qty_out' => $qty,
                'note' => $note,
                'created_by' => $userId,
            ]);
        });
    }

    public function transfer(int $productId, int $warehouseId, int $fromLocationId, int $toLocationId, float $qty, ?string $note = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($productId, $warehouseId, $fromLocationId, $toLocationId, $qty, $note, $userId) {
            $this->moveOut($productId, $warehouseId, $fromLocationId, $qty, 'TRANSFER_OUT', $note, $userId);
            $this->moveIn($productId, $warehouseId, $toLocationId, $qty, 'TRANSFER_IN', $note, $userId);
        });
    }

    public function adjustToPhysical(int $productId, int $warehouseId, int $locationId, float $physicalQty, ?string $note = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($productId, $warehouseId, $locationId, $physicalQty, $note, $userId) {
            $balance = $this->getOrCreateBalance($productId, $warehouseId, $locationId);
            $current = (float) $balance->qty;
            $diff = $physicalQty - $current;
            $balance->qty = $physicalQty;
            $balance->save();

            StockMove::create([
                'transaction_no' => $this->transactionNo('ADJ'),
                'transaction_type' => 'ADJUSTMENT',
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
                'qty_in' => $diff > 0 ? $diff : 0,
                'qty_out' => $diff < 0 ? abs($diff) : 0,
                'note' => $note,
                'created_by' => $userId,
            ]);
        });
    }

    private function getOrCreateBalance(int $productId, int $warehouseId, int $locationId): StockBalance
    {
        return StockBalance::firstOrCreate([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'location_id' => $locationId,
        ], ['qty' => 0]);
    }

    private function transactionNo(string $prefix): string
    {
        return strtoupper($prefix) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InventoryRecords
{
    public const CONNECTION = 'pgsql_remote';

    public const TABLES = [
        'inventory' => [
            'inventory',
            'item_id',
            [
                'name' => 'required|string|max:100',
                'quantity' => 'nullable|integer|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
                'orphanage_id' => 'nullable|integer|min:1',
                'category_id' => 'nullable|integer|min:1',
            ],
        ],
        'item-category' => [
            'item_category',
            'category_id',
            [
                'category_name' => 'required|string|max:100',
            ],
        ],
        'orphanage' => [
            'orphanage',
            'orphanage_id',
            [
                'organization_id' => 'nullable|integer|min:1',
                'name' => 'required|string|max:100',
                'address' => 'nullable|string|max:255',
            ],
        ],
        'stock-issue' => [
            'stock_issue',
            'issue_id',
            [
                'quantity' => 'required|integer|min:1',
                'issue_date' => 'required|date_format:Y-m-d',
                'purpose' => 'nullable|string|max:255',
                'item_id' => 'required|integer|min:1',
                'request_item_id' => 'nullable|integer|min:1',
            ],
        ],
        'stock-receipt' => [
            'stock_receipt',
            'receipt_id',
            [
                'quantity' => 'required|integer|min:1',
                'receipt_date' => 'required|date_format:Y-m-d',
                'supplier_id' => 'nullable|integer|min:1',
                'item_id' => 'required|integer|min:1',
                'received_from' => 'nullable|string|max:100',
            ],
        ],
        'supplier' => [
            'supplier',
            'supplier_id',
            [
                'supplier_name' => 'required|string|max:100',
                'contact_number' => 'nullable|string|max:20',
            ],
        ],
    ];

    public function db()
    {
        return DB::connection(self::CONNECTION);
    }

    public function definition(string $resource): array
    {
        abort_unless(isset(self::TABLES[$resource]), 404);

        return self::TABLES[$resource];
    }

    public function rules(string $resource): array
    {
        return $this->definition($resource)[2];
    }

    public function nextId(string $table, string $pkColumn): int
    {
        $max = $this->db()->table($table)->max($pkColumn);

        return ((int) $max) + 1;
    }

    public function snapshot(): array
    {
        $conn = $this->db();
        $result = [];

        foreach (self::TABLES as $resource => [$table, $key]) {
            $result[$resource] = $conn->table($table)->orderBy($key, 'desc')->get();
        }

        return $result;
    }

    public function findOrCreateCategory(string $categoryName): int
    {
        $conn = $this->db();
        $categoryName = mb_substr(trim($categoryName), 0, 100);
        $existing = $conn->table('item_category')
            ->whereRaw('LOWER(category_name) = LOWER(?)', [$categoryName])
            ->first();

        if ($existing) {
            return (int) $existing->category_id;
        }

        return (int) $conn->table('item_category')->insertGetId([
            'category_name' => $categoryName,
        ], 'category_id');
    }

    public function findOrCreateOrphanage(string $locationName, ?string $address = null): int
    {
        $conn = $this->db();
        $locationName = mb_substr(trim($locationName), 0, 100);
        $existing = $conn->table('orphanage')
            ->whereRaw('LOWER(name) = LOWER(?)', [$locationName])
            ->first();

        if ($existing) {
            return (int) $existing->orphanage_id;
        }

        return (int) $conn->table('orphanage')->insertGetId([
            'organization_id' => 1,
            'name' => $locationName,
            'address' => $address ? mb_substr(trim($address), 0, 255) : 'HopeHaven Orphanage Campus',
        ], 'orphanage_id');
    }

    public function findOrCreateSupplier(string $supplierName, ?string $contactNumber = null): int
    {
        $conn = $this->db();
        $supplierName = mb_substr(trim($supplierName), 0, 100);
        $existing = $conn->table('supplier')
            ->whereRaw('LOWER(supplier_name) = LOWER(?)', [$supplierName])
            ->first();

        if ($existing) {
            if ($contactNumber) {
                $conn->table('supplier')
                    ->where('supplier_id', $existing->supplier_id)
                    ->update(['contact_number' => mb_substr(trim($contactNumber), 0, 20)]);
            }

            return (int) $existing->supplier_id;
        }

        return (int) $conn->table('supplier')->insertGetId([
            'supplier_name' => $supplierName,
            'contact_number' => $contactNumber ? mb_substr(trim($contactNumber), 0, 20) : null,
        ], 'supplier_id');
    }

    public function findOrCreateItem(string $name, ?string $categoryName = 'Medical', ?string $locationName = 'Ward Alpha Cupboard', int $initialQty = 0, int $minStock = 5): object
    {
        $conn = $this->db();
        $name = mb_substr(trim($name), 0, 100);
        $categoryId = $this->findOrCreateCategory($categoryName ?: 'Medical');
        $orphanageId = $this->findOrCreateOrphanage($locationName ?: 'Ward Alpha Cupboard');

        $existing = $conn->table('inventory')
            ->whereRaw('LOWER(name) = LOWER(?)', [$name])
            ->where('orphanage_id', $orphanageId)
            ->first();

        if (! $existing) {
            $existing = $conn->table('inventory')
                ->whereRaw('LOWER(name) = LOWER(?)', [$name])
                ->first();
        }

        if ($existing) {
            return $existing;
        }

        $itemId = (int) $conn->table('inventory')->insertGetId([
            'name' => $name,
            'quantity' => max(0, $initialQty),
            'minimum_stock' => max(0, $minStock),
            'orphanage_id' => $orphanageId,
            'category_id' => $categoryId,
        ], 'item_id');

        return $conn->table('inventory')->where('item_id', $itemId)->first();
    }
}

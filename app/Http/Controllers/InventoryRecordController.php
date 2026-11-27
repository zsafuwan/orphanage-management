<?php

namespace App\Http\Controllers;

use App\Services\InventoryRecords;
use Illuminate\Http\Request;

class InventoryRecordController extends Controller
{
    public function snapshot(InventoryRecords $records)
    {
        return response()->json($records->snapshot())->header('Cache-Control', 'no-store');
    }

    public function addItem(Request $request, InventoryRecords $records)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'supplier_name' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $conn = $records->db();
            $categoryId = $records->findOrCreateCategory($data['category'] ?? 'Medical');
            $orphanageId = $records->findOrCreateOrphanage($data['location'] ?? 'Central Storeroom');
            $supplierId = $records->findOrCreateSupplier(
                $data['supplier_name'] ?? 'HopeHaven Central Storeroom',
                $data['contact_number'] ?? null
            );

            $name = mb_substr(trim($data['name']), 0, 100);
            $qty = (int) $data['quantity'];
            $minStock = isset($data['minimum_stock']) ? (int) $data['minimum_stock'] : 5;

            $existing = $conn->table('inventory')
                ->whereRaw('LOWER(name) = LOWER(?)', [$name])
                ->where('orphanage_id', $orphanageId)
                ->first();

            if ($existing) {
                $itemId = (int) $existing->item_id;
                $conn->table('inventory')->where('item_id', $itemId)->update([
                    'quantity' => $qty,
                    'minimum_stock' => $minStock,
                    'category_id' => $categoryId,
                ]);
            } else {
                $itemId = (int) $conn->table('inventory')->insertGetId([
                    'name' => $name,
                    'quantity' => $qty,
                    'minimum_stock' => $minStock,
                    'orphanage_id' => $orphanageId,
                    'category_id' => $categoryId,
                ], 'item_id');
            }

            $receipt = null;
            if ($qty > 0) {
                $receiptId = (int) $conn->table('stock_receipt')->insertGetId([
                    'quantity' => $qty,
                    'receipt_date' => now()->toDateString(),
                    'supplier_id' => $supplierId,
                    'item_id' => $itemId,
                    'received_from' => mb_substr(trim($data['supplier_name'] ?? 'Initial Stock Entry'), 0, 100),
                ], 'receipt_id');
                $receipt = $conn->table('stock_receipt')->where('receipt_id', $receiptId)->first();
            }

            return response()->json([
                'saved' => true,
                'item' => $conn->table('inventory')->where('item_id', $itemId)->first(),
                'receipt' => $receipt,
            ]);
        });
    }

    public function adjustCount(Request $request, InventoryRecords $records)
    {
        $data = $request->validate([
            'item_id' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $conn = $records->db();
            $item = null;
            if (! empty($data['item_id'])) {
                $item = $conn->table('inventory')->where('item_id', (int) $data['item_id'])->first();
            }
            if (! $item && ! empty($data['name'])) {
                $item = $conn->table('inventory')->whereRaw('LOWER(name) = LOWER(?)', [trim($data['name'])])->first();
            }
            if (! $item) {
                $item = $records->findOrCreateItem($data['name'] ?? 'Ward Supply Item', 'Medical', 'Ward Alpha Cupboard', (int) $data['quantity'], 5);
            }

            $oldQty = (int) ($item->quantity ?? 0);
            $newQty = (int) $data['quantity'];
            $diff = $newQty - $oldQty;

            $conn->table('inventory')->where('item_id', $item->item_id)->update([
                'quantity' => $newQty,
            ]);

            if ($diff > 0) {
                $supplierId = $records->findOrCreateSupplier('HopeHaven Central Storeroom');
                $conn->table('stock_receipt')->insertGetId([
                    'quantity' => $diff,
                    'receipt_date' => now()->toDateString(),
                    'supplier_id' => $supplierId,
                    'item_id' => $item->item_id,
                    'received_from' => mb_substr(trim($data['reason'] ?? 'Stock Count Adjustment'), 0, 100),
                ], 'receipt_id');
            } elseif ($diff < 0) {
                $conn->table('stock_issue')->insertGetId([
                    'quantity' => abs($diff),
                    'issue_date' => now()->toDateString(),
                    'purpose' => mb_substr(trim($data['reason'] ?? 'Stock Count Adjustment'), 0, 255),
                    'item_id' => $item->item_id,
                    'request_item_id' => null,
                ], 'issue_id');
            }

            return response()->json([
                'saved' => true,
                'item' => $conn->table('inventory')->where('item_id', $item->item_id)->first(),
            ]);
        });
    }

    public function receiveStock(Request $request, InventoryRecords $records)
    {
        $data = $request->validate([
            'item_id' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'supplier_name' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'received_from' => 'nullable|string|max:100',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $conn = $records->db();
            $item = null;
            if (! empty($data['item_id'])) {
                $item = $conn->table('inventory')->where('item_id', (int) $data['item_id'])->first();
            }
            if (! $item && ! empty($data['name'])) {
                $item = $records->findOrCreateItem(
                    $data['name'],
                    $data['category'] ?? 'Medical',
                    $data['location'] ?? 'Central Storeroom',
                    0,
                    5
                );
            }
            abort_unless($item, 422, 'Please specify a valid inventory item.');

            $supplierName = $data['supplier_name'] ?? $data['received_from'] ?? 'Verified Merchant';
            $supplierId = $records->findOrCreateSupplier($supplierName, $data['contact_number'] ?? null);

            $qty = (int) $data['quantity'];
            $newTotal = ((int) ($item->quantity ?? 0)) + $qty;
            $conn->table('inventory')->where('item_id', $item->item_id)->update([
                'quantity' => $newTotal,
            ]);

            $receiptId = (int) $conn->table('stock_receipt')->insertGetId([
                'quantity' => $qty,
                'receipt_date' => now()->toDateString(),
                'supplier_id' => $supplierId,
                'item_id' => $item->item_id,
                'received_from' => mb_substr(trim($data['received_from'] ?? $supplierName), 0, 100),
            ], 'receipt_id');

            return response()->json([
                'saved' => true,
                'item' => $conn->table('inventory')->where('item_id', $item->item_id)->first(),
                'receipt' => $conn->table('stock_receipt')->where('receipt_id', $receiptId)->first(),
            ]);
        });
    }

    public function issueStock(Request $request, InventoryRecords $records)
    {
        $data = $request->validate([
            'item_id' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255',
            'request_item_id' => 'nullable|integer|min:1',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $conn = $records->db();
            $item = null;
            if (! empty($data['item_id'])) {
                $item = $conn->table('inventory')->where('item_id', (int) $data['item_id'])->first();
            }
            if (! $item && ! empty($data['name'])) {
                $item = $conn->table('inventory')->whereRaw('LOWER(name) = LOWER(?)', [trim($data['name'])])->first();
            }
            if (! $item) {
                $item = $records->findOrCreateItem(
                    $data['name'] ?? 'Ward Supply Item',
                    $data['category'] ?? 'Medical',
                    $data['location'] ?? 'Ward Alpha Cupboard',
                    (int) $data['quantity'],
                    3
                );
            }

            $qty = (int) $data['quantity'];
            $newTotal = max(0, ((int) ($item->quantity ?? 0)) - $qty);
            $conn->table('inventory')->where('item_id', $item->item_id)->update([
                'quantity' => $newTotal,
            ]);

            $issueId = (int) $conn->table('stock_issue')->insertGetId([
                'quantity' => $qty,
                'issue_date' => now()->toDateString(),
                'purpose' => mb_substr(trim($data['purpose'] ?? 'Issued to Residential Ward'), 0, 255),
                'item_id' => $item->item_id,
                'request_item_id' => $data['request_item_id'] ?? null,
            ], 'issue_id');

            return response()->json([
                'saved' => true,
                'item' => $conn->table('inventory')->where('item_id', $item->item_id)->first(),
                'issue' => $conn->table('stock_issue')->where('issue_id', $issueId)->first(),
            ]);
        });
    }

    public function registerSupplier(Request $request, InventoryRecords $records)
    {
        $data = $request->validate([
            'supplier_name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'category_name' => 'nullable|string|max:100',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            if (! empty($data['category_name'])) {
                $records->findOrCreateCategory($data['category_name']);
            }
            $supplierId = $records->findOrCreateSupplier($data['supplier_name'], $data['contact_number'] ?? null);
            $row = $records->db()->table('supplier')->where('supplier_id', $supplierId)->first();

            return response()->json(['record' => $row]);
        });
    }

    public function save(Request $request, string $resource, InventoryRecords $records)
    {
        [$table, $key] = $records->definition($resource);
        $data = $request->validate($records->rules($resource));
        $id = $request->validate(['record_id' => 'nullable|integer|min:1'])['record_id'] ?? null;

        $row = $records->db()->transaction(function () use ($records, $table, $key, $data, $id) {
            $conn = $records->db();
            if ($key && $id) {
                abort_unless($conn->table($table)->where($key, $id)->exists(), 404);
                $conn->table($table)->where($key, $id)->update($data);
            } else {
                $id = (int) $conn->table($table)->insertGetId($data, $key);
            }

            return $conn->table($table)->where($key, $id)->first();
        });

        return response()->json(['record' => $row]);
    }
}

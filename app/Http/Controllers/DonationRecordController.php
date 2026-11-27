<?php

namespace App\Http\Controllers;

use App\Services\DonationRecords;
use Illuminate\Http\Request;

class DonationRecordController extends Controller
{
    public function snapshot(DonationRecords $records)
    {
        return response()->json($records->snapshot())->header('Cache-Control', 'no-store');
    }

    public function registerDonor(Request $request, DonationRecords $records)
    {
        $data = $request->validate([
            'Donor_name' => 'required|string|max:100',
            'Contact_number' => 'nullable|string|max:100',
            'Email' => 'nullable|string|max:100',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $donorId = $records->findOrCreateDonor(
                $data['Donor_name'],
                $data['Contact_number'] ?? null,
                isset($data['Email']) && filter_var($data['Email'], FILTER_VALIDATE_EMAIL) ? $data['Email'] : ($data['Email'] ?? null)
            );
            $row = $records->db()->table('donor')->where('Donor_Id', $donorId)->first();

            return response()->json(['record' => $row]);
        });
    }

    public function recordContribution(Request $request, DonationRecords $records)
    {
        $data = $request->validate([
            'Donor_Id' => 'nullable|integer|min:1',
            'Donor_name' => 'nullable|string|max:100',
            'Contact_number' => 'nullable|string|max:100',
            'Email' => 'nullable|string|max:100',
            'Donation_Date' => 'nullable|date_format:Y-m-d',
            'Donation_Type' => 'required|string|max:50',
            'Amount' => 'nullable|numeric|min:0',
            'Item_name' => 'nullable|string|max:100',
            'Quantity' => 'nullable|integer|min:1',
            'Orphanage_Id' => 'nullable|integer|min:1',
            'Receipt_reference' => 'nullable|string|max:50',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $conn = $records->db();
            $date = $data['Donation_Date'] ?? now()->toDateString();

            if (! empty($data['Donor_Id']) && $conn->table('donor')->where('Donor_Id', $data['Donor_Id'])->exists()) {
                $donorId = (int) $data['Donor_Id'];
            } else {
                $donorId = $records->findOrCreateDonor(
                    $data['Donor_name'] ?? 'Anonymous Community Donor',
                    $data['Contact_number'] ?? null,
                    $data['Email'] ?? null
                );
            }

            $donationId = (int) $conn->table('donation')->insertGetId([
                'Donation_Date' => $date,
                'Donation_Type' => mb_substr(trim($data['Donation_Type']), 0, 50),
                'Amount' => isset($data['Amount']) ? round((float) $data['Amount'], 2) : 0.00,
                'Donor_Id' => $donorId,
            ], 'Donation_Id');

            $receiptRef = $records->nextReceiptReference($data['Receipt_reference'] ?? null);
            $receiptId = (int) $conn->table('donation_receipt')->insertGetId([
                'Receipt_date' => $date,
                'Receipt_reference' => $receiptRef,
                'Donation_Id' => $donationId,
            ], 'Receipt_id');

            $itemName = ! empty($data['Item_name'])
                ? mb_substr(trim($data['Item_name']), 0, 100)
                : 'General Orphanage Welfare Fund';
            $quantity = ! empty($data['Quantity']) ? (int) $data['Quantity'] : 1;

            $itemId = (int) $conn->table('donation_item')->insertGetId([
                'Item_name' => $itemName,
                'Quantity' => $quantity,
                'Donation_id' => $donationId,
            ], 'Donation_Item_Id');

            $allocationId = (int) $conn->table('donation_allocation')->insertGetId([
                'Donation_Item_Id' => $itemId,
                'Orphanage_Id' => ! empty($data['Orphanage_Id']) ? (int) $data['Orphanage_Id'] : 1,
                'Allocation_date' => $date,
                'Quantity_Allocated' => $quantity,
            ], 'Allocation_Id');

            return response()->json([
                'saved' => true,
                'donor' => $conn->table('donor')->where('Donor_Id', $donorId)->first(),
                'donation' => $conn->table('donation')->where('Donation_Id', $donationId)->first(),
                'receipt' => $conn->table('donation_receipt')->where('Receipt_id', $receiptId)->first(),
                'item' => $conn->table('donation_item')->where('Donation_Item_Id', $itemId)->first(),
                'allocation' => $conn->table('donation_allocation')->where('Allocation_Id', $allocationId)->first(),
            ]);
        });
    }

    public function save(Request $request, string $resource, DonationRecords $records)
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
                $id = $conn->table($table)->insertGetId($data, $key);
            }

            return $conn->table($table)->where($key, $id)->first();
        });

        return response()->json(['record' => $row]);
    }
}

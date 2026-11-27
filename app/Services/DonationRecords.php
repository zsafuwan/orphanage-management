<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DonationRecords
{
    public const CONNECTION = 'mariadb';

    public const TABLES = [
        'donor' => [
            'donor',
            'Donor_Id',
            [
                'Donor_name' => 'required|string|max:100',
                'Contact_number' => 'nullable|string|max:20',
                'Email' => 'nullable|email|max:100',
            ],
        ],
        'donation' => [
            'donation',
            'Donation_Id',
            [
                'Donation_Date' => 'required|date_format:Y-m-d',
                'Donation_Type' => 'required|string|max:50',
                'Amount' => 'nullable|numeric|min:0',
                'Donor_Id' => 'required|integer',
            ],
        ],
        'donation-item' => [
            'donation_item',
            'Donation_Item_Id',
            [
                'Item_name' => 'required|string|max:100',
                'Quantity' => 'required|integer|min:1',
                'Donation_id' => 'required|integer',
            ],
        ],
        'donation-allocation' => [
            'donation_allocation',
            'Allocation_Id',
            [
                'Donation_Item_Id' => 'required|integer',
                'Orphanage_Id' => 'required|integer|min:1',
                'Allocation_date' => 'required|date_format:Y-m-d',
                'Quantity_Allocated' => 'required|integer|min:1',
            ],
        ],
        'donation-receipt' => [
            'donation_receipt',
            'Receipt_id',
            [
                'Receipt_date' => 'required|date_format:Y-m-d',
                'Receipt_reference' => 'required|string|max:50',
                'Donation_Id' => 'required|integer',
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

    public function snapshot(): array
    {
        $conn = $this->db();
        $result = [];

        foreach (self::TABLES as $resource => [$table, $key]) {
            $result[$resource] = $conn->table($table)->orderBy($key, 'desc')->get();
        }

        return $result;
    }

    public function findOrCreateDonor(string $name, ?string $contact = null, ?string $email = null): int
    {
        $conn = $this->db();
        $name = mb_substr(trim($name), 0, 100);
        $rawContact = $contact ? trim($contact) : '';
        $parsedEmail = $email ? trim($email) : null;
        $parsedPhone = null;

        if ($rawContact !== '') {
            if (filter_var($rawContact, FILTER_VALIDATE_EMAIL)) {
                $parsedEmail = $parsedEmail ?: $rawContact;
            } else {
                $parsedPhone = mb_substr($rawContact, 0, 20);
            }
        }
        if ($parsedEmail) {
            $parsedEmail = mb_substr(strtolower($parsedEmail), 0, 100);
        }

        $existing = null;
        if ($parsedEmail) {
            $existing = $conn->table('donor')->where('Email', $parsedEmail)->first();
        }
        if (! $existing) {
            $existing = $conn->table('donor')->where('Donor_name', $name)->first();
        }

        if ($existing) {
            $updates = ['Donor_name' => $name];
            if ($parsedPhone) {
                $updates['Contact_number'] = $parsedPhone;
            }
            if ($parsedEmail) {
                $updates['Email'] = $parsedEmail;
            }
            $conn->table('donor')->where('Donor_Id', $existing->Donor_Id)->update($updates);

            return (int) $existing->Donor_Id;
        }

        return (int) $conn->table('donor')->insertGetId([
            'Donor_name' => $name,
            'Contact_number' => $parsedPhone,
            'Email' => $parsedEmail,
        ], 'Donor_Id');
    }

    public function nextReceiptReference(?string $preferred = null): string
    {
        $conn = $this->db();
        if ($preferred) {
            $clean = mb_substr(trim($preferred), 0, 50);
            if (! $conn->table('donation_receipt')->where('Receipt_reference', $clean)->exists()) {
                return $clean;
            }
        }

        $year = now()->format('Y');
        $prefix = "RCP-{$year}-";
        $rows = $conn->table('donation_receipt')
            ->where('Receipt_reference', 'like', $prefix.'%')
            ->pluck('Receipt_reference');

        $maxSeq = 880;
        foreach ($rows as $ref) {
            $suffix = substr((string) $ref, strlen($prefix));
            if (is_numeric($suffix)) {
                $maxSeq = max($maxSeq, (int) $suffix);
            }
        }

        do {
            $maxSeq++;
            $candidate = $prefix.str_pad((string) $maxSeq, 4, '0', STR_PAD_LEFT);
        } while ($conn->table('donation_receipt')->where('Receipt_reference', $candidate)->exists());

        return $candidate;
    }
}

<?php

namespace App\Services;

use RuntimeException;

class WelfareRequestRecords
{
    public const TABLES = [
        'approval-history' => [
            'dbo.Approval_History',
            'Approval_Id',
            [
                'Decision' => 'required|string|max:50',
                'Decision_Date' => 'nullable|string',
                'User_Id' => 'required|integer|min:1',
                'Request_Id' => 'required|integer|min:1',
            ],
        ],
        'inventory' => [
            'dbo.Inventory',
            'Item_Id',
            [
                'Name' => 'required|string|max:150',
                'Quantity' => 'required|integer|min:0',
                'Minimum_Stock' => 'required|integer|min:0',
                'Orphanage_Id' => 'required|integer|min:1',
                'Category_Id' => 'nullable|integer|min:1',
            ],
        ],
        'notification' => [
            'dbo.Notification',
            'Notification_Id',
            [
                'Message' => 'required|string|max:500',
                'Status' => 'required|string|max:30',
                'User_Id' => 'required|integer|min:1',
            ],
        ],
        'orphanage' => [
            'dbo.Orphanage',
            'Orphanage_Id',
            [
                'Name' => 'required|string|max:150',
                'Address' => 'required|string|max:255',
            ],
        ],
        'request-item' => [
            'dbo.Request_Item',
            'Request_Item_Id',
            [
                'Request_Id' => 'required|integer|min:1',
                'Item_Id' => 'required|integer|min:1',
                'Quantity_Requested' => 'required|integer|min:1',
                'Quantity_Approved' => 'nullable|integer|min:0',
                'Quantity_Fulfilled' => 'nullable|integer|min:0',
            ],
        ],
        'user' => [
            'dbo.[User]',
            'User_Id',
            [
                'Username' => 'required|string|max:100',
                'Role' => 'required|string|max:50',
            ],
        ],
        'welfare-request' => [
            'dbo.Welfare_Request',
            'Request_Id',
            [
                'Request_Date' => 'nullable|string',
                'Status' => 'required|string|max:30',
                'Orphanage_Id' => 'required|integer|min:1',
                'User_Id' => 'required|integer|min:1',
            ],
        ],
    ];

    public function connectionString(): string
    {
        $host = config('database.connections.sqlsrv.host', env('SQLSRV_HOST', '10.213.198.248'));
        $port = config('database.connections.sqlsrv.port', env('SQLSRV_PORT', '1433'));
        $database = config('database.connections.sqlsrv.database', env('SQLSRV_DATABASE', 'QUERY_DB'));
        $username = config('database.connections.sqlsrv.username', env('SQLSRV_USERNAME', 'QueryGroup_User'));
        $password = config('database.connections.sqlsrv.password', env('SQLSRV_PASSWORD', 'QG_StrongPass2026!'));

        return "Server={$host},{$port};Database={$database};User Id={$username};Password={$password};TrustServerCertificate=True;";
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

    public function executePayload(array $payload): array
    {
        $payload['connStr'] = $this->connectionString();
        $runnerPath = storage_path('app/sqlsrv_runner.ps1');
        $tempFile = storage_path('app/sqlsrv_payload_'.uniqid('', true).'.json');

        file_put_contents($tempFile, json_encode($payload, JSON_UNESCAPED_UNICODE));

        try {
            $cmd = sprintf(
                'powershell -NoProfile -ExecutionPolicy Bypass -File %s -PayloadFile %s 2>&1',
                escapeshellarg($runnerPath),
                escapeshellarg($tempFile)
            );
            $output = shell_exec($cmd);
        } finally {
            @unlink($tempFile);
        }

        if (! $output) {
            throw new RuntimeException('No response from SQL Server runner.');
        }

        $jsonStart = strpos($output, '{');
        if ($jsonStart !== false) {
            $output = substr($output, $jsonStart);
        }

        $decoded = json_decode(trim($output), true);
        if (! is_array($decoded) || empty($decoded['ok'])) {
            $msg = is_array($decoded) && isset($decoded['error']) ? $decoded['error'] : trim($output);
            throw new RuntimeException('SQL Server query error: '.$msg);
        }

        return $decoded;
    }

    public function query(string $sql, array $params = []): array
    {
        $res = $this->executePayload([
            'sql' => $sql,
            'params' => (object) $params,
            'mode' => 'query',
        ]);

        $rows = $res['result'] ?? [];
        if (isset($rows['Approval_Id']) || isset($rows['Item_Id']) || isset($rows['Notification_Id']) || isset($rows['Orphanage_Id']) || isset($rows['Request_Item_Id']) || isset($rows['User_Id']) || isset($rows['Request_Id'])) {
            return [$rows];
        }

        return is_array($rows) ? array_values($rows) : [];
    }

    public function scalar(string $sql, array $params = []): mixed
    {
        $res = $this->executePayload([
            'sql' => $sql,
            'params' => (object) $params,
            'mode' => 'scalar',
        ]);

        return $res['result'] ?? null;
    }

    public function statement(string $sql, array $params = []): int
    {
        $res = $this->executePayload([
            'sql' => $sql,
            'params' => (object) $params,
            'mode' => 'nonquery',
        ]);

        return (int) ($res['result'] ?? 0);
    }

    public function snapshot(): array
    {
        $batch = [];
        foreach (self::TABLES as $resource => [$table, $key]) {
            $batch[] = [
                'key' => $resource,
                'sql' => "SELECT * FROM {$table} ORDER BY {$key} DESC",
                'params' => (object) [],
                'mode' => 'query',
            ];
        }

        $res = $this->executePayload(['batch' => $batch]);
        $results = $res['results'] ?? [];

        foreach (self::TABLES as $resource => [$table, $key]) {
            $val = $results[$resource] ?? [];
            if (is_array($val) && isset($val[$key])) {
                $results[$resource] = [$val];
            } elseif (! is_array($val)) {
                $results[$resource] = [];
            } else {
                $results[$resource] = array_values($val);
            }
        }

        return $results;
    }

    public function findOrCreateUser(string $username, string $role = 'Staff'): array
    {
        $username = mb_substr(trim($username), 0, 100);
        $role = mb_substr(trim($role), 0, 50);

        $rows = $this->query(
            'SELECT TOP 1 * FROM dbo.[User] WHERE LOWER(Username) = LOWER(@Username)',
            ['Username' => $username]
        );

        if (! empty($rows)) {
            return $rows[0];
        }

        $rows = $this->query(
            'INSERT INTO dbo.[User] (Username, Role) OUTPUT INSERTED.* VALUES (@Username, @Role)',
            ['Username' => $username, 'Role' => $role]
        );

        return $rows[0];
    }

    public function findOrCreateOrphanage(string $name, string $address = 'HopeHaven Campus'): array
    {
        $name = mb_substr(trim($name), 0, 150);
        $address = mb_substr(trim($address), 0, 255);

        $rows = $this->query(
            'SELECT TOP 1 * FROM dbo.Orphanage WHERE LOWER(Name) = LOWER(@Name)',
            ['Name' => $name]
        );

        if (! empty($rows)) {
            return $rows[0];
        }

        $rows = $this->query(
            'INSERT INTO dbo.Orphanage (Name, Address) OUTPUT INSERTED.* VALUES (@Name, @Address)',
            ['Name' => $name, 'Address' => $address]
        );

        return $rows[0];
    }

    public function findOrCreateInventoryItem(string $name, int $orphanageId, int $initialQty = 20, int $minStock = 5): array
    {
        $name = mb_substr(trim($name), 0, 150);

        $rows = $this->query(
            'SELECT TOP 1 * FROM dbo.Inventory WHERE LOWER(Name) = LOWER(@Name)',
            ['Name' => $name]
        );

        if (! empty($rows)) {
            return $rows[0];
        }

        $rows = $this->query(
            'INSERT INTO dbo.Inventory (Name, Quantity, Minimum_Stock, Orphanage_Id, Category_Id) OUTPUT INSERTED.* VALUES (@Name, @Quantity, @Minimum_Stock, @Orphanage_Id, NULL)',
            [
                'Name' => $name,
                'Quantity' => max(0, $initialQty),
                'Minimum_Stock' => max(0, $minStock),
                'Orphanage_Id' => $orphanageId,
            ]
        );

        return $rows[0];
    }

    public function createNotification(string $message, int $userId, string $status = 'Unread'): array
    {
        $rows = $this->query(
            'INSERT INTO dbo.Notification (Message, Status, User_Id) OUTPUT INSERTED.* VALUES (@Message, @Status, @User_Id)',
            [
                'Message' => mb_substr(trim($message), 0, 500),
                'Status' => mb_substr(trim($status), 0, 30),
                'User_Id' => $userId,
            ]
        );

        return $rows[0] ?? [];
    }
}

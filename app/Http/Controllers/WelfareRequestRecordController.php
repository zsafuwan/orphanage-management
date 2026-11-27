<?php

namespace App\Http\Controllers;

use App\Services\WelfareRequestRecords;
use Illuminate\Http\Request;

class WelfareRequestRecordController extends Controller
{
    public function snapshot(WelfareRequestRecords $records)
    {
        return response()->json($records->snapshot())->header('Cache-Control', 'no-store');
    }

    public function submitRequest(Request $request, WelfareRequestRecords $records)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'beneficiary_name' => 'nullable|string|max:150',
            'beneficiary_id' => 'nullable|string|max:50',
            'dorm' => 'nullable|string|max:150',
            'urgency' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'justification' => 'nullable|string|max:400',
            'requested_by' => 'nullable|string|max:100',
            'line_items' => 'nullable|array',
            'line_items.*.item' => 'required_with:line_items|string|max:150',
            'line_items.*.qty' => 'nullable|integer|min:1',
            'line_items.*.cost' => 'nullable|numeric|min:0',
        ]);

        $requestedBy = $data['requested_by'] ?? 'Nurse Mary (Active Caregiver)';
        $user = $records->findOrCreateUser($requestedBy, 'Staff');
        $userId = (int) $user['User_Id'];

        $dormName = $data['dorm'] ?? 'Rumah Anak Yatim Kasih';
        $orphanage = $records->findOrCreateOrphanage($dormName, 'HopeHaven Campus');
        $orphanageId = (int) $orphanage['Orphanage_Id'];

        $now = now()->format('Y-m-d H:i:s');
        $reqRows = $records->query(
            'INSERT INTO dbo.Welfare_Request (Request_Date, Status, Orphanage_Id, User_Id) OUTPUT INSERTED.* VALUES (@Request_Date, @Status, @Orphanage_Id, @User_Id)',
            [
                'Request_Date' => $now,
                'Status' => 'Pending',
                'Orphanage_Id' => $orphanageId,
                'User_Id' => $userId,
            ]
        );
        $welfareRequest = $reqRows[0];
        $requestId = (int) $welfareRequest['Request_Id'];

        $lineItems = $data['line_items'] ?? [];
        if (empty($lineItems)) {
            $lineItems = [
                [
                    'item' => $data['title'],
                    'qty' => max(1, (int) round((float) ($data['cost'] ?? 1))),
                    'cost' => (float) ($data['cost'] ?? 1),
                ],
            ];
        }

        $createdItems = [];
        foreach ($lineItems as $li) {
            $itemName = trim($li['item']);
            $qtyReq = isset($li['qty']) ? max(1, (int) $li['qty']) : max(1, (int) round((float) ($li['cost'] ?? 1)));
            $invItem = $records->findOrCreateInventoryItem($itemName, $orphanageId, max(20, $qtyReq), 5);
            $itemId = (int) $invItem['Item_Id'];

            $riRows = $records->query(
                'INSERT INTO dbo.Request_Item (Request_Id, Item_Id, Quantity_Requested, Quantity_Approved, Quantity_Fulfilled) OUTPUT INSERTED.* VALUES (@Request_Id, @Item_Id, @Quantity_Requested, 0, 0)',
                [
                    'Request_Id' => $requestId,
                    'Item_Id' => $itemId,
                    'Quantity_Requested' => $qtyReq,
                ]
            );
            if (! empty($riRows)) {
                $createdItems[] = $riRows[0];
            }
        }

        $beneficiary = $data['beneficiary_name'] ?? 'Resident Child';
        $category = $data['category'] ?? 'Medical / Surgical';
        $urgency = $data['urgency'] ?? 'High';
        $justification = $data['justification'] ?? 'Submitted by caregiver on duty.';
        $notifMsg = "WR #{$requestId}: {$data['title']} | Child: {$beneficiary} | Cat: {$category} | Urgency: {$urgency} | Note: {$justification}";
        $notification = $records->createNotification($notifMsg, $userId, 'Unread');

        return response()->json([
            'saved' => true,
            'request' => $welfareRequest,
            'request_items' => $createdItems,
            'notification' => $notification,
        ]);
    }

    public function decideRequest(Request $request, WelfareRequestRecords $records)
    {
        $data = $request->validate([
            'request_id' => 'required|integer|min:1',
            'decision' => 'required|string|max:50',
            'approved_by' => 'nullable|string|max:100',
            'funding_source' => 'nullable|string|max:150',
            'remarks' => 'nullable|string|max:350',
        ]);

        $requestId = (int) $data['request_id'];
        $isApproved = stripos($data['decision'], 'Reject') === false && stripos($data['decision'], 'Decline') === false && stripos($data['decision'], 'Return') === false;
        $status = $isApproved ? 'Approved' : 'Rejected';
        $decisionLabel = $isApproved ? 'Approved' : 'Rejected';

        $adminName = $data['approved_by'] ?? 'Sister Grace (Executive Director)';
        $adminUser = $records->findOrCreateUser($adminName, 'Admin');
        $adminUserId = (int) $adminUser['User_Id'];

        $records->statement(
            'UPDATE dbo.Welfare_Request SET Status = @Status WHERE Request_Id = @Request_Id',
            ['Status' => $status, 'Request_Id' => $requestId]
        );

        if ($isApproved) {
            $records->statement(
                'UPDATE dbo.Request_Item SET Quantity_Approved = Quantity_Requested, Quantity_Fulfilled = Quantity_Requested WHERE Request_Id = @Request_Id',
                ['Request_Id' => $requestId]
            );
        } else {
            $records->statement(
                'UPDATE dbo.Request_Item SET Quantity_Approved = 0, Quantity_Fulfilled = 0 WHERE Request_Id = @Request_Id',
                ['Request_Id' => $requestId]
            );
        }

        $now = now()->format('Y-m-d H:i:s');
        $approvalRows = $records->query(
            'INSERT INTO dbo.Approval_History (Decision, Decision_Date, User_Id, Request_Id) OUTPUT INSERTED.* VALUES (@Decision, @Decision_Date, @User_Id, @Request_Id)',
            [
                'Decision' => $decisionLabel,
                'Decision_Date' => $now,
                'User_Id' => $adminUserId,
                'Request_Id' => $requestId,
            ]
        );

        $funding = $data['funding_source'] ?? 'Orphanage Welfare Fund';
        $remarks = $data['remarks'] ?? ($isApproved ? 'Approved for beneficiary welfare.' : 'Declined after executive review.');
        $notifMsg = "WR #{$requestId} {$decisionLabel} by {$adminName} | Fund: {$funding} | Remarks: {$remarks}";
        $notification = $records->createNotification($notifMsg, $adminUserId, 'Unread');

        $updatedReq = $records->query('SELECT * FROM dbo.Welfare_Request WHERE Request_Id = @Request_Id', ['Request_Id' => $requestId]);

        return response()->json([
            'saved' => true,
            'request' => $updatedReq[0] ?? null,
            'approval' => $approvalRows[0] ?? null,
            'notification' => $notification,
        ]);
    }

    public function receiveRequest(Request $request, WelfareRequestRecords $records)
    {
        $data = $request->validate([
            'request_id' => 'required|integer|min:1',
            'received_by' => 'nullable|string|max:100',
        ]);

        $requestId = (int) $data['request_id'];
        $records->statement(
            'UPDATE dbo.Welfare_Request SET Status = @Status WHERE Request_Id = @Request_Id',
            ['Status' => 'Received', 'Request_Id' => $requestId]
        );
        $records->statement(
            'UPDATE dbo.Request_Item SET Quantity_Approved = CASE WHEN Quantity_Approved = 0 THEN Quantity_Requested ELSE Quantity_Approved END, Quantity_Fulfilled = Quantity_Requested WHERE Request_Id = @Request_Id',
            ['Request_Id' => $requestId]
        );

        $items = $records->query('SELECT * FROM dbo.Request_Item WHERE Request_Id = @Request_Id', ['Request_Id' => $requestId]);
        foreach ($items as $ri) {
            $records->statement(
                'UPDATE dbo.Inventory SET Quantity = Quantity + @Qty WHERE Item_Id = @Item_Id',
                [
                    'Qty' => (int) ($ri['Quantity_Requested'] ?? 0),
                    'Item_Id' => (int) $ri['Item_Id'],
                ]
            );
        }

        $user = $records->findOrCreateUser($data['received_by'] ?? 'Nurse Mary', 'Staff');
        $notif = $records->createNotification("WR #{$requestId} supplies received and added to inventory.", (int) $user['User_Id'], 'Unread');

        return response()->json([
            'saved' => true,
            'notification' => $notif,
        ]);
    }

    public function registerUser(Request $request, WelfareRequestRecords $records)
    {
        $data = $request->validate([
            'username' => 'required|string|max:100',
            'role' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:150',
            'admin_id' => 'nullable|string|max:50',
        ]);

        $displayUsername = trim($data['username']);
        if (! empty($data['admin_id']) && stripos($displayUsername, $data['admin_id']) === false) {
            $displayUsername = mb_substr("{$displayUsername} ({$data['admin_id']})", 0, 100);
        }

        $role = $data['role'] ?? 'Admin';
        $user = $records->findOrCreateUser($displayUsername, $role);

        $notifMsg = "New {$role} account provisioned: {$displayUsername}".(! empty($data['email']) ? " ({$data['email']})" : '');
        $notification = $records->createNotification($notifMsg, (int) $user['User_Id'], 'Unread');

        return response()->json([
            'saved' => true,
            'user' => $user,
            'notification' => $notification,
        ]);
    }

    public function markNotificationsRead(Request $request, WelfareRequestRecords $records)
    {
        $notificationId = $request->input('notification_id');
        if ($notificationId) {
            $records->statement(
                "UPDATE dbo.Notification SET Status = 'Read' WHERE Notification_Id = @Id",
                ['Id' => (int) $notificationId]
            );
        } else {
            $records->statement("UPDATE dbo.Notification SET Status = 'Read' WHERE Status <> 'Read'");
        }

        return response()->json(['saved' => true]);
    }

    public function save(Request $request, string $resource, WelfareRequestRecords $records)
    {
        [$table, $key] = $records->definition($resource);
        $data = $request->validate($records->rules($resource));
        $id = $request->validate(['record_id' => 'nullable|integer|min:1'])['record_id'] ?? null;

        if ($resource === 'approval-history' && empty($data['Decision_Date'])) {
            $data['Decision_Date'] = now()->format('Y-m-d H:i:s');
        }
        if ($resource === 'welfare-request' && empty($data['Request_Date'])) {
            $data['Request_Date'] = now()->format('Y-m-d H:i:s');
        }
        if ($resource === 'request-item') {
            $data['Quantity_Approved'] = $data['Quantity_Approved'] ?? 0;
            $data['Quantity_Fulfilled'] = $data['Quantity_Fulfilled'] ?? 0;
        }

        if ($id) {
            $sets = [];
            $params = ['Record_Id' => (int) $id];
            foreach ($data as $col => $val) {
                $sets[] = "{$col} = @{$col}";
                $params[$col] = $val;
            }
            $setClause = implode(', ', $sets);
            $rows = $records->query(
                "UPDATE {$table} SET {$setClause} OUTPUT INSERTED.* WHERE {$key} = @Record_Id",
                $params
            );
            abort_if(empty($rows), 404);

            return response()->json(['record' => $rows[0]]);
        }

        $cols = array_keys($data);
        $colList = implode(', ', $cols);
        $paramList = implode(', ', array_map(fn ($c) => '@'.$c, $cols));

        $rows = $records->query(
            "INSERT INTO {$table} ({$colList}) OUTPUT INSERTED.* VALUES ({$paramList})",
            $data
        );

        return response()->json(['record' => $rows[0] ?? null]);
    }
}

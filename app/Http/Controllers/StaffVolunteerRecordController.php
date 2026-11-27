<?php

namespace App\Http\Controllers;

use App\Services\StaffVolunteerRecords;
use Illuminate\Http\Request;

class StaffVolunteerRecordController extends Controller
{
    public function snapshot(StaffVolunteerRecords $records)
    {
        return response()->json($records->snapshot())->header('Cache-Control', 'no-store');
    }

    public function personnel(Request $request, StaffVolunteerRecords $records)
    {
        $data = $request->validate([
            'type' => 'nullable|string|in:staff,volunteer,Permanent Staff,Active Volunteer',
            'Staff_Id' => 'nullable|integer|min:1',
            'Volunteer_Id' => 'nullable|integer|min:1',
            'Name' => 'required|string|max:100',
            'Position' => 'nullable|string|max:50',
            'Department' => 'nullable|string|max:50',
            'Contact_Number' => 'nullable|string|max:20',
            'Email' => 'nullable|email|max:100',
            'Duty_name' => 'nullable|string|max:100',
            'Qualification_name' => 'nullable|string|max:100',
            'Year_Obtained' => 'nullable|digits:4',
            'Training_Name' => 'nullable|string|max:120',
            'Trainer_Name' => 'nullable|string|max:100',
            'Event_Id' => 'nullable|integer|min:1',
        ]);

        $isVolunteer = in_array($data['type'] ?? 'staff', ['volunteer', 'Active Volunteer'], true);

        return $records->db()->transaction(function () use ($records, $data, $isVolunteer) {
            if ($isVolunteer) {
                $volunteerId = $records->findOrCreateVolunteer(
                    $data['Name'],
                    $data['Email'] ?? null,
                    $data['Contact_Number'] ?? null
                );

                if (! empty($data['Training_Name'])) {
                    $trainingId = $records->findOrCreateTraining($data['Training_Name'], null, $data['Trainer_Name'] ?? null);
                    $records->recordTraining($trainingId, null, $volunteerId);
                }

                if (! empty($data['Event_Id'])) {
                    $eventExists = $records->db()->table('event')->where('Event_ID', $data['Event_Id'])->exists();
                    if ($eventExists) {
                        $regExists = $records->db()->table('event_registration')
                            ->where('Event_Id', $data['Event_Id'])
                            ->where('Volunteer_Id', $volunteerId)
                            ->exists();
                        if (! $regExists) {
                            $records->db()->table('event_registration')->insert([
                                'Event_Id' => $data['Event_Id'],
                                'Volunteer_Id' => $volunteerId,
                                'Registered_At' => now()->toDateTimeString(),
                                'Status' => 'Registered',
                            ]);
                        }
                    }
                }

                $row = $records->db()->table('volunteer')->where('Volunteer_Id', $volunteerId)->first();

                return response()->json(['type' => 'volunteer', 'record' => $row]);
            }

            $position = $data['Position'] ?? null;
            if (! $position && ! empty($data['Department'])) {
                $position = $data['Department'];
            } elseif ($position && ! empty($data['Department']) && ! str_contains($position, $data['Department'])) {
                $position = mb_substr($position.' · '.$data['Department'], 0, 50);
            }

            if (! empty($data['Staff_Id']) && $records->db()->table('staff')->where('Staff_Id', $data['Staff_Id'])->exists()) {
                $staffId = (int) $data['Staff_Id'];
                $updates = ['Name' => mb_substr(trim($data['Name']), 0, 100)];
                if ($position) {
                    $updates['Position'] = mb_substr(trim($position), 0, 50);
                }
                if (! empty($data['Contact_Number'])) {
                    $updates['Contact_Number'] = mb_substr(trim($data['Contact_Number']), 0, 20);
                }
                $records->db()->table('staff')->where('Staff_Id', $staffId)->update($updates);
            } else {
                $staffId = $records->findOrCreateStaff(
                    $data['Name'],
                    $position ?: 'Caregiver Staff',
                    $data['Contact_Number'] ?? null
                );
            }

            if (! empty($data['Duty_name'])) {
                $dutyId = $records->findOrCreateDuty($data['Duty_name']);
                $records->assignDuty($staffId, $dutyId, now()->toDateString());
            }

            if (! empty($data['Qualification_name'])) {
                $qualId = $records->findOrCreateQualification($data['Qualification_name']);
                $records->attachStaffQualification($staffId, $qualId, isset($data['Year_Obtained']) ? (int) $data['Year_Obtained'] : null);
            }

            if (! empty($data['Training_Name'])) {
                $trainingId = $records->findOrCreateTraining($data['Training_Name'], null, $data['Trainer_Name'] ?? null);
                $records->recordTraining($trainingId, $staffId, null);
            }

            $row = $records->db()->table('staff')->where('Staff_Id', $staffId)->first();

            return response()->json(['type' => 'staff', 'record' => $row]);
        });
    }

    public function shift(Request $request, StaffVolunteerRecords $records)
    {
        $data = $request->validate([
            'Duty_name' => 'required|string|max:100',
            'Staff_Id' => 'nullable|integer|min:1',
            'Staff_Name' => 'nullable|string|max:100',
            'Volunteer_Id' => 'nullable|integer|min:1',
            'Volunteer_Name' => 'nullable|string|max:100',
            'Assignment_date' => 'nullable|date_format:Y-m-d',
            'Location' => 'nullable|string|max:150',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $date = $data['Assignment_date'] ?? now()->toDateString();

            if (! empty($data['Volunteer_Id']) || ! empty($data['Volunteer_Name'])) {
                $volunteerId = ! empty($data['Volunteer_Id']) && $records->db()->table('volunteer')->where('Volunteer_Id', $data['Volunteer_Id'])->exists()
                    ? (int) $data['Volunteer_Id']
                    : $records->findOrCreateVolunteer($data['Volunteer_Name'] ?? 'Volunteer');
                $eventId = $records->findOrCreateEvent($data['Duty_name'], 'Volunteer Shift', $data['Location'] ?? 'HopeHaven Campus', $date, 'Scheduled');
                $records->registerVolunteerForEvent($eventId, $volunteerId, 'Scheduled');

                return response()->json([
                    'saved' => true,
                    'Volunteer_Id' => $volunteerId,
                    'Event_ID' => $eventId,
                    'Assignment_date' => $date,
                ]);
            }

            $staffId = null;
            if (! empty($data['Staff_Id']) && $records->db()->table('staff')->where('Staff_Id', $data['Staff_Id'])->exists()) {
                $staffId = (int) $data['Staff_Id'];
            } elseif (! empty($data['Staff_Name'])) {
                $staffId = $records->findOrCreateStaff($data['Staff_Name']);
            } else {
                $firstStaff = $records->db()->table('staff')->orderBy('Staff_Id')->first();
                $staffId = $firstStaff ? (int) $firstStaff->Staff_Id : $records->findOrCreateStaff('Nurse Mary Jenkins', 'Senior Caregiver');
            }

            $dutyId = $records->findOrCreateDuty($data['Duty_name']);
            $records->assignDuty($staffId, $dutyId, $date);

            return response()->json([
                'saved' => true,
                'Staff_Id' => $staffId,
                'Duty_Id' => $dutyId,
                'Assignment_date' => $date,
            ]);
        });
    }

    public function attendance(Request $request, StaffVolunteerRecords $records)
    {
        $data = $request->validate([
            'Volunteer_Id' => 'nullable|integer|min:1',
            'Volunteer_Name' => 'nullable|string|max:100',
            'Activity' => 'required|string|max:120',
            'Attendance_Date' => 'nullable|date_format:Y-m-d',
            'Event_Id' => 'nullable|integer|min:1',
            'Event_Title' => 'nullable|string|max:120',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $volunteerId = null;
            if (! empty($data['Volunteer_Id']) && $records->db()->table('volunteer')->where('Volunteer_Id', $data['Volunteer_Id'])->exists()) {
                $volunteerId = (int) $data['Volunteer_Id'];
            } elseif (! empty($data['Volunteer_Name'])) {
                $volunteerId = $records->findOrCreateVolunteer($data['Volunteer_Name']);
            } else {
                $firstVol = $records->db()->table('volunteer')->orderBy('Volunteer_Id')->first();
                $volunteerId = $firstVol ? (int) $firstVol->Volunteer_Id : $records->findOrCreateVolunteer('Elena Rostova');
            }

            $eventId = null;
            if (! empty($data['Event_Id']) && $records->db()->table('event')->where('Event_ID', $data['Event_Id'])->exists()) {
                $eventId = (int) $data['Event_Id'];
            } elseif (! empty($data['Event_Title'])) {
                $eventId = $records->findOrCreateEvent($data['Event_Title'], 'Volunteer Program', 'HopeHaven Campus', $data['Attendance_Date'] ?? now()->toDateString(), 'Active');
            }

            if ($eventId) {
                $records->registerVolunteerForEvent($eventId, $volunteerId, 'Attended');
            }

            $attendanceId = $records->db()->table('volunteer_attendance')->insertGetId([
                'Volunteer_Id' => $volunteerId,
                'Activity' => mb_substr(trim($data['Activity']), 0, 120),
                'Attendance_Date' => $data['Attendance_Date'] ?? now()->toDateString(),
                'Event_Id' => $eventId,
            ], 'Attendance_Id');

            $row = $records->db()->table('volunteer_attendance')->where('Attendance_Id', $attendanceId)->first();

            return response()->json(['record' => $row]);
        });
    }

    public function credential(Request $request, StaffVolunteerRecords $records)
    {
        $data = $request->validate([
            'Staff_Id' => 'nullable|integer|min:1',
            'Staff_Name' => 'nullable|string|max:100',
            'Volunteer_Id' => 'nullable|integer|min:1',
            'Volunteer_Name' => 'nullable|string|max:100',
            'Qualification_name' => 'nullable|string|max:100',
            'Year_Obtained' => 'nullable|digits:4',
            'Training_Name' => 'nullable|string|max:120',
            'Training_Date' => 'nullable|date_format:Y-m-d',
            'Trainer_Name' => 'nullable|string|max:100',
            'Completion_Date' => 'nullable|date_format:Y-m-d',
            'Status' => 'nullable|string|max:30',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $staffId = null;
            $volunteerId = null;

            if (! empty($data['Staff_Id']) && $records->db()->table('staff')->where('Staff_Id', $data['Staff_Id'])->exists()) {
                $staffId = (int) $data['Staff_Id'];
            } elseif (! empty($data['Staff_Name'])) {
                $staffId = $records->findOrCreateStaff($data['Staff_Name']);
            }

            if (! empty($data['Volunteer_Id']) && $records->db()->table('volunteer')->where('Volunteer_Id', $data['Volunteer_Id'])->exists()) {
                $volunteerId = (int) $data['Volunteer_Id'];
            } elseif (! empty($data['Volunteer_Name'])) {
                $volunteerId = $records->findOrCreateVolunteer($data['Volunteer_Name']);
            }

            if (! $staffId && ! $volunteerId) {
                $firstStaff = $records->db()->table('staff')->orderBy('Staff_Id')->first();
                $staffId = $firstStaff ? (int) $firstStaff->Staff_Id : $records->findOrCreateStaff('Nurse Mary Jenkins', 'Senior Caregiver');
            }

            $result = [];

            if (! empty($data['Qualification_name']) && $staffId) {
                $qualId = $records->findOrCreateQualification($data['Qualification_name']);
                $sqId = $records->attachStaffQualification(
                    $staffId,
                    $qualId,
                    isset($data['Year_Obtained']) ? (int) $data['Year_Obtained'] : (int) now()->format('Y')
                );
                $result['staff_qualification_id'] = $sqId;
            }

            if (! empty($data['Training_Name'])) {
                $trainingId = $records->findOrCreateTraining(
                    $data['Training_Name'],
                    $data['Training_Date'] ?? now()->toDateString(),
                    $data['Trainer_Name'] ?? 'HopeHaven Compliance Board'
                );
                $trId = $records->recordTraining(
                    $trainingId,
                    $staffId,
                    $volunteerId,
                    $data['Completion_Date'] ?? now()->toDateString(),
                    $data['Status'] ?? 'Completed'
                );
                $result['training_record_id'] = $trId;
            }

            return response()->json(['saved' => true, ...$result]);
        });
    }

    public function event(Request $request, StaffVolunteerRecords $records)
    {
        $data = $request->validate([
            'Event_ID' => 'nullable|integer|min:1',
            'Title' => 'required|string|max:120',
            'Description' => 'nullable|string',
            'Event_Type' => 'nullable|string|max:50',
            'Location' => 'nullable|string|max:150',
            'Start_At' => 'nullable|date',
            'End_At' => 'nullable|date',
            'Capacity' => 'nullable|integer|min:1',
            'Status' => 'nullable|string|max:30',
            'Volunteer_Id' => 'nullable|integer|min:1',
            'Volunteer_Name' => 'nullable|string|max:100',
        ]);

        return $records->db()->transaction(function () use ($records, $data) {
            $payload = [
                'Title' => mb_substr(trim($data['Title']), 0, 120),
                'Description' => $data['Description'] ?? null,
                'Event_Type' => $data['Event_Type'] ?? 'Community Care',
                'Location' => $data['Location'] ?? 'HopeHaven Main Campus',
                'Start_At' => ! empty($data['Start_At']) ? date('Y-m-d H:i:s', strtotime($data['Start_At'])) : now()->addDays(7)->toDateTimeString(),
                'End_At' => ! empty($data['End_At']) ? date('Y-m-d H:i:s', strtotime($data['End_At'])) : null,
                'Capacity' => $data['Capacity'] ?? 25,
                'Status' => $data['Status'] ?? 'Upcoming',
                'User_Id' => null,
                'Orphanage_ID' => null,
            ];

            if (! empty($data['Event_ID']) && $records->db()->table('event')->where('Event_ID', $data['Event_ID'])->exists()) {
                $eventId = (int) $data['Event_ID'];
                $records->db()->table('event')->where('Event_ID', $eventId)->update($payload);
            } else {
                $eventId = (int) $records->db()->table('event')->insertGetId($payload, 'Event_ID');
            }

            $volunteerId = null;
            if (! empty($data['Volunteer_Id']) && $records->db()->table('volunteer')->where('Volunteer_Id', $data['Volunteer_Id'])->exists()) {
                $volunteerId = (int) $data['Volunteer_Id'];
            } elseif (! empty($data['Volunteer_Name'])) {
                $volunteerId = $records->findOrCreateVolunteer($data['Volunteer_Name']);
            }

            if ($volunteerId) {
                $exists = $records->db()->table('event_registration')
                    ->where('Event_Id', $eventId)
                    ->where('Volunteer_Id', $volunteerId)
                    ->exists();
                if (! $exists) {
                    $records->db()->table('event_registration')->insert([
                        'Event_Id' => $eventId,
                        'Volunteer_Id' => $volunteerId,
                        'Registered_At' => now()->toDateTimeString(),
                        'Status' => 'Registered',
                    ]);
                }
            }

            $row = $records->db()->table('event')->where('Event_ID', $eventId)->first();

            return response()->json(['record' => $row]);
        });
    }

    public function save(Request $request, string $resource, StaffVolunteerRecords $records)
    {
        [$table, $key] = $records->definition($resource);
        $data = $request->validate($records->rules($resource));
        $id = $request->validate(['record_id' => 'nullable|integer|min:1'])['record_id'] ?? null;

        $row = $records->db()->transaction(function () use ($records, $table, $key, $data, $id) {
            $conn = $records->db();
            if ($key && $id) {
                abort_unless($conn->table($table)->where($key, $id)->exists(), 404);
                $conn->table($table)->where($key, $id)->update($data);
            } elseif ($key) {
                $id = $conn->table($table)->insertGetId($data, $key);
            } elseif ($table === 'duty_assignment') {
                $records->assignDuty((int) $data['Staff_Id'], (int) $data['Duty_Id'], $data['Assignment_date'] ?? null);

                return $data;
            } elseif ($table === 'event_registration') {
                $exists = $conn->table('event_registration')
                    ->where('Event_Id', $data['Event_Id'])
                    ->where('Volunteer_Id', $data['Volunteer_Id'])
                    ->exists();
                if ($exists) {
                    $conn->table('event_registration')
                        ->where('Event_Id', $data['Event_Id'])
                        ->where('Volunteer_Id', $data['Volunteer_Id'])
                        ->update(['Status' => $data['Status'] ?? 'Registered']);
                } else {
                    $conn->table('event_registration')->insert([
                        'Event_Id' => $data['Event_Id'],
                        'Volunteer_Id' => $data['Volunteer_Id'],
                        'Registered_At' => $data['Registered_At'] ?? now()->toDateTimeString(),
                        'Status' => $data['Status'] ?? 'Registered',
                    ]);
                }

                return $data;
            }

            return $conn->table($table)->where($key, $id)->first();
        });

        return response()->json(['record' => $row]);
    }
}

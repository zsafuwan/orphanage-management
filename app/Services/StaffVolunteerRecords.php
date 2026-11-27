<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StaffVolunteerRecords
{
    public const CONNECTION = 'mysql';

    public const TABLES = [
        'staff' => [
            'staff',
            'Staff_Id',
            [
                'Name' => 'required|string|max:100',
                'Position' => 'nullable|string|max:50',
                'Contact_Number' => 'nullable|string|max:20',
                'Orphanage_Id' => 'nullable|integer',
                'User_Id' => 'nullable|integer',
            ],
        ],
        'volunteer' => [
            'volunteer',
            'Volunteer_Id',
            [
                'Name' => 'required|string|max:100',
                'Contact_Number' => 'nullable|string|max:20',
                'Email' => 'nullable|email|max:100',
            ],
        ],
        'duty' => [
            'duty',
            'Duty_Id',
            [
                'Duty_name' => 'required|string|max:100',
            ],
        ],
        'duty-assignment' => [
            'duty_assignment',
            null,
            [
                'Staff_Id' => 'required|integer',
                'Duty_Id' => 'required|integer',
                'Assignment_date' => 'nullable|date_format:Y-m-d',
            ],
        ],
        'qualification' => [
            'qualification',
            'Qualification_id',
            [
                'Qualification_name' => 'required|string|max:100',
            ],
        ],
        'staff-qualification' => [
            'staff_qualification',
            'Staff_Qualification_Id',
            [
                'Year_Obtained' => 'nullable|digits:4',
                'Qualification_Id' => 'required|integer',
                'Staff_Id' => 'required|integer',
            ],
        ],
        'training' => [
            'training',
            'Training_Id',
            [
                'Training_Name' => 'required|string|max:120',
                'Training_Date' => 'nullable|date_format:Y-m-d',
                'Trainer_Name' => 'nullable|string|max:100',
            ],
        ],
        'training-record' => [
            'training_record',
            'Training_Record_Id',
            [
                'Training_Id' => 'required|integer',
                'Staff_Id' => 'nullable|integer',
                'Volunteer_Id' => 'nullable|integer',
                'Completion_Date' => 'nullable|date_format:Y-m-d',
                'Status' => 'nullable|string|max:30',
            ],
        ],
        'volunteer-attendance' => [
            'volunteer_attendance',
            'Attendance_Id',
            [
                'Activity' => 'required|string|max:120',
                'Attendance_Date' => 'nullable|date_format:Y-m-d',
                'Event_Id' => 'nullable|integer',
                'Volunteer_Id' => 'nullable|integer',
            ],
        ],
        'event' => [
            'event',
            'Event_ID',
            [
                'Title' => 'required|string|max:120',
                'Description' => 'nullable|string',
                'Event_Type' => 'nullable|string|max:50',
                'Location' => 'nullable|string|max:150',
                'Start_At' => 'nullable|date',
                'End_At' => 'nullable|date',
                'Capacity' => 'nullable|integer|min:1',
                'Status' => 'nullable|string|max:30',
                'User_Id' => 'nullable|integer',
                'Orphanage_ID' => 'nullable|integer',
            ],
        ],
        'event-registration' => [
            'event_registration',
            null,
            [
                'Event_Id' => 'required|integer',
                'Volunteer_Id' => 'required|integer',
                'Registered_At' => 'nullable|date',
                'Status' => 'nullable|string|max:30',
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
            $query = $conn->table($table);
            if ($key) {
                $query->orderBy($key, 'desc');
            } elseif ($table === 'duty_assignment') {
                $query->orderBy('Assignment_date', 'desc')->orderBy('Duty_Id', 'desc');
            } elseif ($table === 'event_registration') {
                $query->orderBy('Registered_At', 'desc');
            }
            $result[$resource] = $query->get();
        }

        return $result;
    }

    public function findOrCreateStaff(string $name, ?string $position = null, ?string $phone = null): int
    {
        $conn = $this->db();
        $name = mb_substr(trim($name), 0, 100);
        $existing = $conn->table('staff')->where('Name', $name)->first();

        if ($existing) {
            $updates = [];
            if ($position !== null && $position !== '') {
                $updates['Position'] = mb_substr(trim($position), 0, 50);
            }
            if ($phone !== null && $phone !== '') {
                $updates['Contact_Number'] = mb_substr(trim($phone), 0, 20);
            }
            if (! empty($updates)) {
                $conn->table('staff')->where('Staff_Id', $existing->Staff_Id)->update($updates);
            }

            return (int) $existing->Staff_Id;
        }

        return (int) $conn->table('staff')->insertGetId([
            'Name' => $name,
            'Position' => $position ? mb_substr(trim($position), 0, 50) : 'Caregiver Staff',
            'Contact_Number' => $phone ? mb_substr(trim($phone), 0, 20) : null,
            'Orphanage_Id' => null,
            'User_Id' => null,
        ], 'Staff_Id');
    }

    public function findOrCreateVolunteer(string $name, ?string $email = null, ?string $phone = null): int
    {
        $conn = $this->db();
        $name = mb_substr(trim($name), 0, 100);
        $email = $email ? mb_substr(strtolower(trim($email)), 0, 100) : null;
        $phone = $phone ? mb_substr(trim($phone), 0, 20) : null;

        $existing = null;
        if ($email) {
            $existing = $conn->table('volunteer')->where('Email', $email)->first();
        }
        if (! $existing) {
            $existing = $conn->table('volunteer')->where('Name', $name)->first();
        }

        if ($existing) {
            $updates = ['Name' => $name];
            if ($phone) {
                $updates['Contact_Number'] = $phone;
            }
            if ($email && (! $existing->Email || $existing->Email === $email)) {
                $updates['Email'] = $email;
            }
            $conn->table('volunteer')->where('Volunteer_Id', $existing->Volunteer_Id)->update($updates);

            return (int) $existing->Volunteer_Id;
        }

        return (int) $conn->table('volunteer')->insertGetId([
            'Name' => $name,
            'Contact_Number' => $phone,
            'Email' => $email,
        ], 'Volunteer_Id');
    }

    public function findOrCreateDuty(string $dutyName): int
    {
        $conn = $this->db();
        $dutyName = mb_substr(trim($dutyName), 0, 100);
        $existing = $conn->table('duty')->where('Duty_name', $dutyName)->first();
        if ($existing) {
            return (int) $existing->Duty_Id;
        }

        return (int) $conn->table('duty')->insertGetId([
            'Duty_name' => $dutyName,
        ], 'Duty_Id');
    }

    public function assignDuty(int $staffId, int $dutyId, ?string $date = null): void
    {
        $conn = $this->db();
        $date = $date ?: now()->toDateString();
        $exists = $conn->table('duty_assignment')
            ->where('Staff_Id', $staffId)
            ->where('Duty_Id', $dutyId)
            ->exists();

        if ($exists) {
            $conn->table('duty_assignment')
                ->where('Staff_Id', $staffId)
                ->where('Duty_Id', $dutyId)
                ->update(['Assignment_date' => $date]);
        } else {
            $conn->table('duty_assignment')->insert([
                'Staff_Id' => $staffId,
                'Duty_Id' => $dutyId,
                'Assignment_date' => $date,
            ]);
        }
    }

    public function findOrCreateQualification(string $qualificationName): int
    {
        $conn = $this->db();
        $qualificationName = mb_substr(trim($qualificationName), 0, 100);
        $existing = $conn->table('qualification')->where('Qualification_name', $qualificationName)->first();
        if ($existing) {
            return (int) $existing->Qualification_id;
        }

        return (int) $conn->table('qualification')->insertGetId([
            'Qualification_name' => $qualificationName,
        ], 'Qualification_id');
    }

    public function attachStaffQualification(int $staffId, int $qualificationId, ?int $year = null): int
    {
        $conn = $this->db();
        $year = $year ?: (int) now()->format('Y');
        $existing = $conn->table('staff_qualification')
            ->where('Staff_Id', $staffId)
            ->where('Qualification_Id', $qualificationId)
            ->first();

        if ($existing) {
            $conn->table('staff_qualification')
                ->where('Staff_Qualification_Id', $existing->Staff_Qualification_Id)
                ->update(['Year_Obtained' => $year]);

            return (int) $existing->Staff_Qualification_Id;
        }

        return (int) $conn->table('staff_qualification')->insertGetId([
            'Staff_Id' => $staffId,
            'Qualification_Id' => $qualificationId,
            'Year_Obtained' => $year,
        ], 'Staff_Qualification_Id');
    }

    public function findOrCreateTraining(string $trainingName, ?string $date = null, ?string $trainer = null): int
    {
        $conn = $this->db();
        $trainingName = mb_substr(trim($trainingName), 0, 120);
        $existing = $conn->table('training')->where('Training_Name', $trainingName)->first();
        if ($existing) {
            return (int) $existing->Training_Id;
        }

        return (int) $conn->table('training')->insertGetId([
            'Training_Name' => $trainingName,
            'Training_Date' => $date ?: now()->toDateString(),
            'Trainer_Name' => $trainer ? mb_substr(trim($trainer), 0, 100) : 'HopeHaven Compliance Board',
        ], 'Training_Id');
    }

    public function recordTraining(int $trainingId, ?int $staffId = null, ?int $volunteerId = null, ?string $completionDate = null, string $status = 'Completed'): int
    {
        $conn = $this->db();

        return (int) $conn->table('training_record')->insertGetId([
            'Training_Id' => $trainingId,
            'Staff_Id' => $staffId,
            'Volunteer_Id' => $volunteerId,
            'Completion_Date' => $completionDate ?: now()->toDateString(),
            'Status' => mb_substr(trim($status), 0, 30),
        ], 'Training_Record_Id');
    }

    public function findOrCreateEvent(string $title, ?string $type = 'Community Care', ?string $location = 'HopeHaven Campus', ?string $startAt = null, ?string $status = 'Upcoming'): int
    {
        $conn = $this->db();
        $title = mb_substr(trim($title), 0, 120);
        $existing = $conn->table('event')->where('Title', $title)->first();
        if ($existing) {
            return (int) $existing->Event_ID;
        }

        return (int) $conn->table('event')->insertGetId([
            'Title' => $title,
            'Description' => $type.' at '.($location ?: 'HopeHaven Campus'),
            'Event_Type' => mb_substr(trim($type ?: 'Community Care'), 0, 50),
            'Location' => mb_substr(trim($location ?: 'HopeHaven Campus'), 0, 150),
            'Start_At' => $startAt ? date('Y-m-d H:i:s', strtotime($startAt)) : now()->toDateTimeString(),
            'End_At' => null,
            'Capacity' => 30,
            'Status' => mb_substr(trim($status ?: 'Upcoming'), 0, 30),
            'User_Id' => null,
            'Orphanage_ID' => null,
        ], 'Event_ID');
    }

    public function registerVolunteerForEvent(int $eventId, int $volunteerId, string $status = 'Registered'): void
    {
        $conn = $this->db();
        $exists = $conn->table('event_registration')
            ->where('Event_Id', $eventId)
            ->where('Volunteer_Id', $volunteerId)
            ->exists();

        if ($exists) {
            $conn->table('event_registration')
                ->where('Event_Id', $eventId)
                ->where('Volunteer_Id', $volunteerId)
                ->update(['Status' => mb_substr(trim($status), 0, 30)]);
        } else {
            $conn->table('event_registration')->insert([
                'Event_Id' => $eventId,
                'Volunteer_Id' => $volunteerId,
                'Registered_At' => now()->toDateTimeString(),
                'Status' => mb_substr(trim($status), 0, 30),
            ]);
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CareRecords
{
    public const TABLES = [
        'organization' => ['Organization', 'Organization_Id', ['Organization_Name' => 'required|string|max:150', 'Organization_Address' => 'required|string', 'Organization_ConNum' => 'required|string|max:30', 'Organization_Email' => 'required|email|max:254']],
        'orphanage' => ['Orphanage', 'Orphanage_Id', ['Orphanage_Name' => 'required|string|max:150', 'Orphanage_Address' => 'required|string', 'Organization_Id' => 'required|integer']],
        'child' => ['Child', 'Child_Id', ['Child_Name' => 'required|string|max:150', 'Child_Date_Of_Birth' => 'required|date_format:Y-m-d|before_or_equal:today', 'Child_Gender' => 'required|string|max:20', 'Orphanage_Id' => 'required|integer']],
        'education' => ['Education_Record', 'Education_Id', ['Academic_Year' => 'required|string|max:9', 'Child_Attendance' => 'nullable|integer|min:0', 'Child_Result' => 'nullable|numeric|between:0,100', 'Child_Id' => 'required|integer']],
        'medical' => ['Medical_Record', 'Medical_Id', ['Medical_Diagnosis' => 'required|string', 'Medical_Treatment' => 'nullable|string', 'Medical_Record_Date' => 'required|date_format:Y-m-d', 'Child_Id' => 'required|integer']],
        'appointment' => ['Health_Appointment', 'Appointment_Id', ['Appointment_Date' => 'required|date_format:Y-m-d', 'Appointment_Purpose' => 'required|string', 'Child_Id' => 'required|integer']],
        'followup' => ['Welfare_FollowUp', 'Followup_Id', ['Followup_Date' => 'required|date_format:Y-m-d', 'Followup_Remarks' => 'nullable|string', 'Child_Id' => 'required|integer']],
        'subject' => ['Subject', 'Subject_Id', ['Subject_Name' => 'required|string|max:100']],
        'subject-result' => ['Subject_Result', null, ['Education_Id' => 'required|integer', 'Subject_Id' => 'required|integer', 'Subject_Score' => 'nullable|numeric|between:0,100', 'Subject_Grade' => 'nullable|string|max:5']],
    ];

    public const REFERENCES = ['Organization_Id' => 'organization', 'Orphanage_Id' => 'orphanage', 'Child_Id' => 'child', 'Education_Id' => 'education', 'Subject_Id' => 'subject'];

    public function definition(string $resource): array
    {
        abort_unless(isset(self::TABLES[$resource]), 404);

        return self::TABLES[$resource];
    }

    public function rules(string $resource): array
    {
        $rules = $this->definition($resource)[2];
        foreach ($rules as $column => $rule) {
            $rules[$column] = explode('|', $rule);
            if (isset(self::REFERENCES[$column])) {
                [$table, $key] = self::TABLES[self::REFERENCES[$column]];
                $rules[$column][] = Rule::exists($table, $key);
            }
        }

        return $rules;
    }

    public function snapshot(): array
    {
        $result = [];
        foreach (self::TABLES as $resource => [$table, $key]) {
            $result[$resource] = DB::table($table)->orderBy($key ?? 'Education_Id')->get();
        }

        return $result;
    }
}

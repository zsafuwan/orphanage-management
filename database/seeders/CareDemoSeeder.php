<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CareDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $organization = DB::table('Organization')->where('Organization_Email', 'care-demo@example.test')->value('Organization_Id');
            $organization ??= DB::table('Organization')->insertGetId([
                'Organization_Name' => 'HopeHaven Demo Foundation', 'Organization_Address' => 'Fictional demo address, Malaysia',
                'Organization_ConNum' => 'DEMO-NOT-A-PHONE', 'Organization_Email' => 'care-demo@example.test',
            ], 'Organization_Id');
            $homes = [];
            foreach (['Demo Sunrise Home', 'Demo Garden Home'] as $name) {
                $id = DB::table('Orphanage')->where('Organization_Id', $organization)->where('Orphanage_Name', $name)->value('Orphanage_Id');
                $homes[] = $id ?? DB::table('Orphanage')->insertGetId(['Organization_Id' => $organization, 'Orphanage_Name' => $name, 'Orphanage_Address' => 'Fictional demo address, Malaysia'], 'Orphanage_Id');
            }
            $subjects = [];
            foreach (['Mathematics', 'Science', 'English', 'Bahasa Melayu'] as $name) {
                $subjects[] = DB::table('Subject')->where('Subject_Name', $name)->value('Subject_Id')
                    ?? DB::table('Subject')->insertGetId(['Subject_Name' => $name], 'Subject_Id');
            }
            $names = ['Aina Rahman', 'Daniel Tan', 'Mei Lin Lim', 'Arjun Kumar', 'Sofia Hassan', 'Adam Iskandar', 'Priya Nair', 'Ethan Wong', 'Nur Iman', 'Lucas Lee'];
            $scores = [[92,88,95,90],[76,82,79,85],[98,94,96,93],[62,70,68,74],[85,89,91,87],[48,55,60,52],[90,86,94,88],[73,78,80,75],[66,72,70,77],[84,81,88,86]];
            foreach ($names as $i => $name) {
                $name = 'Demo '.$name;
                // Reruns leave existing demo profiles and any edits untouched.
                if (DB::table('Child')->whereIn('Orphanage_Id', $homes)->where('Child_Name', $name)->exists()) {
                    continue;
                }
                $child = DB::table('Child')->insertGetId([
                    'Child_Name' => $name, 'Child_Date_Of_Birth' => sprintf('%d-%02d-12', 2012 + $i % 6, 1 + $i),
                    'Child_Gender' => $i % 2 === 0 ? 'Female' : 'Male', 'Orphanage_Id' => $homes[$i % 2],
                ], 'Child_Id');
                $education = DB::table('Education_Record')->insertGetId([
                    'Child_Id' => $child, 'Academic_Year' => '2026', 'Child_Attendance' => 160 + $i * 2,
                    'Child_Result' => array_sum($scores[$i]) / 4,
                ], 'Education_Id');
                foreach ($subjects as $j => $subject) {
                    $score = $scores[$i][$j];
                    $grade = match (true) { $score >= 95 => 'A+', $score >= 85 => 'A', $score >= 75 => 'B+', $score >= 65 => 'B', $score >= 50 => 'C', default => 'D' };
                    DB::table('Subject_Result')->insert(['Education_Id' => $education, 'Subject_Id' => $subject, 'Subject_Score' => $score, 'Subject_Grade' => $grade]);
                }
                DB::table('Medical_Record')->insert([
                    'Child_Id' => $child, 'Medical_Record_Date' => '2026-11-10',
                    'Medical_Diagnosis' => ['Demo: routine wellness review', 'Demo: dental review', 'Demo: vision review', 'Demo: seasonal allergy review'][$i % 4],
                    'Medical_Treatment' => 'Fictional sample record for system demonstration; no actual treatment prescribed.',
                ]);
                DB::table('Health_Appointment')->insert([
                    'Child_Id' => $child, 'Appointment_Date' => sprintf('2026-12-%02d', 1 + $i),
                    'Appointment_Purpose' => ['Demo wellness appointment', 'Demo dental appointment', 'Demo eye screening'][$i % 3],
                ]);
                DB::table('Welfare_FollowUp')->insert([
                    'Child_Id' => $child, 'Followup_Date' => '2026-11-17',
                    'Followup_Remarks' => 'Fictional demo follow-up: '.($i === 5 ? 'Arrange additional learning support and review progress.' : 'Review school progress, wellbeing and participation in daily activities.'),
                ]);
            }
            $ids = DB::table('Child')->whereIn('Orphanage_Id', $homes)->where('Child_Name', 'like', 'Demo %')->pluck('Child_Id');
            $this->command->info('Demo children: '.$ids->count());
            foreach (['Education_Record', 'Medical_Record', 'Health_Appointment', 'Welfare_FollowUp'] as $table) {
                $this->command->info($table.': '.DB::table($table)->whereIn('Child_Id', $ids)->count());
            }
            $educationIds = DB::table('Education_Record')->whereIn('Child_Id', $ids)->pluck('Education_Id');
            $this->command->info('Subject_Result: '.DB::table('Subject_Result')->whereIn('Education_Id', $educationIds)->count());
        });
    }
}

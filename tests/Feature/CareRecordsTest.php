<?php

namespace Tests\Feature;

use App\Http\Middleware\CareAccess;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareRecordsTest extends TestCase
{
    private bool $transactionStarted = false;

    private function photoFile(string $name = 'portrait.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jD1sAAAAASUVORK5CYII='));
    }

    private function photoIntakeData(): array
    {
        $organization = DB::table('Organization')->insertGetId(['Organization_Name' => 'Photo test', 'Organization_Address' => 'Test', 'Organization_ConNum' => 'Test', 'Organization_Email' => 'photo@example.test'], 'Organization_Id');
        $home = DB::table('Orphanage')->insertGetId(['Organization_Id' => $organization, 'Orphanage_Name' => 'Photo test', 'Orphanage_Address' => 'Test'], 'Orphanage_Id');

        return ['Child_Name' => 'Photo test child', 'Child_Date_Of_Birth' => '2016-01-01', 'Child_Gender' => 'Female', 'Orphanage_Id' => $home];
    }

    public function test_child_photo_is_saved_privately_reloaded_and_replaced(): void
    {
        Storage::fake('local');
        $response = $this->post('/care-data/intake', [...$this->photoIntakeData(), 'photo' => $this->photoFile()], ['Accept' => 'application/json'])->assertOk();
        $id = $response->json('record.Child_Id');
        $path = $response->json('record.Child_Photo_Path');
        $this->assertStringStartsWith('child-photos/', $path);
        Storage::disk('local')->assertExists($path);
        $this->assertSame($path, DB::table('Child')->where('Child_Id', $id)->value('Child_Photo_Path'));
        $this->get('/care-data/child/'.$id.'/photo')->assertOk()->assertHeader('Content-Type', 'image/png')->assertHeader('X-Content-Type-Options', 'nosniff');
        $snapshot = $this->getJson('/care-data')->assertOk()->json('child');
        $this->assertSame($path, collect($snapshot)->firstWhere('Child_Id', $id)['Child_Photo_Path']);
        $this->post('/care-data/child/'.$id.'/photo', ['photo' => $this->photoFile('new.png')], ['Accept' => 'application/json'])->assertOk();
        $replacement = DB::table('Child')->where('Child_Id', $id)->value('Child_Photo_Path');
        $this->assertNotSame($path, $replacement);
        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertExists($replacement);
    }

    public function test_invalid_child_photos_are_rejected_without_creating_records_or_files(): void
    {
        Storage::fake('local');
        $data = $this->photoIntakeData();
        $before = DB::table('Child')->count();
        $this->post('/care-data/intake', [...$data, 'photo' => UploadedFile::fake()->create('script.svg', 1, 'image/svg+xml')], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('photo');
        $this->post('/care-data/intake', [...$data, 'photo' => UploadedFile::fake()->create('large.png', 6000, 'image/png')], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('photo');
        $this->assertSame($before, DB::table('Child')->count());
        $this->assertSame([], Storage::disk('local')->allFiles('child-photos'));
        $id = $this->postJson('/care-data/intake', $data)->assertOk()->json('record.Child_Id');
        $this->get('/care-data/child/'.$id.'/photo')->assertNotFound();
        $this->withMiddleware(CareAccess::class)->get('/care-data/child/'.$id.'/photo')->assertForbidden();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('CARE_DATABASE_TESTS') !== '1') {
            $this->markTestSkipped('Opt-in PostgreSQL integration test; uses existing tables and rolls back every write.');
        }
        config(['database.default' => 'pgsql', 'database.connections.pgsql.database' => getenv('CARE_TEST_DATABASE') ?: 'postgres']);
        $this->withoutMiddleware(CareAccess::class);
        DB::beginTransaction();
        $this->transactionStarted = true;
    }

    protected function tearDown(): void
    {
        if ($this->transactionStarted) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    public function test_forms_save_and_reload_all_existing_care_tables(): void
    {
        $organization = $this->postJson('/care-data/organization', ['Organization_Name' => 'Integration test', 'Organization_Address' => 'Test address', 'Organization_ConNum' => '0123456789', 'Organization_Email' => 'test@example.test'])->assertOk()->json('record.Organization_Id');
        $home = $this->postJson('/care-data/orphanage', ['Orphanage_Name' => 'Test home', 'Orphanage_Address' => 'Test address', 'Organization_Id' => $organization])->assertOk()->json('record.Orphanage_Id');
        $child = $this->postJson('/care-data/child', ['Child_Name' => 'Test child', 'Child_Date_Of_Birth' => '2016-01-01', 'Child_Gender' => 'Female', 'Orphanage_Id' => $home])->assertOk()->json('record.Child_Id');
        $intake = $this->postJson('/care-data/intake', ['Child_Name' => 'Test intake', 'Child_Date_Of_Birth' => '2016-01-01', 'Child_Gender' => 'Male', 'Orphanage_Id' => $home, 'Medical_Diagnosis' => 'Initial review'])->assertOk()->json('record.Child_Id');
        $this->assertSame('Initial review', DB::table('Medical_Record')->where('Child_Id', $intake)->value('Medical_Diagnosis'));
        $this->postJson('/care-data/paper', ['Child_Id' => $child, 'Academic_Year' => '2026', 'subjects' => [['name' => 'Test subject', 'score' => 85.5, 'grade' => 'A']]])->assertOk();
        $education = DB::table('Education_Record')->where('Child_Id', $child)->first();
        $subject = DB::table('Subject_Result')->where('Education_Id', $education->Education_Id)->first();
        $this->assertSame(85.5, (float) $subject->Subject_Score);
        $this->postJson('/care-data/subject-result', ['Education_Id' => $education->Education_Id, 'Subject_Id' => $subject->Subject_Id, 'Subject_Score' => 90, 'Subject_Grade' => 'A'])->assertOk();
        $this->assertSame(1, DB::table('Subject_Result')->where('Education_Id', $education->Education_Id)->count());
        $this->postJson('/care-data/education', ['record_id' => $education->Education_Id, 'Child_Id' => $child, 'Academic_Year' => '2026', 'Child_Attendance' => 350, 'Child_Result' => 90])->assertOk();
        $this->postJson('/care-data/medical', ['Child_Id' => $child, 'Medical_Diagnosis' => 'Routine review', 'Medical_Treatment' => 'Observation', 'Medical_Record_Date' => '2026-11-17'])->assertOk();
        $this->postJson('/care-data/appointment', ['Child_Id' => $child, 'Appointment_Purpose' => 'Review', 'Appointment_Date' => '2026-12-01'])->assertOk();
        $this->postJson('/care-data/followup', ['Child_Id' => $child, 'Followup_Date' => '2026-11-17', 'Followup_Remarks' => 'Follow up'])->assertOk();
        $snapshot = $this->getJson('/care-data')->assertOk()->assertHeader('Cache-Control', 'no-store, private')->json();
        $this->assertCount(9, $snapshot);
        $this->assertTrue(collect($snapshot['child'])->contains('Child_Id', $child));
        $this->assertSame(350, DB::table('Education_Record')->where('Education_Id', $education->Education_Id)->value('Child_Attendance'));
        $this->get('/records')->assertNotFound();
    }

    public function test_invalid_scores_foreign_keys_and_resources_are_rejected(): void
    {
        $this->postJson('/care-data/child', ['Child_Name' => 'Test', 'Child_Date_Of_Birth' => '2999-01-01', 'Child_Gender' => 'Female', 'Orphanage_Id' => -1])->assertUnprocessable()->assertJsonValidationErrors(['Child_Date_Of_Birth', 'Orphanage_Id']);
        $this->postJson('/care-data/education', ['Academic_Year' => '2026', 'Child_Id' => -1, 'Child_Result' => 120])->assertUnprocessable()->assertJsonValidationErrors(['Child_Result', 'Child_Id']);
        $this->postJson('/care-data/not-a-table', [])->assertNotFound();
    }
}

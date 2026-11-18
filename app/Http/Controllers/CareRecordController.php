<?php

namespace App\Http\Controllers;

use App\Services\CareRecords;
use App\Services\ChildPhotos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CareRecordController extends Controller
{
    public function snapshot(CareRecords $records)
    {
        return response()->json($records->snapshot())->header('Cache-Control', 'no-store');
    }

    public function save(Request $request, string $resource, CareRecords $records)
    {
        [$table, $key] = $records->definition($resource);
        $data = $request->validate($records->rules($resource));
        $id = $request->validate(['record_id' => 'nullable|integer|min:1'])['record_id'] ?? null;
        $row = DB::transaction(function () use ($table, $key, $data, $id) {
            if ($key && $id) {
                abort_unless(DB::table($table)->where($key, $id)->exists(), 404);
                DB::table($table)->where($key, $id)->update($data);
            } elseif ($key) {
                $id = DB::table($table)->insertGetId($data, $key);
            } else {
                DB::table($table)->upsert([$data], ['Education_Id', 'Subject_Id'], ['Subject_Score', 'Subject_Grade']);

                return $data;
            }

            return DB::table($table)->where($key, $id)->first();
        });

        return response()->json(['record' => $row]);
    }

    public function paper(Request $request, CareRecords $records)
    {
        $data = $request->validate([
            'Child_Id' => $records->rules('education')['Child_Id'],
            'Academic_Year' => 'required|string|max:9',
            'subjects' => 'required|array|min:1',
            'subjects.*.name' => 'required|string|max:100|distinct',
            'subjects.*.score' => 'required|numeric|between:0,100',
            'subjects.*.grade' => 'nullable|string|max:5',
        ]);
        DB::transaction(function () use ($data) {
            $education = DB::table('Education_Record')->insertGetId([
                'Child_Id' => $data['Child_Id'], 'Academic_Year' => $data['Academic_Year'],
                'Child_Result' => round(collect($data['subjects'])->avg('score'), 2),
            ], 'Education_Id');
            foreach ($data['subjects'] as $subject) {
                $id = DB::table('Subject')->where('Subject_Name', $subject['name'])->value('Subject_Id');
                $id ??= DB::table('Subject')->insertGetId(['Subject_Name' => $subject['name']], 'Subject_Id');
                DB::table('Subject_Result')->insert(['Education_Id' => $education, 'Subject_Id' => $id, 'Subject_Score' => $subject['score'], 'Subject_Grade' => $subject['grade'] ?? null]);
            }
        });

        return response()->json(['saved' => true]);
    }

    public function intake(Request $request, CareRecords $records, ChildPhotos $photos)
    {
        $data = $request->validate([...$records->rules('child'), 'Medical_Diagnosis' => 'nullable|string', 'photo' => ChildPhotos::RULES]);
        unset($data['photo']);
        $path = $request->hasFile('photo') ? $photos->store($request->file('photo')) : null;
        try {
            $id = DB::transaction(function () use ($data, $path) {
                $diagnosis = $data['Medical_Diagnosis'] ?? null;
                unset($data['Medical_Diagnosis']);
                $data['Child_Photo_Path'] = $path;
                $id = DB::table('Child')->insertGetId($data, 'Child_Id');
                if ($diagnosis) {
                    DB::table('Medical_Record')->insert(['Child_Id' => $id, 'Medical_Diagnosis' => $diagnosis, 'Medical_Record_Date' => now()->toDateString()]);
                }

                return $id;
            });
        } catch (Throwable $error) {
            $photos->discard($path);
            throw $error;
        }

        return response()->json(['record' => DB::table('Child')->where('Child_Id', $id)->first()]);
    }

    public function photo(string $child)
    {
        $row = DB::table('Child')->where('Child_Id', $child)->first();
        abort_unless($row && $row->Child_Photo_Path && preg_match('~^child-photos/[a-zA-Z0-9]+\.(jpg|jpeg|png|webp)$~', $row->Child_Photo_Path), 404);
        abort_unless(Storage::disk('local')->exists($row->Child_Photo_Path), 404);

        return Storage::disk('local')->response($row->Child_Photo_Path, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function updatePhoto(Request $request, string $child, ChildPhotos $photos)
    {
        $request->validate(['photo' => ['required', ...ChildPhotos::RULES]]);
        abort_unless(DB::table('Child')->where('Child_Id', $child)->exists(), 404);
        $path = $photos->store($request->file('photo'));
        try {
            $old = DB::transaction(function () use ($child, $path) {
                $row = DB::table('Child')->where('Child_Id', $child)->lockForUpdate()->first();
                abort_unless($row, 404);
                DB::table('Child')->where('Child_Id', $child)->update(['Child_Photo_Path' => $path]);

                return $row->Child_Photo_Path;
            });
        } catch (Throwable $error) {
            $photos->discard($path);
            throw $error;
        }
        $photos->discard($old);

        return response()->json(['saved' => true]);
    }
}

<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class AttendanceImport implements ToModel, WithStartRow
{
    protected $course_id;

    public function __construct($course_id)
    {
        $this->course_id = $course_id;
    }

    public function model(array $row)
    {
        try {
            $attendance = Attendance::create([
                'full_name' => $row[0],
                'first_seen' => $row[1],
                'time_in_call' => $row[2],
                'course_id' => $this->course_id
            ]);

            Log::info('Attendance record created', ['attendance' => $attendance->toArray()]);

            return $attendance;
        } catch (\Exception $e) {
            Log::error('Error creating Attendance record', [
                'error' => $e->getMessage(),
                'row' => $row
            ]);
        }
    }

    public function startRow(): int
    {
        return 6; // Start from the 6th row
    }
}
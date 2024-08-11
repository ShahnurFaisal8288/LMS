<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function courseTeacher()
    {
        return $this->belongsTo(CourseTeacher::class,'course_teacher_id');
    }
}

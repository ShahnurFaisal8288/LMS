<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function sections()
    {
        return $this->hasMany(Section::class,'course_teacher_id');
    }
}

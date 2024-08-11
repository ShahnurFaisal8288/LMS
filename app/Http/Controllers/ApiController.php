<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Models\AboutUsSection;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\BannerSection;
use App\Models\CategoryItem;
use App\Models\CategorySection;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\Employee;
use App\Models\FeatureSection;
use App\Models\Lesson;
use App\Models\Notice;
use App\Models\OnlineClassLink;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\UpdateAssignment;
use App\Models\UpdateQuiz;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ApiController extends Controller
{
    //userRegister
    public function userRegister()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        if (request()->hasFile('student_img')) {
            $extension = request()->file('student_img')->extension();
            $photo_name = "backend/img/student/" . uniqid() . '.' . $extension;
            request()->file('student_img')->move('backend/img/student/', $photo_name);
        }
        $student = Student::create([
            'name' => request('name'),
            'email' => request('email'),
            'birth_date' => request('birth_date'),
            'gender' => request('gender'),
            'address' => request('address'),
            'phone' => request('phone'),
            'year_of_study' => request('year_of_study'),
            'course_id' => request('course_id'),
            'emergency_contact' => request('emergency_contact'),
            'guardian_info' => request('guardian_info'),
            'status' => 'approve',
        ]);
        $user = User::create([
            'role_id' => 2,
            'authId' => $student->id,
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'confirm_password' => bcrypt(request('confirm_password')),
        ]);



        // $code = sha1(rand(1000, 8000));
        // $user->UserVerify()->create([
        //     'code' => $code
        // ]);
        // $generatedUrl = route('user.verify', [$user->email, $code]);
        // Mail::to($user->email)->send(new UserVerification($generatedUrl));
        return response()->json([
            'status' => 'ok',
            'message' => 'User CReated',
            'user' => $user,
            'student' => $student
        ]);
    }
    //studentfrontend
    public function studentfrontend()
    {
        if (request()->hasFile('student_img')) {
            $extension = request()->file('student_img')->extension();
            $photo_name = "backend/img/student/" . uniqid() . '.' . $extension;
            request()->file('student_img')->move('backend/img/student/', $photo_name);
        }
        $student = Student::create([
            'name' => request('name'),
            'email' => request('email'),
            'birth_date' => request('birth_date'),
            'gender' => request('gender'),
            'address' => request('address'),
            'phone' => request('phone'),
            'year_of_study' => request('year_of_study'),
            'course_id' => request('course_id'),
            'emergency_contact' => request('emergency_contact'),
            'guardian_info' => request('guardian_info'),
            // 'status' => 'approve',
        ]);
        $user = User::create([
            'role_id' => 2,
            'authId' => $student->id,
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'confirm_password' => bcrypt(request('confirm_password')),
        ]);

        // $code = sha1(rand(1000, 8000));
        // $user->UserVerify()->create([
        //     'code' => $code
        // ]);
        // $generatedUrl = route('user.verify', [$user->email, $code]);
        // Mail::to($user->email)->send(new UserVerification($generatedUrl));
        return response()->json([
            'status' => 'ok',
            'message' => 'User CReated',
            'user' => $user,
            'student' => $student
        ]);
    }
    //email verify
    //user email verify
    // public function verify($email, $code)
    // {
    //     $user = User::where('email', $email)->first();
    //     if ($user) {
    //         if ($user->email_verified == 'no') {
    //             $userCode = $user->UserVerify->code;
    //             if ($userCode == $code) {
    //                 $user->update([
    //                     'email_verified' => 'yes'
    //                 ]);

    //                 $user->UserVerify->delete();

    //                 //email to admin

    //                 try{
    //                     $data = ['name' => 'Admin', 'email' => 'shahediqbal80@gmail.com'];
    //                     Mail::send('backend.sendMail', $data, function ($message) use ($data)
    //                     {
    //                         $message->from('info@bpdac.ca','Admin');
    //                         $message->to($data['email'], $data['name'])
    //                             ->subject('Approval of Order');
    //                     });
    //                 }catch (\Exception $e) {
    //                     // Log or dump the exception message for debugging
    //                     dd($e->getMessage());
    //                 }

    //                                     // end email to admin
    //                 return '<strong style="font-size: xx-large;">Congratulations! Your email has been successfully verified.<br> <a href="https://bpdac.ca/login">https://bpdac.ca/login</a></strong>';

    //             } else {
    //                 return '<strong style="font-size: xx-large;">Unauthorized Data!!!</strong>';
    //             }
    //         }else{
    //             return '<strong style="font-size: xx-large;"> Your email has already been verified</strong>';
    //         }
    //     } else {
    //         return '<strong style="font-size: xx-large;">Unauthorized</strong>';
    //     }
    // }
    //userLogin
    public function userLogin()
    {
        $credentials = request()->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user = JWTAuth::user();

        if ($user) {
            if ($user->status === 'pending') {
                return response()->json(['error' => 'User is pending approval.'], 403);
            }

            $userData = User::select('id', 'name', 'email', 'role_id')->find($user->id);
            if ($userData) {

                $cookie = cookie('jwt', $token, 60 * 24);
                return response()->json([
                    'status' => 'ok',
                    'token' => $token,
                    'user' => $userData,

                ])->withCookie($cookie);
            } else {
                return response()->json(['error' => 'User not found or has missing columns.'], 404);
            }
        } else {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }
    }
    //authCourse
    public function authCourse()
    {
        $user = JWTAuth::user();
        $studentCourse = CourseStudent::where('user_id', $user->id)->first();
        $course = Course::where('id', $studentCourse->course_id)->first();
        $totalCourseStudent = Student::where('course_id', $course->id)->count();
        return response()->json(['studentCourse' => $studentCourse, 'course' => $course, '$totalCourseStudent' => $totalCourseStudent], 200);
    }
    //authTeacherCourse
    public function authTeacherCourse()
    {
        $user = JWTAuth::user();
        $courseTeachers = CourseTeacher::where('user_id', $user->id)->get();
        if ($courseTeachers->isEmpty()) {
            return response()->json(['error' => 'Course Teacher not found'], 404);
        }

        // Assuming all course_teacher records have the same course_id
        $courseId = $courseTeachers->first()->course_id;
        $course = Course::find($courseId);

        // Retrieve all sections based on the section_ids from courseTeachers
        $sectionIds = $courseTeachers->pluck('section_id');
        $sections = Section::whereIn('id', $sectionIds)->get();

        return response()->json([
            "courseTeachers" => $courseTeachers,
            "course" => $course,
            "sections" => $sections,
        ]);
    }
    //studentUpdate
    public function studentUpdate($id)
    {
        $statusUpdate = Student::findOrFail($id);
        $statusUpdate->status = 'approve';
        $statusUpdate->save();
        return response()->json($statusUpdate, 200);
    }
    //bannerSection
    public function bannerSection()
    {
        $banner = BannerSection::get();
        return response()->json($banner, 200);
    }
    public function bannerSectionPost()
    {
        if (request()->hasFile('banner')) {
            $extension = request()->file('banner')->getClientOriginalExtension();
            $bannerName = 'image/banner/' . uniqid() . '.' . $extension;
            request()->file('banner')->move('image/banner', $bannerName);
        }
        $banner = BannerSection::create([
            'title' => request('title'),
            'subTitle' => request('subTitle'),
            'description' => request('description'),
            'banner_show_time' => request('banner_show_time'),
            'banner' => $bannerName,
        ]);
        return response()->json($banner, 200);
    }
    //bannerEdit
    public function bannerEdit($id)
    {
        $banner = BannerSection::find($id);
        if (request()->hasFile('banner')) {
            $extension = request()->file('banner')->getClientOriginalExtension();
            $bannerName = 'image/banner/' . uniqid() . '.' . $extension;
            request()->file('banner')->move('image/banner', $bannerName);
            if (File::exists($banner->banner)) {
                File::delete($banner->banner);
            }
            $banner->banner = $bannerName;
        }
        $banner->title = request('title');
        $banner->subTitle = request('subTitle');
        $banner->description = request('description');
        $banner->banner_show_time = request('banner_show_time');
        $banner->save();
        return response()->json($banner, 200);
    }
    // bannerDelete
    public function bannerDelete($id)
    {
        $banner = BannerSection::findOrFail($id);
        if (File::exists($banner->banner)) {
            File::delete($banner->banner);
        }
        $banner->delete();
        return response()->json(['message' => 'Banner Deleted Successfully'], 200);
    }
    //categoryItemGet
    public function categoryItem()
    {
        $categoryItem = CategoryItem::get();
        return response()->json($categoryItem, 200);
    }
    //categoryItemPost
    public function categoryItemPost()
    {
        $categoryItem = new CategoryItem();
        $categoryItem->title = request('title');
        $categoryItem->description = request('description');
        $categoryItem->type = request('type');
        $categoryItem->save();
        return response()->json($categoryItem, 200);
    }
    //categoryItemEdit
    public function categoryItemEdit($id)
    {
        $categoryItem = CategoryItem::findOrFail($id);
        $categoryItem->title = request('title');
        $categoryItem->description = request('description');
        $categoryItem->type = request('type');
        $categoryItem->save();
        return response()->json($categoryItem, 200);
    }
    //categoryItemDelete
    public function categoryItemDelete($id)
    {
        CategoryItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Category Item Deleted Successfully'], 200);
    }
    //categorySectionGet
    public function categorySection()
    {
        $categorySection = CategorySection::get();
        return response()->json($categorySection, 200);
    }
    //categorySectionPost
    public function categorySectionPost()
    {

        $categorySection = CategorySection::create([
            'title' => request('title'),
            'color' => request('color'),
            'desc' => request('desc'),
            'icon' => request('icon'),
        ]);
        return response()->json($categorySection, 200);
    }
    //categorySectionEdit
    public function categorySectionEdit($id)
    {
        $categorySection = CategorySection::findOrFail($id);
        $categorySection->title = request('title');
        $categorySection->color = request('color');
        $categorySection->desc = request('desc');
        $categorySection->icon = request('icon');
        $categorySection->save();
        return response()->json($categorySection, 200);
    }
    //categorySectionDelete
    public function categorySectionDelete($id)
    {
        CategorySection::findOrFail($id)->delete();
        return response()->json(['message' => 'Category Section Data Deleted Successfully'], 200);
    }
    //featuredSection
    public function featuredSection()
    {
        $featuredSection = FeatureSection::get();
        return response()->json($featuredSection, 200);
    }
    //featuredSectionPost
    public function featuredSectionPost()
    {
        if (request()->hasFile('featuredImg')) {
            $extension = request()->file('featuredImg')->getClientOriginalExtension();
            $imageName = 'image/featuredImg/' . uniqid() . '.' . $extension;
            request()->file('featuredImg')->move('image/featuredImg', $imageName);
        }
        $featuredSection = FeatureSection::create([
            'title' => request('title'),
            'desc' => request('desc'),
            'featuredImg' => $imageName,
        ]);
        return response()->json($featuredSection, 200);
    }
    //featuredSectionEdit
    public function featuredSectionEdit($id)
    {
        if (request()->hasFile('featuredImg')) {
            $extension = request()->file('featuredImg')->getClientOriginalExtension();
            $imageName = 'image/featuredImg/' . uniqid() . '.' . $extension;
            request()->file('featuredImg')->move('image/featuredImg', $imageName);
        }
        $featuredSection = FeatureSection::find($id);
        $featuredSection->title = request('title');
        $featuredSection->desc = request('desc');
        $featuredSection->featuredImg = $imageName;
        $featuredSection->save();

        return response()->json($featuredSection, 200);
    }
    //featuredSectionDelete
    public function featuredSectionDelete($id)
    {
        $featuredSection = FeatureSection::find($id);
        if (File::exists($featuredSection->featuredImg)) {
            File::delete($featuredSection->featuredImg);
        }
        $featuredSection->delete();
        return response()->json(['message' => 'Featured Section Data Deleted Successfully'], 200);
    }
    //aboutSection
    public function aboutSection()
    {
        $aboutSection = AboutUsSection::get();
        return response()->json($aboutSection, 200);
    }
    // aboutSectionPost
    public function aboutSectionPost()
    {
        if (request()->hasFile('amountUs_img')) {
            $extension = request()->file('amountUs_img')->getClientOriginalExtension();
            $imageName = 'image/amountUs_img/' . uniqid() . '.' . $extension;
            request()->file('amountUs_img')->move('image/amountUs_img', $imageName);
        }
        $amountUs_img = AboutUsSection::create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'desc' => request('desc'),
            'desc_list' => request('desc_list'),
            'amountUs_img' => $imageName,
        ]);
        return response()->json($amountUs_img, 200);
    }
    //aboutUsEdit
    public function aboutUsEdit($id)
    {
        $aboutUs = AboutUsSection::findOrFail($id);
        if (request()->hasFile('amountUs_img')) {
            $extension = request()->file('amountUs_img')->getClientOriginalExtension();
            $imageName = 'image/amountUs_img/' . uniqid() . '.' . $extension;
            request()->file('amountUs_img')->move('image/amountUs_img', $imageName);
            if (File::exists($aboutUs->amountUs_img)) {
                File::delete($aboutUs->amountUs_img);
            }
            $aboutUs->amountUs_img = $imageName;
        }
        $aboutUs->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'desc' => request('desc'),
            'desc_list' => request('desc_list'),
        ]);
        return response()->json($aboutUs, 200);
    }
    //aboutUsDelete
    public function aboutUsDelete($id)
    {
        $aboutUs = AboutUsSection::findOrFail($id);
        if (File::exists($aboutUs->amountUs_img)) {
            File::delete($aboutUs->amountUs_img);
        }
        $aboutUs->delete();
        return response()->json(['message' => "About Us Data Deleted Successfully"], 200);
    }
    //employee
    public function teacher()
    {
        $teacher = Teacher::get();
        return response()->json($teacher, 200);
    }
    //employeePost
    public function teacherPost()
    {

        $teacher = Teacher::create([
            'name' => request('name'),
            'designation' => request('designation'),
            'slug' => Str::slug(request()->input('name'), '-'),
            'email' => request('email'),
        ]);
        $teacherAuth = User::create([
            'role_id' => 3,
            'authId' => $teacher->id,
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'confirm_password' => bcrypt(request('confirm_password')),
        ]);
        return response()->json([$teacher, $teacherAuth, 200]);
    }
    //teacherEdit
    public function teacherEdit($id)
    {
        $teacher = Teacher::findOrFail($id);
        if (request()->hasFile('image')) {
            $extension = request()->file('image')->getClientOriginalExtension();
            $imageName = 'image/teacher/' . uniqid() . '.' . $extension;
            request()->file('image')->move('image/teacher', $imageName);
            $teacher->image = $imageName;
        }
        $teacher->name = request('name');
        $teacher->designation = request('designation');
        $teacher->slug = Str::slug(request()->input('name'), '-');
        $teacher->about = request('about');
        $teacher->phone = request('phone');
        $teacher->facebook_link = request('facebook_link');
        $teacher->linkedin_link = request('linkedin_link');
        $teacher->save();
        return response()->json($teacher, 200);
    }
    //teacherDelete
    public function teacherDelete($id)
    {
        $teacher = Teacher::findOrFail($id);
        if (File::exists($teacher->image)) {
            File::delete($teacher->image);
        }

        $user = User::where('authId', $id)->first();
        $user->delete();
        $teacher->delete();
        return response()->json(['message' => "Teacher's Data Deleted Successfully"], 200);
    }
    //course
    public function course()
    {
        $course = Course::get();
        return response()->json($course, 200);
    }
    //coursePost
    public function coursePost()
    {
        if (request()->hasFile('course_img')) {
            $extension = request()->file('course_img')->getClientOriginalExtension();
            $imageName = 'image/course_img/' . uniqid() . '.' . $extension;
            request()->file('course_img')->move('image/course_img', $imageName);
        }
        $course = Course::create([
            'category_id' => request('category_id'),
            'price' => request('price'),
            'description' => request('description'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'color' => request('color'),
            'title' => request('title'),
            'slug' => Str::slug(request()->input('title'), '-'),
            'course_img' => $imageName,
        ]);

        return response()->json($course, 200);
    }
    //courseEdit
    public function courseEdit($id)
    {
        $course = Course::findOrFail($id);

        if (request()->hasFile('course_img')) {
            $extension = request()->file('course_img')->getClientOriginalExtension();
            $imageName = 'image/course_img/' . uniqid() . '.' . $extension;
            request()->file('course_img')->move('image/course_img', $imageName);
            if (File::exists($course->course_img)) {
                File::delete($course->course_img);
            }
            $course->course_img = $imageName;
        }
        $course->category_id = request('category_id');
        $course->price = request('price');
        $course->description = request('description');
        $course->start_date = request('start_date');
        $course->end_date = request('end_date');
        $course->color = request('color');
        $course->title = request('title');
        $course->slug = Str::slug(request()->input('title'), '-');
        $course->save();
        return response()->json($course, 200);
    }
    //courseDelete
    public function courseDelete($id)
    {
        $course = Course::findOrFail($id);
        if (File::exists($course->course_img)) {
            File::delete($course->course_img);
        }
        $course->delete();
        return response()->json(['message' => 'Course  Data Deleted Successfully'], 200);
    }
    // lesson
    public function lesson()
    {
        $lesson = Lesson::get();
        return response()->json($lesson, 200);
    }
    //lessonPost
    public function lessonPost()
    {
        // Handle files upload
        if (request()->hasFile('files')) {
            $extension = request()->file('files')->getClientOriginalExtension();
            $imageName = 'image/files/' . uniqid() . '.' . $extension;
            request()->file('files')->move('image/files', $imageName);
        }

        // Create the lesson entry
        $lesson = Lesson::create([
            'course_id' => request()->course_id,
            'lecture_title' => request()->lecture_title,
            'files' => $imageName,
        ]);
        return response()->json($lesson, 200);
    }
    //lessonEdit
    public function lessonEdit($id)
    {
        $imageName = null;
        $fileNames = [];
        $pdfLesson = null;
        $lesson = Lesson::findOrFail($id);
        // Handle lesson_image upload


        // Handle files upload
        if (request()->hasFile('files')) {
            $files = request()->file('files');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = 'image/files/' . uniqid() . '.' . $extension;
                        $file->move('image/files', $fileName);
                        $fileNames[] = $fileName;
                    }
                }
            }
            if (File::exists($lesson->files)) {
                File::delete($lesson->files);
            }
            $lesson->files = json_encode($fileNames, JSON_UNESCAPED_SLASHES);
        }

        // Handle pdf upload


        // Create the lesson entry
        $lesson->update([
            'course_id' => request('course_id'),
            'lecture_title' => request('lecture_title'),
            'files' => json_encode($fileNames, JSON_UNESCAPED_SLASHES),
        ]);

        return response()->json($lesson, 200);
    }
    //lessonDelete
    public function lessonDelete($id)
    {
        $lesson = Lesson::findOrFail($id);

        if (File::exists($lesson->files)) {
            File::delete($lesson->files);
        }
        $lesson->delete();
        return response()->json(['message' => 'Lesson  Data Deleted Successfully'], 200);
    }
    //roleGet
    public function roleGet()
    {
        $role = Role::all();
        return response()->json($role, 200);
    }
    //rolePost
    public function rolePost()
    {
        $role = new Role();
        $role->name = request('name');
        $role->slug = Str::slug(request()->input('name'), '-');
        $role->save();
        return response()->json($role, 200);
    }
    //studentGet
    public function studentGet()
    {
        $student = Student::all();
        return response()->json($student, 200);
    }
    //courseStudent
    public function courseStudent()
    {
        $courseStudent = new CourseStudent();
        $courseStudent->student_id = request('student_id');
        $courseStudent->course_id = request('course_id');
        $courseStudent->save();
        return response()->json($courseStudent, 200);
    }

    //studentPost
    public function studentPost($id)
    {
        $student = Student::findOrFail($id);

        if (request()->hasFile('student_img')) {
            $extension = request()->file('student_img')->getClientOriginalExtension();
            $imageName = 'backend/img/student/' . uniqid() . '.' . $extension;
            request()->file('student_img')->move('backend/img/student', $imageName);
            if (File::exists($student->student_img)) {
                File::delete($student->student_img);
            }
            $student->student_img = $imageName;
        }
        $student->name = request('name');
        $student->birth_date = request('birth_date');
        $student->gender = request('gender');
        $student->address = request('address');
        $student->phone = request('phone');
        $student->year_of_study = request('year_of_study');
        $student->emergency_contact = request('emergency_contact');
        $student->guardian_info = request('guardian_info');

        $student->save();
        return response()->json($student, 200);
    }
    //studentDelete
    public function studentDelete($id)
    {
        $student = Student::findOrFail($id);
        if (File::exists($student->student_img)) {
            File::delete($student->student_img);
        }
        $user = User::where('authId', $id)->first();
        $user->delete();
        $student->delete();
        return response()->json(['message' => "Student Data Deleted Successfully"], 200);
    }
    //sectionGet
    public function sectionGet()
    {
        $section = Section::all();
        return response()->json($section, 200);
    }
    //sectionPost
    public function sectionPost()
    {
        $section = new Section();
        $section->name = request('name');
        $section->save();
        return response()->json($section, 200);
    }
    //sectionEdit
    public function sectionEdit($id)
    {
        $section = Section::findOrFail($id);
        $section->name = request('name');
        $section->save();
        return response()->json($section, 200);
    }
    //sectionDelete
    public function sectionDelete($id)
    {
        Section::findOrFail($id)->delete();
        return response()->json(['message' => "Section Successfully Deleted"], 200);
    }
    //courseTeacher
    public function courseTeacher()
    {
        $course_id = request('course_id');
        $user_id = request('user_id');
        $section_ids = request('section_id'); // Expecting an array of section IDs

        $courses = [];

        foreach ($section_ids as $section_id) {
            $course = CourseTeacher::create([
                'course_id' => $course_id,
                'user_id' => $user_id,
                'section_id' => $section_id,
            ]);
            $courses[] = $course;
        }
        return response()->json($courses, 200);
    }
    // courseTeacherGet
    public function courseTeacherGet()
    {
        $courseTeacher = CourseTeacher::all();
        return response()->json($courseTeacher, 200);
    }
    //notice
    public function notice()
    {
        $notice = Notice::all();
        return response()->json($notice, 200);
    }
    //noticePost
    public function noticePost()
    {
        $notice = new Notice();
        $notice->title = request('title');
        $notice->description = request('description');
        $notice->save();
        return response()->json($notice, 200);
    }
    //noticeEdit
    public function noticeEdit($id)
    {
        $notice = Notice::findOrFail($id);
        // $notice->user_id = request('user_id');
        $notice->title = request('title');
        $notice->description = request('description');
        $notice->save();
        return response()->json($notice, 200);
    }
    //noticeDelete
    public function noticeDelete($id)
    {
        Notice::findOrFail($id)->delete();
        return response()->json(['measasge' => 'Notice Deleted Successfully'], 200);
    }
    // onlineClass
    public function onlineClass()
    {
        $onlineClass = OnlineClassLink::all();
        return response()->json($onlineClass, 200);
    }
    //onlineClassStore
    public function onlineClassStore()
    {
        $onlineClass = new OnlineClassLink();
        $onlineClass->course_id = request('course_id');
        $onlineClass->class_link = request('class_link'); // Ensure that 'video_link' column in the database is of string type.
        $onlineClass->dateTime = date('Y-m-d H:i:s', strtotime(request('dateTime'))); // Convert the datetime string to a format MySQL can recognize.

        // Convert 'week' to an array
        $weeks = (array)request('week');

        // Store the array directly without serialization
        $onlineClass->week = json_encode($weeks);

        $onlineClass->save();


        return response()->json($onlineClass, 200);
    }
    //onlineClassEdit
    public function onlineClassEdit($id)
    {
        $onlineClass = OnlineClassLink::findOrFail($id);
        $onlineClass->course_id = request('course_id');
        $onlineClass->class_link = request('class_link'); // Ensure that 'video_link' column in the database is of string type.
        $onlineClass->dateTime = date('Y-m-d H:i:s', strtotime(request('dateTime'))); // Convert the datetime string to a format MySQL can recognize.

        // Convert 'week' to an array
        $weeks = (array)request('week');

        // Store the array directly without serialization
        $onlineClass->week = json_encode($weeks);

        $onlineClass->save();


        return response()->json($onlineClass, 200);
    }
    //onlineClassDelete
    public function onlineClassDelete($id)
    {
        OnlineClassLink::findOrFail($id)->delete();
        return response()->json(['message' => 'Online Class Link Deleted Successfully'], 200);
    }
    // importCsv
    public function importCsv(){
        try {
            Excel::import(new AttendanceImport(request()->course_id), request()->file('csv_file'));
    
            return response()->json(['success' => 'CSV data imported successfully.']);
        } catch (\Exception $e) {
            Log::error('CSV import failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'CSV import failed: ' . $e->getMessage()], 500);
        }
    }
    //getCsv
    public function getCsv(){
        $attendance = Attendance::all();
        return response()->json($attendance, 200);

    }
    //quizeQuestion
    public function quizeQuestion(){
        $quiz = Quiz::all();
        return response()->json($quiz, 200);
    }
    //quizeQuestionPost
    public function quizeQuestionPost(Request $request)
    {
        if ($request->hasFile('question_upload')) {
            $extension = $request->file('question_upload')->getClientOriginalExtension();
            $question = 'image/quizQuestion/'. uniqid().'.'.$extension;
            $request->file('question_upload')->move('image/quizQuestion', $question);
        }

        $quiz = new Quiz();
        $quiz->title = $request->title;
        $quiz->course_id = $request->course_id;
        $quiz->start_time = $request->start_time;
        $quiz->end_time = $request->end_time;
        $quiz->question_upload = isset($question) ? $question : null;
        $quiz->save();
        return response()->json($quiz, 200);
    }
    //quizAnswer
    public function quizAnswer(){
        $quiz = UpdateQuiz::all();
        return response()->json($quiz, 200);
    }
    //quizAnswerPost
    public function quizAnswerPost(Request $request){
        if ($request->hasFile('answer_upload')) {
            $extension = $request->file('answer_upload')->getClientOriginalExtension();
            $answer = 'image/answerUpload/'. uniqid().'.'.$extension;
            $request->file('answer_upload')->move('image/answerUpload', $answer);
        }

        $quiz = new UpdateQuiz();
        $quiz->quiz_id = $request->quiz_id;
        $quiz->student_id = $request->student_id;
        $quiz->course_id = $request->course_id;
        $quiz->answer_upload = isset($answer) ? $answer : null;
        $quiz->save();
        return response()->json($quiz, 200);
    }
    //quizAnswerMarksPost
    public function quizAnswerMarksPost(Request $request,$id){
        $quizUpdate = UpdateQuiz::find($id);
        $quizUpdate->marks = $request->marks;
        $quizUpdate->save();
        return response()->json($quizUpdate, 200);
    }
    //findQuize
    public function findQuize($course_id){
        $quiz = Quiz::where('course_id',$course_id)->get();
        return response()->json($quiz, 200);

    }
    //findAnswers
    public function findAnswers(Request $request){
        $course_id = $request->query('course_id');
        $quiz_id = $request->query('quiz_id');
        if ($course_id && $quiz_id) {
            $quizAnswer = UpdateQuiz::where('course_id', $course_id)->where('quiz_id', $quiz_id)->get();
            return response()->json($quizAnswer, 200);
        } else {
            return response()->json(['error' => 'Missing query parameters'], 400);
        }
    }
    //assignmentQuestion
    public function assignmentQuestion(){
        $assignment = Assignment::all();
        return response()->json($assignment,200);
    }
    //assignmentQuestionPost
    public function assignmentQuestionPost(Request $request){
        if ($request->hasFile('question_upload')) {
            $extension = $request->file('question_upload')->getClientOriginalExtension();
            $question = 'image/assignmentQuestion/'. uniqid().'.'.$extension;
            $request->file('question_upload')->move('image/assignmentQuestion', $question);
        }

        $quiz = new Assignment();
        $quiz->title = $request->title;
        $quiz->course_id = $request->course_id;
        $quiz->start_time = $request->start_time;
        $quiz->end_time = $request->end_time;
        $quiz->question_upload = isset($question) ? $question : null;
        $quiz->save();
        return response()->json($quiz, 200);
    }
    //assignmentAnswer
    public function assignmentAnswer()
    {
        $updateAssignment = UpdateAssignment::get();
        return response()->json($updateAssignment, 200);
    }
    //assignmentAnswerPost
    public function assignmentAnswerPost(Request $request)
    {
        if ($request->hasFile('answer_upload')) {
            $extension = $request->file('answer_upload')->getClientOriginalExtension();
            $answer = 'image/assignmentUpload/'. uniqid().'.'.$extension;
            $request->file('answer_upload')->move('image/assignmentUpload', $answer);
        }

        $quiz = new UpdateAssignment();
        $quiz->quiz_id = $request->quiz_id;
        $quiz->student_id = $request->student_id;
        $quiz->course_id = $request->course_id;
        $quiz->answer_upload = isset($answer) ? $answer : null;
        $quiz->save();
        return response()->json($quiz, 200);
    }
    //assignmentAnswerMarksPost
    public function assignmentAnswerMarksPost(Request $request,$id){
        $quizUpdate = UpdateAssignment::find($id);
        $quizUpdate->marks = $request->marks;
        $quizUpdate->save();
        return response()->json($quizUpdate, 200);
    }
    //findAssignment
    public function findAssignment($course_id){
        $quiz = Assignment::where('course_id',$course_id)->get();
        return response()->json($quiz, 200);
    }
    //findAssignmentAnswers
    public function findAssignmentAnswers(Request $request){
        $course_id = $request->query('course_id');
        $quiz_id = $request->query('quiz_id');
        if ($course_id && $quiz_id) {
            $quizAnswer = UpdateAssignment::where('course_id', $course_id)->where('quiz_id', $quiz_id)->get();
            return response()->json($quizAnswer, 200);
        } else {
            return response()->json(['error' => 'Missing query parameters'], 400);
        }
    }
    //showMarks
    public function showMarks(Request $request)
    {
        $student_id = $request->query('student_id');
        if($student_id){
            $showMarks = UpdateQuiz::where('student_id',$student_id)->get();
            return response()->json($showMarks, 200);
        }else {
            return response()->json(['error' => 'Missing query parameters'], 400);
        }
    }
    //getAssignmentMarks
    public function getAssignmentMarks(Request $request){
        $student_id = $request->query('student_id');
        if($student_id){
            $showMarks = UpdateAssignment::where('student_id',$student_id)->get();
            return response()->json($showMarks, 200);
        }else {
            return response()->json(['error' => 'Missing query parameters'], 400);
        }
    }
    
}

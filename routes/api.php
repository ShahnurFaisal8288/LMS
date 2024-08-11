<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//authentication
Route::post('/userRegister', [ApiController::class, 'userRegister'])->name('userRegister');
Route::post('/userLogin', [ApiController::class, 'userLogin'])->name('userLogin');
//bannerget
Route::get('/banner/section', [ApiController::class, 'bannerSection']);
Route::get('/banner/delete/{id}', [ApiController::class, 'bannerDelete']);
//categoryItemGet
Route::get('/category/item', [ApiController::class, 'categoryItem']);
//categoryItemDelete
Route::get('/categoryItem/delete/{id}', [ApiController::class, 'categoryItemDelete']);
//categorySectionGet
Route::get('/category/section', [ApiController::class, 'categorySection']);
//categorySectionDelete
Route::post('/categorySection/delete/{id}', [ApiController::class, 'categorySectionDelete']);

//FeaturdSectionGet
Route::get('/featured/section', [ApiController::class, 'featuredSection']);
//FeaturdSectionDelete
Route::get('/featuredSection/delete/{id}', [ApiController::class, 'featuredSectionDelete']);
//AboutusGet
Route::get('/aboutUs/section', [ApiController::class, 'aboutSection']);
//AboutusDelete
Route::post('/aboutUsDelete/{id}', [ApiController::class, 'aboutUsDelete']);
//TeacherGet
Route::get('/teacher', [ApiController::class, 'teacher']);
Route::get('/teacherDelete/{id}', [ApiController::class, 'teacherDelete']);

//CourseGet
Route::get('/course', [ApiController::class, 'course']);
//CourseEdit
Route::get('/courseDelete/{id}', [ApiController::class, 'courseDelete']);
//LessonGet
Route::get('/lesson', [ApiController::class, 'lesson']);
Route::get('/lessonDelete/{id}', [ApiController::class, 'lessonDelete']);
//role
Route::get('/role', [ApiController::class, 'roleGet']);
//studentGet
Route::get('/student', [ApiController::class, 'studentGet']);
//studentDelete
Route::get('/studentDelete/{id}', [ApiController::class, 'studentDelete']);
//sectionGet
Route::get('/section', [ApiController::class, 'sectionGet']);
//sectionDelete
Route::get('/sectionDelete/{id}', [ApiController::class, 'sectionDelete']);

//courseTeacher
Route::get('/courseTeacher', [ApiController::class, 'courseTeacherGet']);
//studentfrontend
Route::post('/studentfrontend', [ApiController::class, 'studentfrontend']);
//noticeGet
Route::get('/notice', [ApiController::class, 'notice']);
//noticePost
Route::post('/noticeDelete/{id}', [ApiController::class, 'noticeDelete']);
//online Class
Route::get('/onlineClass', [ApiController::class, 'onlineClass']);
//online Class Delete
Route::get('/onlineClassDelete/{id}', [ApiController::class, 'onlineClassDelete']);

//studentProperGet
Route::get('/onlineClassDelete', [ApiController::class, 'onlineClassDelete']);

Route::post('/lesson', [ApiController::class, 'lessonPost']);
Route::post('/import/csv', [ApiController::class, 'importCsv']);
Route::get('/getCsv', [ApiController::class, 'getCsv']);

//quizeQuestion
Route::get('/quizQuestion', [ApiController::class, 'quizeQuestion']);
Route::post('/quizQuestion', [ApiController::class, 'quizeQuestionPost']);
//quizAnswer
Route::get('/quizAnswer', [ApiController::class, 'quizAnswer']);
Route::post('/quizAnswer', [ApiController::class, 'quizAnswerPost']);
Route::post('/quizAnswerMarks/{id}', [ApiController::class, 'quizAnswerMarksPost']);
//quizGet
Route::get('/findQuize/{course_id}', [ApiController::class, 'findQuize']);
Route::get('/findAnswers', [ApiController::class, 'findAnswers']);

//AssignmentQuestion
Route::get('/assignmentQuestion', [ApiController::class, 'assignmentQuestion']);
Route::post('/assignmentQuestion', [ApiController::class, 'assignmentQuestionPost']);
//AssignmentAnswer
Route::get('/assignmentAnswer', [ApiController::class, 'assignmentAnswer']);
Route::post('/assignmentAnswer', [ApiController::class, 'assignmentAnswerPost']);
Route::post('/assignmentAnswerMarks/{id}', [ApiController::class, 'assignmentAnswerMarksPost']);

//AssignmentGet
Route::get('/findAssignment/{course_id}', [ApiController::class, 'findAssignment']);
Route::get('/findAssignmentAnswers', [ApiController::class, 'findAssignmentAnswers']);

//getQuizMark
Route::get('/showQuizMarks', [ApiController::class, 'showMarks']);
//getAssignmentMark
Route::get('/getAssignmentMarks', [ApiController::class, 'getAssignmentMarks']);


Route::group(['middleware' => ['jwtAuth']], function () {
    //authCourse
    Route::get('/authCourse', [ApiController::class, 'authCourse']);
    //authTeacherCourse
    Route::get('/authTeacherCourse', [ApiController::class, 'authTeacherCourse']);
    //studentUpdate
    Route::post('/studentUpdate/{id}', [ApiController::class, 'studentUpdate']);

    //bannerPost
    Route::post('/banner/section', [ApiController::class, 'bannerSectionPost']);
    //bannerEdit
    Route::post('/banner/edit/{id}', [ApiController::class, 'bannerEdit']);
    //categoryItemPost
    Route::post('/category/itemPost', [ApiController::class, 'categoryItemPost']);
    //categoryItemEdit
    Route::post('/categoryItem/edit/{id}', [ApiController::class, 'categoryItemEdit']);
    //categorySectionPost
    Route::post('/category/section', [ApiController::class, 'categorySectionPost']);
    //categorySectionEdIT
    Route::post('/categorySection/edit/{id}', [ApiController::class, 'categorySectionEdit']);
    //FeaturdSectionPost
    Route::post('/featured/section', [ApiController::class, 'featuredSectionPost']);
    //FeaturdSectionEdit
    Route::post('/featuredSection/edit/{id}', [ApiController::class, 'featuredSectionEdit']);
    //AboutusPost
    Route::post('/aboutUs/section', [ApiController::class, 'aboutSectionPost']);
    //AboutusEdit
    Route::post('/aboutUsEdit/{id}', [ApiController::class, 'aboutUsEdit']);
    //TeacherPost
    Route::post('/teacher', [ApiController::class, 'teacherPost']);
    //TeacherEdit
    Route::post('/teacherEdit/{id}', [ApiController::class, 'teacherEdit']);
    //CoursePost
    Route::post('/course', [ApiController::class, 'coursePost']);
    //CourseEdit
    Route::post('/courseEdit/{id}', [ApiController::class, 'courseEdit']);
    //LessonPost
    
    //LessonEdit
    Route::post('/lessonEdit/{id}', [ApiController::class, 'lessonEdit']);
    //rolePost
    Route::post('/role', [ApiController::class, 'rolePost']);

    //studentPost
    Route::post('/studentEdit/{id}', [ApiController::class, 'studentPost']);
    //sectionPost
    Route::post('/section', [ApiController::class, 'sectionPost']);
    //sectionEdit
    Route::post('/sectionEdit/{id}', [ApiController::class, 'sectionEdit']);
    //courseTeacher
    Route::post('/courseTeacher', [ApiController::class, 'courseTeacher']);
    //noticePost
    Route::post('/notice', [ApiController::class, 'noticePost']);
    //noticePost
    Route::post('/noticeEdit/{id}', [ApiController::class, 'noticeEdit']);
    //online Class Post
    Route::post('/onlineClass', [ApiController::class, 'onlineClassStore']);
    //online Class Edit
    Route::post('/onlineClassEdit/{id}', [ApiController::class, 'onlineClassEdit']);
});

<?php

use App\Controller\AdminViewController;
use Slim\App;
use App\Controller\StudentViewController;
use App\Controller\AuthController;
use App\Controller\GradesController;
use App\Controller\CoursesController;
use App\Controller\AttendanceController;
use App\Controller\TeacherController;
use App\Controller\TeacherViewController;
use App\Controller\AdminController;
use App\Controller\QueryController;

require __DIR__ . '/../vendor/autoload.php';

return function (App $app) {

    $app->get('/', [StudentViewController::class, 'HomePage']);
    $app->get('/student', [StudentViewController::class, 'showHomePage']);
    $app->get('/student/info', [StudentViewController::class, 'showStudentPage']);
    $app->post('/auth/login', [AuthController::class, 'login']);
    $app->get('/student/grades', [GradesController::class, 'showStudentGradesPage']);
    $app->get('/student/courses', [CoursesController::class, 'showStudentCoursesPage']);
    $app->get('/student/attendance', [AttendanceController::class, 'showStudentAttendancePage']);


    $app->get('/admin', [AdminViewController::class, 'showHomePage']);
    $app->get('/admin/info', [AdminViewController::class, 'showAdminPage']);
    $app->get('/admin/courses', [AdminController::class, 'showCoursesPage']);

    $app->get('/admin/courses/add', [AdminController::class, 'showAddCoursePage']);
    $app->post('/admin/courses/add', [AdminController::class, 'addCourse']);

    $app->get('/admin/courses/edit/{id}', [AdminController::class, 'showEditCoursePage']);
    $app->post('/admin/courses/edit/{id}', [AdminController::class, 'editCourse']);
    $app->post('/admin/courses/delete/{id}', [AdminController::class, 'deleteCourse']);

    $app->get('/admin/grades', [AdminController::class, 'showGradesPage']);
    $app->get('/admin/grades/add', [AdminController::class, 'showAddGradePage']);
    $app->post('/admin/grades/add', [AdminController::class, 'addGrade']);
    $app->get('/admin/grades/students/{student_id}', [AdminController::class, 'getCoursesByStudent']);

    $app->get('/admin/grades/edit/{id}', [AdminController::class, 'showEditGradePage']);
    $app->post('/admin/grades/edit/{id}', [AdminController::class, 'editGrade']);
    $app->post('/admin/grades/delete/{id}', [AdminController::class, 'deleteGrade']);



    $app->get('/admin/attendance', [AdminController::class, 'showAttendancePage']);

    $app->get('/admin/attendance/add', [AdminController::class, 'showAddAttendancePage']);
    $app->post('/admin/attendance/add', [AdminController::class, 'addAttendance']);

    $app->get('/admin/attendance/edit/{id}', [AdminController::class, 'showEditAttendancePage']);
    $app->post('/admin/attendance/update/{id}', [AdminController::class, 'updateAttendance']);

    $app->get('/admin/attendance/courses/{student_id}', [AdminController::class, 'getCoursesByStudent']);
    $app->post('/admin/attendance/delete/{id}', [AdminController::class, 'deleteAttendance']);

    $app->get('/admin/students', [AdminController::class, 'showStudentsPage']);

    $app->get('/admin/students/add', [AdminController::class, 'showAddStudentPage']);
    $app->post('/admin/students/add', [AdminController::class, 'addStudent']);

    $app->get('/admin/students/edit/{id}', [AdminController::class, 'showEditStudentPage']);
    $app->post('/admin/students/edit/{id}', [AdminController::class, 'editStudent']);

    $app->post('/admin/students/delete/{id}', [AdminController::class, 'deleteStudent']);


    $app->get('/admin/enrollments', [AdminController::class, 'showEnrollmentsPage']);
    $app->get('/admin/enrollments/add', [AdminController::class, 'showAddEnrollmentPage']);
    $app->post('/admin/enrollments/add', [AdminController::class, 'addEnrollment']);
    $app->post('/admin/enrollments/delete/{id}', [AdminController::class, 'deleteEnrollment']);
    $app->get('/admin/enrollments/edit/{id}', [AdminController::class, 'showEditEnrollmentPage']);
    $app->post('/admin/enrollments/edit/{id}', [AdminController::class, 'editEnrollment']);

    $app->get('/admin/teachers', [AdminController::class, 'showTeachersPage']);
    $app->get('/admin/teachers/add', [AdminController::class, 'showAddTeacherPage']);
    $app->post('/admin/teachers/add', [AdminController::class, 'addTeacher']);
    $app->get('/admin/teachers/edit/{id}', [AdminController::class, 'showEditTeacherPage']);
    $app->post('/admin/teachers/edit/{id}', [AdminController::class, 'editTeacher']);
    $app->post('/admin/teachers/delete/{id}', [AdminController::class, 'deleteTeacher']);

    $app->get('/admin/query', [QueryController::class, 'showQueryPage']);
    $app->post('/admin/query/run', [QueryController::class, 'runQuery']);
    $app->get('/admin/query/auth', [QueryController::class, 'showQueryAuthPage']);
    $app->post('/admin/query/auth', [QueryController::class, 'processQueryAuth']);

    $app->get('/teacher', [TeacherViewController::class, 'showHomePage']);
    $app->get('/teacher/info', [TeacherViewController::class, 'showTeacherPage']);
    $app->get('/teacher/courses', [CoursesController::class, 'showTeacherCoursesPage']);
    $app->get('/teacher/grades', [GradesController::class, 'showTeacherGradesPage']);
    $app->get('/teacher/attendance', [AttendanceController::class, 'showTeacherAttendancePage']);


    $app->get('/teacher/grades/add', [GradesController::class, 'showAddGradePage']);
    $app->post('/teacher/grades/add', [GradesController::class, 'addGrade']);


    $app->get('/teacher/grades/students/{course_id}', [GradesController::class, 'getStudentsByCourse']);
    $app->get('/teacher/grades/edit/{grade_id}', [GradesController::class, 'showEditGradePage']);
    $app->post('/teacher/grades/update', [GradesController::class, 'updateGrade']);
    $app->post('/teacher/grades/delete/{grade_id}', [GradesController::class, 'deleteGrade']);


    $app->get('/teacher/attendance/add', [AttendanceController::class, 'showAddAttendancePage']);
    $app->post('/teacher/attendance/add', [AttendanceController::class, 'addAttendance']);
    $app->get('/teacher/attendance/students/{course_id}', [AttendanceController::class, 'getStudentsByCourse']);
    $app->post('/teacher/attendance/update', [AttendanceController::class, 'updateAttendance']);
    $app->post('/teacher/attendance/delete/{attendance_id}', [AttendanceController::class, 'deleteAttendance']);
    $app->get('/teacher/attendance/check-existing-date', \App\Controller\AttendanceController::class . ':checkExistingDate');

    $app->get('/auth/logout', [AuthController::class, 'logout']);
};

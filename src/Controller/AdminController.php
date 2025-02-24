<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;
use PDOException;

class AdminController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    public function fetchAllStudents()
    {
        $query = "SELECT id, full_name FROM students";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllCourses()
    {
        $query = "SELECT id, course_name FROM courses";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchStudentById($studentId)
    {
        $query = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function showCoursesPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }


        $query = "SELECT c.id, c.course_name, c.course_code, c.semester, c.credits, 
                     t.full_name AS teacher_name
              FROM courses c
              LEFT JOIN teachers t ON c.teacher_id = t.id";

        $stmt = $this->db->query($query);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/courses.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function showAddCoursePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }


        $query = "SELECT id, full_name FROM teachers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/add_course.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function addCourse(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        if (empty($data['course_name']) || empty($data['course_code']) || empty($data['teacher_id']) || empty($data['semester']) || empty($data['credits'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', '/admin/courses/add')->withStatus(302);
        }

        $query = "INSERT INTO courses (course_name, course_code, teacher_id, semester, credits) 
                  VALUES (:course_name, :course_code, :teacher_id, :semester, :credits)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':course_name' => $data['course_name'],
            ':course_code' => $data['course_code'],
            ':teacher_id' => $data['teacher_id'],
            ':semester' => $data['semester'],
            ':credits' => $data['credits']
        ]);

        $_SESSION['success'] = "Course added successfully!";
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }
    public function showEditCoursePage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $courseId = $args['id'];


        $query = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            $_SESSION['error'] = "Course not found!";
            return $response->withHeader('Location', '/admin/courses')->withStatus(302);
        }


        $query = "SELECT id, full_name FROM teachers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_course.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function editCourse(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $courseId = $args['id'];
        $data = $request->getParsedBody();


        if (empty($data['course_name']) || empty($data['course_code']) || empty($data['teacher_id']) || empty($data['semester']) || empty($data['credits'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', "/admin/courses/edit/$courseId")->withStatus(302);
        }


        $query = "UPDATE courses SET 
                  course_name = :course_name, 
                  course_code = :course_code, 
                  teacher_id = :teacher_id, 
                  semester = :semester, 
                  credits = :credits 
              WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':course_name' => $data['course_name'],
            ':course_code' => $data['course_code'],
            ':teacher_id' => $data['teacher_id'],
            ':semester' => $data['semester'],
            ':credits' => $data['credits'],
            ':id' => $courseId
        ]);

        $_SESSION['success'] = "Course updated successfully!";
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }

    public function deleteCourse(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $courseId = $args['id'];
        $query = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $courseId]);

        $_SESSION['success'] = "Course deleted successfully!";
        return $response->withHeader('Location', '/admin/courses')->withStatus(302);
    }

    public function showGradesPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT g.id, s.full_name as student_name, c.course_name, g.midterm, g.final, g.project, g.total_grade
              FROM grades g
              JOIN enrollments e ON g.enrollment_id = e.id
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id";
        $stmt = $this->db->query($query);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/grades.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showAddGradePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $students = $this->fetchAllStudents();
        $courses = $this->fetchAllCourses();

        ob_start();
        include __DIR__ . '/../../templates/admins/add_grade.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function addGrade(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        if (empty($data['student_id']) || empty($data['course_id']) || empty($data['midterm']) || empty($data['final']) || empty($data['project'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', '/admin/grades/add')->withStatus(302);
        }


        $enrollmentQuery = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->db->prepare($enrollmentQuery);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id']
        ]);
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            $_SESSION['error'] = "This student is not enrolled in the selected course!";
            return $response->withHeader('Location', '/admin/grades/add')->withStatus(302);
        }


        $enrollment_id = $enrollment['id'];


        $query = "INSERT INTO grades (enrollment_id, midterm, final, project) 
              VALUES (:enrollment_id, :midterm, :final, :project)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':enrollment_id' => $enrollment_id,
            ':midterm' => $data['midterm'],
            ':final' => $data['final'],
            ':project' => $data['project']
        ]);

        $_SESSION['success'] = "Grade added successfully!";
        return $response->withHeader('Location', '/admin/grades')->withStatus(302);
    }
    public function showEditGradePage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $gradeId = $args['id'];


        $query = "SELECT g.id, g.midterm, g.final, g.project, g.total_grade, 
                     s.full_name as student_name, c.course_name
              FROM grades g
              JOIN enrollments e ON g.enrollment_id = e.id
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id
              WHERE g.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $gradeId]);
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$grade) {
            $_SESSION['error'] = "Grade not found!";
            return $response->withHeader('Location', '/admin/grades')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_grade.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function editGrade(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $gradeId = $args['id'];
        $data = $request->getParsedBody();

        $query = "UPDATE grades SET midterm = :midterm, final = :final, project = :project WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':midterm' => $data['midterm'],
            ':final' => $data['final'],
            ':project' => $data['project'],
            ':id' => $gradeId
        ]);

        $_SESSION['success'] = "Grade updated successfully!";
        return $response->withHeader('Location', '/admin/grades')->withStatus(302);
    }
    public function deleteGrade(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $gradeId = $args['id'];


        $query = "SELECT id FROM grades WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $gradeId]);
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$grade) {
            $_SESSION['error'] = "Grade not found!";
            return $response->withHeader('Location', '/admin/grades')->withStatus(302);
        }


        $deleteQuery = "DELETE FROM grades WHERE id = :id";
        $stmt = $this->db->prepare($deleteQuery);
        $stmt->execute([':id' => $gradeId]);

        $_SESSION['success'] = "Grade deleted successfully!";
        return $response->withHeader('Location', '/admin/grades')->withStatus(302);
    }
    public function getCoursesByStudent(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $args['student_id'];

        $query = "SELECT c.id, c.course_name 
              FROM enrollments e
              JOIN courses c ON e.course_id = c.id
              WHERE e.student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $payload = json_encode($courses, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function showAttendancePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT a.id, s.full_name, s.student_number, a.date, a.status, c.course_name 
              FROM attendance a
              JOIN enrollments e ON a.enrollment_id = e.id
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id";
        $stmt = $this->db->query($query);
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function showAddAttendancePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentsQuery = "SELECT id, full_name FROM students";
        $stmt = $this->db->query($studentsQuery);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/add_attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function addAttendance(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();

        if (empty($data['student_id']) || empty($data['course_id']) || empty($data['date']) || empty($data['status'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', '/admin/attendance/add')->withStatus(302);
        }


        $enrollmentQuery = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->db->prepare($enrollmentQuery);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id']
        ]);
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            $_SESSION['error'] = "This student is not enrolled in the selected course!";
            return $response->withHeader('Location', '/admin/attendance/add')->withStatus(302);
        }

        $enrollment_id = $enrollment['id'];

        $query = "INSERT INTO attendance (enrollment_id, date, status) 
              VALUES (:enrollment_id, :date, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':enrollment_id' => $enrollment_id,
            ':date' => $data['date'],
            ':status' => $data['status']
        ]);

        $_SESSION['success'] = "Attendance added successfully!";
        return $response->withHeader('Location', '/admin/attendance')->withStatus(302);
    }
    public function updateAttendance(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $attendanceId = $args['id'];
        $data = $request->getParsedBody();

        $query = "UPDATE attendance SET date = :date, status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':date' => $data['date'],
            ':status' => $data['status'],
            ':id' => $attendanceId
        ]);

        $_SESSION['success'] = "Attendance updated successfully!";
        return $response->withHeader('Location', '/admin/attendance')->withStatus(302);
    }
    public function deleteAttendance(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $attendanceId = $args['id'];

        $query = "DELETE FROM attendance WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $attendanceId]);

        $_SESSION['success'] = "Attendance deleted successfully!";
        return $response->withHeader('Location', '/admin/attendance')->withStatus(302);
    }
    public function showEditAttendancePage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $attendanceId = $args['id'];


        $query = "SELECT a.id, a.date, a.status, s.full_name as student_name, c.course_name
              FROM attendance a
              JOIN enrollments e ON a.enrollment_id = e.id
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id
              WHERE a.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $attendanceId]);
        $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$attendance) {
            $_SESSION['error'] = "Attendance record not found!";
            return $response->withHeader('Location', '/admin/attendance')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }





    public function showEnrollmentsPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT e.id, s.full_name as student_name, c.course_name, e.enrolled_at
              FROM enrollments e
              JOIN students s ON e.student_id = s.id
              JOIN courses c ON e.course_id = c.id";
        $stmt = $this->db->query($query);
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/enrollments.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showAddEnrollmentPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }


        $query = "SELECT id, full_name FROM students";
        $stmt = $this->db->query($query);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $query = "SELECT id, course_name FROM courses";
        $stmt = $this->db->query($query);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/add_enrollment.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function addEnrollment(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        if (empty($data['student_id']) || empty($data['course_id'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', '/admin/enrollments/add')->withStatus(302);
        }


        $checkQuery = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id']
        ]);

        if ($stmt->fetch()) {
            $_SESSION['error'] = "This student is already enrolled in this course!";
            return $response->withHeader('Location', '/admin/enrollments/add')->withStatus(302);
        }


        $query = "INSERT INTO enrollments (student_id, course_id) VALUES (:student_id, :course_id)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id']
        ]);

        $_SESSION['success'] = "Enrollment added successfully!";
        return $response->withHeader('Location', '/admin/enrollments')->withStatus(302);
    }

    public function deleteEnrollment(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "DELETE FROM enrollments WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $args['id']]);

        return $response->withHeader('Location', '/admin/enrollments')->withStatus(302);
    }
    public function showEditEnrollmentPage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $enrollmentId = $args['id'];


        $query = "SELECT * FROM enrollments WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $enrollmentId]);
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            $_SESSION['error'] = "Enrollment not found!";
            return $response->withHeader('Location', '/admin/enrollments')->withStatus(302);
        }


        $query = "SELECT id, full_name FROM students";
        $stmt = $this->db->query($query);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $query = "SELECT id, course_name FROM courses";
        $stmt = $this->db->query($query);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_enrollment.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function editEnrollment(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $enrollmentId = $args['id'];
        $data = $request->getParsedBody();


        if (empty($data['student_id']) || empty($data['course_id'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', "/admin/enrollments/edit/$enrollmentId")->withStatus(302);
        }


        $query = "UPDATE enrollments SET student_id = :student_id, course_id = :course_id WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id'],
            ':id' => $enrollmentId
        ]);

        $_SESSION['success'] = "Enrollment updated successfully!";
        return $response->withHeader('Location', '/admin/enrollments')->withStatus(302);
    }
    public function showStudentsPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT * FROM students";
        $stmt = $this->db->query($query);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/students.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showAddStudentPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/add_student.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function addStudent(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        if (empty($data['username']) || empty($data['password']) || empty($data['full_name']) || empty($data['student_number'])) {
            $_SESSION['error'] = "All required fields must be filled!";
            return $response->withHeader('Location', '/admin/students/add')->withStatus(302);
        }


        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);


        $query = "INSERT INTO students (username, password, full_name, student_number, birth_date, email, address) 
              VALUES (:username, :password, :full_name, :student_number, :birth_date, :email, :address)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':username' => $data['username'],
            ':password' => $hashedPassword,
            ':full_name' => $data['full_name'],
            ':student_number' => $data['student_number'],
            ':birth_date' => $data['birth_date'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null
        ]);

        $_SESSION['success'] = "Student added successfully!";
        return $response->withHeader('Location', '/admin/students')->withStatus(302);
    }

    public function showEditStudentPage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $args['id'];

        $query = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $_SESSION['error'] = "Student not found!";
            return $response->withHeader('Location', '/admin/students')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_student.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function editStudent(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $args['id'];
        $data = $request->getParsedBody();


        $query = "UPDATE students SET 
              full_name = :full_name, 
              student_number = :student_number, 
              birth_date = :birth_date, 
              email = :email, 
              address = :address
              WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':student_number' => $data['student_number'],
            ':birth_date' => $data['birth_date'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
            ':id' => $studentId
        ]);

        $_SESSION['success'] = "Student updated successfully!";
        return $response->withHeader('Location', '/admin/students')->withStatus(302);
    }

    public function deleteStudent(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $args['id'];

        $query = "DELETE FROM students WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $studentId]);

        $_SESSION['success'] = "Student deleted successfully!";
        return $response->withHeader('Location', '/admin/students')->withStatus(302);
    }

    public function showTeachersPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT * FROM teachers";
        $stmt = $this->db->query($query);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/teachers.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showAddTeacherPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/add_teacher.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function addTeacher(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();

        if (empty($data['full_name']) || empty($data['email']) || empty($data['password']) || empty($data['username'])) {
            $_SESSION['error'] = "All fields are required!";
            return $response->withHeader('Location', '/admin/teachers/add')->withStatus(302);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO teachers (username, password, full_name, department, email) 
              VALUES (:username, :password, :full_name, :department, :email)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':username' => $data['username'],
            ':password' => $hashedPassword,
            ':full_name' => $data['full_name'],
            ':department' => $data['department'],
            ':email' => $data['email']
        ]);

        $_SESSION['success'] = "Teacher added successfully!";
        return $response->withHeader('Location', '/admin/teachers')->withStatus(302);
    }
    public function showEditTeacherPage(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $args['id'];

        $query = "SELECT * FROM teachers WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $teacherId]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            $_SESSION['error'] = "Teacher not found!";
            return $response->withHeader('Location', '/admin/teachers')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/edit_teacher.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function editTeacher(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $args['id'];
        $data = $request->getParsedBody();

        if (empty($data['full_name']) || empty($data['email']) || empty($data['department'])) {
            $_SESSION['error'] = "Full Name, Department, and Email are required!";
            return $response->withHeader('Location', "/admin/teachers/edit/$teacherId")->withStatus(302);
        }

        $query = "UPDATE teachers SET full_name = :full_name, department = :department, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':department' => $data['department'],
            ':email' => $data['email'],
            ':id' => $teacherId
        ]);

        $_SESSION['success'] = "Teacher updated successfully!";
        return $response->withHeader('Location', '/admin/teachers')->withStatus(302);
    }
    public function deleteTeacher(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $args['id'];

        $query = "DELETE FROM teachers WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $teacherId]);

        $_SESSION['success'] = "Teacher deleted successfully!";
        return $response->withHeader('Location', '/admin/teachers')->withStatus(302);
    }
}

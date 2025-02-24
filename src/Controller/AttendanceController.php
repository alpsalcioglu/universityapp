<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class AttendanceController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function showStudentAttendancePage(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $_SESSION['user_id'];


        $query = "SELECT a.date, a.status, c.course_name
                  FROM attendance a
                  JOIN enrollments e ON a.enrollment_id = e.id
                  JOIN courses c ON e.course_id = c.id
                  WHERE e.student_id = :student_id
                  ORDER BY a.date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/students/attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showTeacherAttendancePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $_SESSION['user_id'];

        $query = "SELECT a.id, s.full_name, s.student_number, a.date, a.status, c.course_name, c.id as course_id, s.id as student_id
          FROM attendance a
          JOIN enrollments e ON a.enrollment_id = e.id
          JOIN students s ON e.student_id = s.id
          JOIN courses c ON e.course_id = c.id
          WHERE c.teacher_id = :teacher_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':teacher_id', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/teachers/attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }


    public function getStudentsByCourse(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $courseId = $args['course_id'];
        $queryParams = $request->getQueryParams();
        $date = $queryParams['date'] ?? null;

        // 🔥 Eğer tarih NULL ise, SQL sorgusunda tarih kontrolünü kaldır
        if ($date) {
            $query = "SELECT s.id, s.full_name, s.student_number, 
                             CASE 
                                 WHEN a.date IS NOT NULL THEN 'exists' 
                                 ELSE 'not_exists' 
                             END AS attendance_status
                      FROM students s
                      JOIN enrollments e ON s.id = e.student_id
                      LEFT JOIN attendance a ON a.enrollment_id = e.id AND a.date = :date
                      WHERE e.course_id = :course_id";
        } else {
            $query = "SELECT s.id, s.full_name, s.student_number, 'not_exists' AS attendance_status
                      FROM students s
                      JOIN enrollments e ON s.id = e.student_id
                      WHERE e.course_id = :course_id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        if ($date) {
            $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        }

        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function showAddAttendancePage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "SELECT id, course_name FROM courses WHERE teacher_id = :teacher_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':teacher_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/teachers/add_attendance.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }


    public function addAttendance(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        $checkQuery = "SELECT id FROM attendance WHERE enrollment_id = 
                   (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id)
                   AND date = :date";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id'],
            ':date' => $data['date']
        ]);

        if ($checkStmt->fetch()) {
            $_SESSION['error'] = "This student already has attendance for this date!";
            return $response->withHeader('Location', '/teacher/attendance/add')->withStatus(302);
        }


        $query = "INSERT INTO attendance (enrollment_id, date, status)
              VALUES (
                  (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id),
                  :date, :status
              )";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id'],
            ':date' => $data['date'],
            ':status' => $data['status']
        ]);

        $_SESSION['success'] = "Attendance added successfully!";
        return $response->withHeader('Location', '/teacher/attendance')->withStatus(302);
    }


    public function updateAttendance(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $parsedBody = $request->getParsedBody();


        $checkQuery = "SELECT id FROM attendance 
                       WHERE enrollment_id = 
                       (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id)
                       AND date = :date AND id != :attendance_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([
            ':student_id' => $parsedBody['student_id'],
            ':course_id' => $parsedBody['course_id'],
            ':date' => $parsedBody['date'],
            ':attendance_id' => $parsedBody['attendance_id']
        ]);

        if ($checkStmt->fetch()) {
            $_SESSION['error'] = "This student already has attendance for this date!";
            return $response->withHeader('Location', '/teacher/attendance')->withStatus(302);
        }


        $query = "UPDATE attendance SET date = :date, status = :status 
                  WHERE id = :attendance_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':date' => $parsedBody['date'],
            ':status' => $parsedBody['status'],
            ':attendance_id' => $parsedBody['attendance_id']
        ]);

        $_SESSION['success'] = "Attendance updated successfully!";
        return $response->withHeader('Location', '/teacher/attendance')->withStatus(302);
    }


    public function deleteAttendance(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $query = "DELETE FROM attendance WHERE id = :attendance_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':attendance_id' => $args['attendance_id']]);

        return $response->withHeader('Location', '/teacher/attendance')->withStatus(302);
    }
    public function checkExistingDate(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $queryParams = $request->getQueryParams();
        $date = $queryParams['date'] ?? null;
        $attendanceId = $queryParams['attendance_id'] ?? null;
        $studentId = $queryParams['student_id'] ?? null;
        $courseId = $queryParams['course_id'] ?? null;

        if (!$date || !$attendanceId || !$studentId || !$courseId) {
            $response->getBody()->write(json_encode(['exists' => false]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $checkQuery = "SELECT id FROM attendance 
                   WHERE date = :date AND enrollment_id = 
                   (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id)
                   AND id != :attendance_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([
            ':date' => $date,
            ':attendance_id' => $attendanceId,
            ':student_id' => $studentId,
            ':course_id' => $courseId
        ]);

        $exists = $checkStmt->fetch() ? true : false;

        $response->getBody()->write(json_encode(['exists' => $exists]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}

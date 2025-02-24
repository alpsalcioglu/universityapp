<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class GradesController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function showStudentGradesPage(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $_SESSION['user_id'];

        $query = "SELECT g.midterm, g.final, g.project, g.total_grade, c.course_name 
                  FROM grades g
                  JOIN enrollments e ON g.enrollment_id = e.id
                  JOIN courses c ON e.course_id = c.id
                  WHERE e.student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/students/grades.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }


    public function showTeacherGradesPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $_SESSION['user_id'];

        $query = "SELECT g.id, s.full_name, s.student_number, g.midterm, g.final, g.project, g.total_grade, c.course_name 
                  FROM grades g
                  JOIN enrollments e ON g.enrollment_id = e.id
                  JOIN students s ON e.student_id = s.id
                  JOIN courses c ON e.course_id = c.id
                  WHERE c.teacher_id = :teacher_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':teacher_id', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/teachers/grades.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }


    public function showAddGradePage(Request $request, Response $response)
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
        include __DIR__ . '/../../templates/teachers/add_grade.php';
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


        $query = "SELECT DISTINCT s.id, s.full_name, s.student_number 
                  FROM students s
                  JOIN enrollments e ON s.id = e.student_id
                  WHERE e.course_id = :course_id
                  AND NOT EXISTS (
                      SELECT 1 FROM grades g WHERE g.enrollment_id = e.id
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function addGrade(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();


        $checkQuery = "SELECT id FROM grades WHERE enrollment_id = 
                      (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id)";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([
            ':student_id' => $data['student_id'],
            ':course_id' => $data['course_id']
        ]);

        if ($checkStmt->fetch()) {
            return $response->withHeader('Location', '/teacher/grades')->withStatus(302);
        }

        $query = "INSERT INTO grades (enrollment_id, midterm, final, project)
                  VALUES (
                      (SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id),
                      :midterm, :final, :project
                      
                  )";

        $stmt = $this->db->prepare($query);
        $stmt->execute($data);
        return $response->withHeader('Location', '/teacher/grades')->withStatus(302);
    }


    public function updateGrade(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $parsedBody = $request->getParsedBody();

        $query = "UPDATE grades SET midterm = :midterm, final = :final, project = :project 
                  WHERE id = :grade_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute($parsedBody);

        return $response->withHeader('Location', '/teacher/grades')->withStatus(302);
    }

    public function deleteGrade(Request $request, Response $response, array $args)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $gradeId = $args['grade_id'];

        $query = "DELETE FROM grades WHERE id = :grade_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':grade_id', $gradeId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response->getBody()->write(json_encode(["success" => true]));
        } else {
            $response->getBody()->write(json_encode(["success" => false]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

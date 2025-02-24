<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class CoursesController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function showStudentCoursesPage(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $_SESSION['user_id'];


        $query = "SELECT c.id, c.course_name, c.course_code, c.credits, c.semester, 
                         t.full_name AS instructor
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  LEFT JOIN teachers t ON c.teacher_id = t.id
                  WHERE e.student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/students/courses.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showTeacherCoursesPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        $studentId = $_SESSION['user_id'];


        $query = "SELECT c.id, c.course_name, c.course_code, c.credits, c.semester, 
                         t.full_name AS instructor
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  LEFT JOIN teachers t ON c.teacher_id = t.id
                  WHERE e.student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/teachers/courses.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
}

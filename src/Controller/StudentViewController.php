<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class StudentViewController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function showStudentPage(Request $request, Response $response)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $studentId = $_SESSION['user_id'];

        $query = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/students/student.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function showHomePage(Request $request, Response $response)
    {
        ob_start();
        include __DIR__ . '/../../templates/homepage.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function HomePage(Request $request, Response $response)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/student')->withStatus(302);
        }
        ob_start();
        include __DIR__ . '/../../templates/login.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html');
    }
}

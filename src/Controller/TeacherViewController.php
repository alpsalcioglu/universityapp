<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class TeacherViewController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function showTeacherPage(Request $request, Response $response)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $teacherId = $_SESSION['user_id'];

        $query = "SELECT * FROM teachers WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/teachers/teacher.php';
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
            return $response->withHeader('Location', '/teacher')->withStatus(302);
        }
        ob_start();
        include __DIR__ . '/../../templates/login.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html');
    }
}

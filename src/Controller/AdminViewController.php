<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class AdminViewController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function showAdminPage(Request $request, Response $response)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $adminId = $_SESSION['user_id'];

        $query = "SELECT * FROM admins WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../templates/admins/admin.php';
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

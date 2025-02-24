<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;
use PDOException;

class QueryController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function showQueryPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        if (!isset($_SESSION['query_access']) || $_SESSION['query_access'] !== true) {
            return $response->withHeader('Location', '/admin/query/auth')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/query.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function runQuery(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $parsedBody = $request->getParsedBody();
        $query = trim($parsedBody['query']);


        $dbCheckQuery = "SELECT DATABASE() as db_name";
        $stmt = $this->db->query($dbCheckQuery);
        $dbName = $stmt->fetch(PDO::FETCH_ASSOC)['db_name'];

        if ($dbName !== 'university_app') {
            $response->getBody()->write(json_encode(['error' => 'Queries can only be executed within the university_app database.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }


        $forbiddenPatterns = [
            '/\bDROP\s+TABLE\b/i',
            '/\bALTER\s+TABLE\b/i'
        ];

        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $query)) {
                $response->getBody()->write(json_encode(['error' => 'Table deletion or modification is not allowed.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
        }

        try {
            $stmt = $this->db->query($query);


            if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)/i', $query)) {
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response->getBody()->write(json_encode(['data' => $data]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['message' => 'Query executed successfully.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    public function showQueryAuthPage(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../templates/admins/query_auth.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function processQueryAuth(Request $request, Response $response)
    {
        session_start();
        if ($_SESSION['user_type'] !== 'admin') {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $data = $request->getParsedBody();
        $password = trim($data['password']);


        $query = "SELECT password FROM admins WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$admin) {
            $_SESSION['auth_error'] = "User not found!";
            return $response->withHeader('Location', '/admin/query/auth')->withStatus(302);
        }


        if (password_get_info($admin['password'])['algo'] > 0) {
            $isValid = password_verify($password, $admin['password']);
        } else {
            $isValid = $password === $admin['password'];
        }

        if (!$isValid) {
            $_SESSION['auth_error'] = "Incorrect password!";
            return $response->withHeader('Location', '/admin/query/auth')->withStatus(302);
        }


        $_SESSION['query_access'] = true;
        return $response->withHeader('Location', '/admin/query')->withStatus(302);
    }
}

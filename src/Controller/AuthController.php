<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;

class AuthController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function login(Request $request, Response $response)
    {
        session_start();
        $data = $request->getParsedBody();

        $userType = $data['user_type'];
        $username = $data['username'];
        $password = $data['password'];

        if ($userType === 'student') {
            $query = "SELECT * FROM students WHERE student_number = :username OR email = :email";
        } elseif ($userType === 'teacher') {
            $query = "SELECT * FROM teachers WHERE username = :username OR email = :email";
        } elseif ($userType === 'admin') {
            $query = "SELECT * FROM admins WHERE username = :username OR email = :email";
        } else {
            $_SESSION['login_error'] = "Invalid user type.";
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':email', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_type'] = $userType;

            if ($userType === 'student') {
                return $response->withHeader('Location', '/student')->withStatus(302);
            } elseif ($userType === 'teacher') {
                return $response->withHeader('Location', '/teacher')->withStatus(302);
            } elseif ($userType === 'admin') {
                return $response->withHeader('Location', '/admin')->withStatus(302);
            }
        }

        $_SESSION['login_error'] = "Invalid credentials. Please try again.";
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logout(Request $request, Response $response)
    {
        session_start();
        session_destroy();
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}

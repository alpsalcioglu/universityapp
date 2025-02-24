<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;
use PDOException;

class TeacherController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function fetchAllTeachers()
    {
        try {
            $query = "SELECT * FROM teachers;";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function fetchTeacherById($teacherId)
    {
        $query = "SELECT * FROM teachers WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

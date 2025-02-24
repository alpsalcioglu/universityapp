<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PDO;
use PDOException;

class StudentController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function fetchAllStudents()
    {
        try {
            $query = "SELECT * FROM students;";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function fetchStudentById($studentId)
    {
        $query = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

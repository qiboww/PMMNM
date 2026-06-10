<?php
require_once 'app/config/database.php';

class UserController {
    private $conn;

    public function __construct() {
        // Chỉ cho phép admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function list() {
        include 'app/views/user/list.php';
    }

    public function toggleLock($id) {
        if ($id == $_SESSION['user']['id']) {
            echo "<script>alert('Không thể tự khóa tài khoản của mình!'); window.location.href='/User/list';</script>";
            exit();
        }

        $stmt = $this->conn->prepare("UPDATE users SET is_locked = NOT is_locked WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: /User/list');
        exit();
    }
}
?>
<?php
require_once 'app/config/database.php';

class OrderController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function list() {
        // Chỉ admin mới xem được tất cả đơn hàng
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }
        include 'app/views/order/list.php';
    }

    public function detail($id) {
        // Chỉ admin mới xem chi tiết qua trang này
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }
        include 'app/views/order/detail.php';
    }

    public function history() {
        // User phải đăng nhập để xem lịch sử đơn hàng của mình
        if (!isset($_SESSION['user'])) {
            header('Location: /Auth/login');
            exit();
        }
        include 'app/views/order/history.php';
    }
}
?>
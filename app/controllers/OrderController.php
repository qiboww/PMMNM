<?php
require_once 'app/config/database.php';

class OrderController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function list() {
        include 'app/views/order/list.php';
    }

    public function detail($id) {
        include 'app/views/order/detail.php';
    }

    public function history() {
        include 'app/views/order/history.php';
    }
}
?>
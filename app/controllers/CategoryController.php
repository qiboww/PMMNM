<?php
require_once 'app/config/database.php';

class CategoryController {
    private $conn;

    public function __construct() {

            $db = new Database();
            $this->conn = $db->getConnection();
        }

    private function getCategoriesForHeader() {
        $stmt = $this->conn->prepare("SELECT * FROM category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function index() { $this->list(); }

    public function list() {
        $categories = $this->getCategoriesForHeader();
        include 'app/views/category/list.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            if (!empty($name)) {
                $stmt = $this->conn->prepare("INSERT INTO category (name, description) VALUES (:name, :description)");
                $stmt->execute([':name' => $name, ':description' => $description]);
                header('Location: /Category/list'); exit();
            }
        }
        $categories = $this->getCategoriesForHeader();
        include 'app/views/category/add.php';
    }

    public function edit($id) {
        $categories = $this->getCategoriesForHeader();
        include 'app/views/category/edit.php';
    }
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM category WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: /Category/list'); exit();
    }
}
?>
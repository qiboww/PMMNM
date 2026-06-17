<?php
require_once 'app/config/database.php';

class ProductController {
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
            
        $category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? $_GET['category_id'] : null;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;

        $query = "SELECT * FROM product WHERE 1=1";
        $params = [];

        if ($category_id) {
            $query .= " AND category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        if ($keyword !== '') {
            $query .= " AND name LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if ($min_price !== null) {
            $query .= " AND price >= :min_price";
            $params[':min_price'] = $min_price;
        }
        if ($max_price !== null) {
            $query .= " AND price <= :max_price";
            $params[':max_price'] = $max_price;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
            
        include 'app/views/product/list.php';
    }

    public function add() {
        $categories = $this->getCategoriesForHeader();
        include 'app/views/product/add.php';
    }

    public function edit($id) {
        $categories = $this->getCategoriesForHeader();
        include 'app/views/product/edit.php';
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM product WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: /Product/list'); exit();
    }
    public function show($id) {
        $categories = $this->getCategoriesForHeader();
        include 'app/views/product/show.php';
    }
}
?>
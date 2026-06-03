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
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
            $imageName = 'default.png';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
            }

            $stmt = $this->conn->prepare("INSERT INTO product (name, description, price, image, category_id) VALUES (:name, :desc, :price, :img, :cat_id)");
            $stmt->execute([':name'=>$name, ':desc'=>$description, ':price'=>$price, ':img'=>$imageName, ':cat_id'=>$category_id]);
            header('Location: /Product/list'); exit();
        }
        $categories = $this->getCategoriesForHeader();
        include 'app/views/product/add.php';
    }

    public function edit($id) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imageUpdate = "";
            $params = [':name'=>$_POST['name'], ':desc'=>$_POST['description'], ':price'=>$_POST['price'], ':cat_id'=>$_POST['category_id'], ':id'=>$id];
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/';
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                    $imageUpdate = ", image = :img";
                    $params[':img'] = $imageName;
                }
            }

            $stmt = $this->conn->prepare("UPDATE product SET name=:name, description=:desc, price=:price, category_id=:cat_id" . $imageUpdate . " WHERE id=:id");
            $stmt->execute($params);
            header('Location: /Product/list'); exit();
        }

        $stmt = $this->conn->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_OBJ);
        
        $categories = $this->getCategoriesForHeader();
        include 'app/views/product/edit.php';
    }

    public function delete($id) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Auth/login');
            exit();
        }
        $stmt = $this->conn->prepare("DELETE FROM product WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: /Product/list'); exit();
    }
    public function show($id) {
        
        $stmt = $this->conn->prepare("SELECT p.*, c.name as category_name FROM product p LEFT JOIN category c ON p.category_id = c.id WHERE p.id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_OBJ);

        $categories = $this->getCategoriesForHeader();

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            die('Không tìm thấy sản phẩm');
        }
    }
}
?>
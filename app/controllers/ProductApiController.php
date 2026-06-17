<?php
require_once 'app/config/database.php';
require_once 'app/helpers/AuthMiddleware.php';

class ProductApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // GET: /api/product?search=abc&category_id=1&sort=asc
    public function index() {
        $query = "SELECT * FROM product WHERE 1=1";
        $params = [];

        if (isset($_GET['search']) && trim($_GET['search']) !== '') {
            $query .= " AND name LIKE :search";
            $params[':search'] = "%" . trim($_GET['search']) . "%";
        }

        if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
            $query .= " AND category_id = :category_id";
            $params[':category_id'] = $_GET['category_id'];
        }

        if (isset($_GET['sort'])) {
            $sort = strtolower($_GET['sort']) === 'asc' ? 'ASC' : 'DESC';
            $query .= " ORDER BY price $sort";
        } else {
            $query .= " ORDER BY id DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(["status" => "success", "data" => $products]);
    }

    // GET: /api/product/{id}
    public function show($id) {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            echo json_encode(["status" => "success", "data" => $product]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Không tìm thấy sản phẩm"]);
        }
    }

    private function validateProductData($data) {
        if (empty(trim($data->name ?? ''))) {
            return "Tên sản phẩm không được rỗng";
        }
        if (!isset($data->price) || !is_numeric($data->price) || $data->price <= 0) {
            return "Giá phải là số và lớn hơn 0";
        }
        if (empty($data->category_id)) {
            return "Danh mục sản phẩm không được rỗng";
        }
        
        $stmt = $this->conn->prepare("SELECT id FROM category WHERE id = :id");
        $stmt->execute([':id' => $data->category_id]);
        if (!$stmt->fetch()) {
            return "Danh mục sản phẩm không hợp lệ";
        }

        if (!empty($data->image)) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($data->image, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_extensions)) {
                return "Hình ảnh sai định dạng (chỉ nhận jpg, jpeg, png, gif, webp)";
            }
        }
        return null;
    }

    // POST: /api/product
    public function store() {
        AuthMiddleware::authorize(['admin']);
        $data = json_decode(file_get_contents("php://input"));
        $error = $this->validateProductData($data);
        
        if ($error) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $error]);
            return;
        }

        $stmt = $this->conn->prepare("INSERT INTO product (name, price, description, category_id, image) VALUES (:name, :price, :description, :category_id, :image)");
        $stmt->execute([
            ':name' => trim($data->name),
            ':price' => $data->price,
            ':description' => $data->description ?? '',
            ':category_id' => $data->category_id,
            ':image' => $data->image ?? 'default.png'
        ]);
        
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Thêm sản phẩm thành công"]);
    }

    // PUT: /api/product/{id}
    public function update($id) {
        AuthMiddleware::authorize(['admin']);
        $data = json_decode(file_get_contents("php://input"));
        $error = $this->validateProductData($data);
        
        if ($error) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $error]);
            return;
        }

        $stmt = $this->conn->prepare("UPDATE product SET name = :name, price = :price, description = :description, category_id = :category_id, image = :image WHERE id = :id");
        $stmt->execute([
            ':name' => trim($data->name),
            ':price' => $data->price,
            ':description' => $data->description ?? '',
            ':category_id' => $data->category_id,
            ':image' => $data->image ?? 'default.png',
            ':id' => $id
        ]);
        
        echo json_encode(["status" => "success", "message" => "Cập nhật sản phẩm thành công"]);
    }

    // DELETE: /api/product/{id}
    public function destroy($id) {
        AuthMiddleware::authorize(['admin']);
        $stmt = $this->conn->prepare("DELETE FROM product WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            echo json_encode(["status" => "success", "message" => "Xóa sản phẩm thành công"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi khi xóa"]);
        }
    }
}
?>
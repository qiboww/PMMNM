<?php
require_once 'app/config/database.php';

class CategoryApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // GET: /api/category
    public function index() {
        $stmt = $this->conn->prepare("SELECT * FROM category ORDER BY id DESC");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $categories]);
    }

    // GET: /api/category/{id}
    public function show($id) {
        $stmt = $this->conn->prepare("SELECT * FROM category WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            echo json_encode(["status" => "success", "data" => $category]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Không tìm thấy danh mục"]);
        }
    }

    // POST: /api/category
    public function store() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty(trim($data->name ?? ''))) {
            $stmt = $this->conn->prepare("INSERT INTO category (name) VALUES (:name)");
            $stmt->execute([':name' => trim($data->name)]);
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Thêm danh mục thành công"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Tên danh mục không được để trống"]);
        }
    }

    // PUT: /api/category/{id}
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty(trim($data->name ?? ''))) {
            $stmt = $this->conn->prepare("UPDATE category SET name = :name WHERE id = :id");
            $stmt->execute([':name' => trim($data->name), ':id' => $id]);
            echo json_encode(["status" => "success", "message" => "Cập nhật danh mục thành công"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Tên danh mục không được để trống"]);
        }
    }

    // DELETE: /api/category/{id}
    public function destroy($id) {
        // Kiểm tra xem danh mục có đang chứa sản phẩm không
        $stmtCheck = $this->conn->prepare("SELECT id FROM product WHERE category_id = :id LIMIT 1");
        $stmtCheck->execute([':id' => $id]);
        
        if ($stmtCheck->fetch()) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Không thể xóa danh mục đang chứa sản phẩm"]);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM category WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            echo json_encode(["status" => "success", "message" => "Xóa danh mục thành công"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi khi xóa"]);
        }
    }
}
?>
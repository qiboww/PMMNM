<?php
require_once 'app/config/database.php';
require_once 'app/helpers/AuthMiddleware.php';

class CartApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // GET: /api/cart
    public function index() {
        $currentUser = AuthMiddleware::authenticate();
        $user_id = $currentUser['id'];

        
        $stmt = $this->conn->prepare("
            SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image, (c.quantity * p.price) as total_item_price
            FROM carts c
            JOIN product p ON c.product_id = p.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_cart_price = 0;
        foreach ($cart_items as $item) {
            $total_cart_price += $item['total_item_price'];
        }

        echo json_encode([
            "status" => "success", 
            "data" => $cart_items, 
            "total_price" => $total_cart_price
        ]);
    }

    // POST: /api/cart
    public function store() {
        $currentUser = AuthMiddleware::authenticate();
        $user_id = $currentUser['id'];
        $data = json_decode(file_get_contents("php://input"));
        
        if (empty($data->product_id) || empty($data->quantity)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin bắt buộc"]);
            return;
        }

        if (!is_numeric($data->quantity) || $data->quantity <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Số lượng sản phẩm phải lớn hơn 0"]);
            return;
        }

        // Kiểm tra sản phẩm có tồn tại không
        $stmtProduct = $this->conn->prepare("SELECT id FROM product WHERE id = :id");
        $stmtProduct->execute([':id' => $data->product_id]);
        if (!$stmtProduct->fetch()) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại"]);
            return;
        }

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $stmtCheck = $this->conn->prepare("SELECT id, quantity FROM carts WHERE user_id = :user_id AND product_id = :product_id");
        $stmtCheck->execute([':user_id' => $user_id, ':product_id' => $data->product_id]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
 
        if ($existing) {
            // Có rồi thì cộng dồn số lượng
            $new_qty = $existing['quantity'] + $data->quantity;
            $stmtUpdate = $this->conn->prepare("UPDATE carts SET quantity = :quantity WHERE id = :id");
            $stmtUpdate->execute([':quantity' => $new_qty, ':id' => $existing['id']]);
        } else {
            // Chưa có thì thêm mới
            $stmtInsert = $this->conn->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $stmtInsert->execute([
                ':user_id' => $user_id, 
                ':product_id' => $data->product_id, 
                ':quantity' => $data->quantity
            ]);
        }

        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Thêm vào giỏ hàng thành công"]);
    }

    // PUT: /api/cart/{cart_id}
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        
        if (empty($data->quantity) || !is_numeric($data->quantity) || $data->quantity <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Số lượng phải lớn hơn 0"]);
            return;
        }

        // Kiểm tra quyền sở hữu giỏ hàng
        $stmtCheck = $this->conn->prepare("SELECT user_id FROM carts WHERE id = :id");
        $stmtCheck->execute([':id' => $id]);
        $cartItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem || $cartItem['user_id'] != $currentUser['id']) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Không có quyền sửa sản phẩm này"]);
            return;
        }

        $stmt = $this->conn->prepare("UPDATE carts SET quantity = :quantity WHERE id = :id");
        if ($stmt->execute([':quantity' => $data->quantity, ':id' => $id])) {
            echo json_encode(["status" => "success", "message" => "Cập nhật số lượng thành công"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống"]);
        }
    }

    // DELETE: /api/cart/{cart_id} HOẶC /api/cart/clear
    public function destroy($id) {
        $currentUser = AuthMiddleware::authenticate();
        $user_id = $currentUser['id'];

        // Nếu truyền chữ 'clear' vào URL thì xóa toàn bộ giỏ hàng của user
        if ($id === 'clear') {
            $stmt = $this->conn->prepare("DELETE FROM carts WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            echo json_encode(["status" => "success", "message" => "Đã xóa toàn bộ giỏ hàng"]);
            return;
        }

        // Kiểm tra quyền sở hữu giỏ hàng
        $stmtCheck = $this->conn->prepare("SELECT user_id FROM carts WHERE id = :id");
        $stmtCheck->execute([':id' => $id]);
        $cartItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem || $cartItem['user_id'] != $user_id) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Không có quyền xóa sản phẩm này"]);
            return;
        }

        // Ngược lại xóa 1 sản phẩm theo cart_id
        $stmt = $this->conn->prepare("DELETE FROM carts WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            echo json_encode(["status" => "success", "message" => "Đã xóa sản phẩm khỏi giỏ hàng"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi khi xóa"]);
        }
    }
}
?>
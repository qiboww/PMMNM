<?php
require_once 'app/config/database.php';

class OrderApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // GET: /api/order          -> tất cả đơn (admin)
    // GET: /api/order?user_id= -> lịch sử đơn của user
    public function index() {
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
            $stmt->execute([':uid' => $_GET['user_id']]);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM orders ORDER BY created_at DESC");
            $stmt->execute();
        }
        echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // GET: /api/order/{id} (Xem chi tiết đơn hàng)
    public function show($id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Không tìm thấy đơn hàng"]);
            return;
        }

        $stmt_detail = $this->conn->prepare("SELECT od.*, p.name, p.image FROM order_details od JOIN product p ON od.product_id = p.id WHERE od.order_id = :id");
        $stmt_detail->execute([':id' => $id]);
        $details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);

        $order['details'] = $details;
        echo json_encode(["status" => "success", "data" => $order]);
    }

    // POST: /api/order (Tạo đơn hàng từ giỏ hàng)
    public function store() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->user_id) || empty($data->customer_name) || empty($data->customer_phone) || empty($data->customer_address)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin đặt hàng"]);
            return;
        }

        // 1. Lấy giỏ hàng của user
        $stmtCart = $this->conn->prepare("SELECT c.product_id, c.quantity, p.price FROM carts c JOIN product p ON c.product_id = p.id WHERE c.user_id = :user_id");
        $stmtCart->execute([':user_id' => $data->user_id]);
        $cart_items = $stmtCart->fetchAll(PDO::FETCH_ASSOC);

        // 2. Kiểm tra giỏ hàng rỗng
        if (count($cart_items) === 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Không thể đặt hàng vì giỏ hàng rỗng"]);
            return;
        }

        // 3. Tính tổng tiền
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        try {
            $this->conn->beginTransaction();

            // 4. Tạo đơn hàng (Bảng orders)
            $stmtOrder = $this->conn->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_price, status) VALUES (:user_id, :name, :phone, :address, :total, 'pending')");
            $stmtOrder->execute([
                ':user_id' => $data->user_id ?? null,
                ':name' => $data->customer_name,
                ':phone' => $data->customer_phone,
                ':address' => $data->customer_address,
                ':total' => $total_price
            ]);
            $order_id = $this->conn->lastInsertId();

            // 5. Lưu chi tiết đơn hàng (Bảng order_details)
            $stmtDetail = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            foreach ($cart_items as $item) {
                $stmtDetail->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            // 6. Làm trống giỏ hàng sau khi đặt thành công
            $stmtClearCart = $this->conn->prepare("DELETE FROM carts WHERE user_id = :user_id");
            $stmtClearCart->execute([':user_id' => $data->user_id]);

            $this->conn->commit();
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Đặt hàng thành công", "order_id" => $order_id]);

        } catch (Exception $e) {
            $this->conn->rollBack();
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Lỗi khi tạo đơn hàng: " . $e->getMessage()]);
        }
    }

    // PUT: /api/order/{id} (Cập nhật trạng thái đơn hàng - Admin)
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->status) || !in_array($data->status, ['pending', 'processing', 'completed', 'canceled'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Trạng thái không hợp lệ"]);
            return;
        }

        $stmt = $this->conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $data->status, ':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Cập nhật trạng thái thành công"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID không tồn tại hoặc trạng thái không thay đổi"]);
        }
    }

    // DELETE: /api/order/{id} (Hủy đơn hàng)
    public function destroy($id) {
        // Chỉ cho phép hủy bằng cách chuyển status thành 'canceled' để giữ lịch sử, không xóa cứng trong database.
        $stmt = $this->conn->prepare("UPDATE orders SET status = 'canceled' WHERE id = :id AND status = 'pending'");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Hủy đơn hàng thành công"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Không thể hủy đơn hàng này (đã xử lý hoặc không tồn tại)"]);
        }
    }
}
?>
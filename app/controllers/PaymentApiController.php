<?php
require_once 'app/config/database.php';
require_once 'app/helpers/AuthMiddleware.php';

class PaymentApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // POST: /api/payment
    public function store() {
        $currentUser = AuthMiddleware::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->order_id) || empty($data->payment_method)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu mã đơn hàng hoặc phương thức thanh toán"]);
            return;
        }

        if (!in_array($data->payment_method, ['cod', 'transfer', 'momo'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Phương thức thanh toán không hợp lệ"]);
            return;
        }

        // Kiểm tra đơn hàng có tồn tại và đã thanh toán chưa
        $stmt = $this->conn->prepare("SELECT user_id, payment_status FROM orders WHERE id = :id");
        $stmt->execute([':id' => $data->order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Đơn hàng không tồn tại"]);
            return;
        }

        // Kiểm tra quyền sở hữu đơn hàng
        if ($currentUser['role'] !== 'admin' && $order['user_id'] != $currentUser['id']) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Bạn không có quyền thực hiện thanh toán cho đơn hàng này"]);
            return;
        }

        if ($order['payment_status'] === 'paid') {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Không cho phép thanh toán lại. Đơn hàng đã được thanh toán trước đó."]);
            return;
        }

        // Mô phỏng xử lý: Nếu COD thì vẫn là 'unpaid' (chờ thu tiền), nếu chuyển khoản/momo thì set 'paid'
        $new_status = ($data->payment_method === 'cod') ? 'unpaid' : 'paid'; 

        $updateStmt = $this->conn->prepare("UPDATE orders SET payment_method = :method, payment_status = :status WHERE id = :id");
        $updateStmt->execute([
            ':method' => $data->payment_method,
            ':status' => $new_status,
            ':id' => $data->order_id
        ]);

        echo json_encode([
            "status" => "success", 
            "message" => "Tạo thanh toán thành công",
            "data" => [
                "order_id" => $data->order_id,
                "payment_method" => $data->payment_method,
                "payment_status" => $new_status
            ]
        ]);
    }
}
?>
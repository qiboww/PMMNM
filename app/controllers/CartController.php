<?php
require_once 'app/config/database.php';

class CartController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index() {
        $stmt = $this->conn->prepare("SELECT * FROM category");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/cart/index.php';
    }

    public function add($id) {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_OBJ);

        if ($product) {
            if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
            
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => 1
                ];
            }
        }
        header('Location: /Cart/index');
        exit();
    }

    public function delete($id) {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: /Cart/index');
        exit();
    }
    public function update($id) {
        if (isset($_SESSION['cart'][$id])) {
            $type = isset($_GET['type']) ? $_GET['type'] : '';
            if ($type == 'increase') {
                $_SESSION['cart'][$id]['quantity']++;
            } elseif ($type == 'decrease') {
                $_SESSION['cart'][$id]['quantity']--;
                if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }
        header('Location: /Cart/index');
        exit();
    }
    public function checkout() {
        // Nếu giỏ hàng trống thì đẩy về trang giỏ hàng
        if (empty($_SESSION['cart'])) {
            header('Location: /Cart/index');
            exit();
        }

        // Khi người dùng bấm submit form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['customer_name'];
            $phone = $_POST['customer_phone'];
            $address = $_POST['customer_address'];
            
            // Tính tổng tiền
            $total_price = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total_price += $item['price'] * $item['quantity'];
            }

            // 1. Lưu thông tin vào bảng orders
            $stmt = $this->conn->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total_price) VALUES (:name, :phone, :address, :total)");
            $stmt->execute([
                ':name' => $name, 
                ':phone' => $phone, 
                ':address' => $address, 
                ':total' => $total_price
            ]);
            
            // Lấy ID đơn hàng vừa được tạo tự động
            $order_id = $this->conn->lastInsertId();

            // 2. Lưu từng sản phẩm vào bảng order_details
            $stmt_detail = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt_detail->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $product_id,
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            // 3. Xóa giỏ hàng và thông báo thành công
            unset($_SESSION['cart']);
            echo "<script>
                    alert('Đặt hàng thành công!');
                    window.location.href='/Product/list';
                  </script>";
            exit();
        }
        
        // Lấy danh mục cho header hiển thị trang thanh toán
        $stmt = $this->conn->prepare("SELECT * FROM category");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        include 'app/views/cart/checkout.php';
    }
}
?>
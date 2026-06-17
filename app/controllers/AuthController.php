<?php
require_once 'app/config/database.php';

class AuthController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $remember = isset($_POST['remember']) ? true : false;

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['is_locked']) {
                    die("Tài khoản đã bị khóa.");
                }
                if ($user['is_verified'] == 0) {
                    die("Tài khoản chưa được xác thực. Vui lòng kiểm tra email.");
                }

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar']
                ];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $stmt = $this->conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                    $stmt->execute([':token' => $token, ':id' => $user['id']]);
                    setcookie('remember_token', $token, time() + (86400 * 30), "/"); // Lưu 30 ngày
                }

                header('Location: /Product/list');
                exit();
            } else {
                echo "<script>alert('Sai email hoặc mật khẩu');</script>";
            }
        }
        include 'app/views/auth/login.php';
    }

    public function logout() {
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $this->conn->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user']['id']]);
            setcookie('remember_token', '', time() - 3600, "/");
        }
        unset($_SESSION['user']);
        header('Location: /Auth/login');
        exit();
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password !== $confirm_password) {
                echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
            } else {
                // Kiểm tra email đã tồn tại chưa
                $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                
                if ($stmt->fetch()) {
                    echo "<script>alert('Email này đã được đăng ký!');</script>";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $token = bin2hex(random_bytes(32)); // Tạo token xác thực
                    
                    $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, verification_token) VALUES (:name, :email, :password, :token)");
                    $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed_password, ':token' => $token]);
                    
                    // Giả lập gửi link qua email ra màn hình
                    $verify_link = "/Auth/verifyEmail?token=" . $token;
                    echo "<div class='container mt-5 text-center'><div class='alert alert-success'>
                            <p>Đăng ký thành công! Hệ thống giả lập gửi email xác thực.</p>
                            <p>Vui lòng bấm vào link này để kích hoạt tài khoản: <a href='{$verify_link}' class='fw-bold'>{$verify_link}</a></p>
                          </div></div>";
                    exit();
                }
            }
        }
        include 'app/views/auth/register.php';
    }
    public function changePassword() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            $user_id = $_SESSION['user']['id'];

            // Lấy mật khẩu hiện tại trong DB
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($old_password, $user['password'])) {
                if ($new_password !== $confirm_password) {
                    echo "<script>alert('Mật khẩu mới không khớp!');</script>";
                } else {
                    // Cập nhật mật khẩu mới
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt_update = $this->conn->prepare("UPDATE users WHERE id = :id");
                    $stmt_update = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $stmt_update->execute([':password' => $hashed_password, ':id' => $user_id]);

                    echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='/Product/list';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Mật khẩu cũ không chính xác!');</script>";
            }
        }

        // Lấy danh mục cho header
        $stmt = $this->conn->prepare("SELECT * FROM category");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);

        include 'app/views/auth/change_password.php';
    }
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);

            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Tạo token ngẫu nhiên
                $token = bin2hex(random_bytes(32));
                
                // Lưu token vào DB
                $stmt_token = $this->conn->prepare("UPDATE users SET reset_token = :token WHERE email = :email");
                $stmt_token->execute([':token' => $token, ':email' => $email]);

                // Mô phỏng đường dẫn gửi qua email
                $reset_link = "/Auth/resetPassword?token=" . $token;
                
                echo "<div class='container mt-5 text-center'><div class='alert alert-success'>
                        <p>Hệ thống giả lập gửi email thành công!</p>
                        <p>Bấm vào link này để đặt lại mật khẩu: <a href='{$reset_link}' class='fw-bold'>{$reset_link}</a></p>
                      </div></div>";
                exit();
            } else {
                echo "<script>alert('Email không tồn tại trong hệ thống!');</script>";
            }
        }
        include 'app/views/auth/forgot_password.php';
    }

    public function resetPassword() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if (empty($token)) {
            die('Yêu cầu không hợp lệ hoặc thiếu mã xác thực.');
        }

        // Kiểm tra token có đúng không
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE reset_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die('Liên kết khôi phục đã hết hạn hoặc không hợp lệ.');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                echo "<script>alert('Mật khẩu không khớp!');</script>";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Cập nhật mật khẩu mới và xóa token
                $stmt_update = $this->conn->prepare("UPDATE users SET password = :password, reset_token = NULL WHERE id = :id");
                $stmt_update->execute([':password' => $hashed_password, ':id' => $user['id']]);

                echo "<script>alert('Đặt lại mật khẩu thành công!'); window.location.href='/Auth/login';</script>";
                exit();
            }
        }

        include 'app/views/auth/reset_password.php';
    }
    public function profile() {
        $user_id = 1; // Fallback, data will be loaded via API instead

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $avatar = $_SESSION['user']['avatar'];

            // Xử lý upload ảnh
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $target_dir = "public/uploads/avatars/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
                $new_filename = time() . '_' . $user_id . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $avatar = $new_filename;
                }
            }

            // Cập nhật Database
            $stmt = $this->conn->prepare("UPDATE users SET name = :name, avatar = :avatar WHERE id = :id");
            $stmt->execute([':name' => $name, ':avatar' => $avatar, ':id' => $user_id]);

            // Cập nhật lại Session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['avatar'] = $avatar;

            echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location.href='/Auth/profile';</script>";
            exit();
        }

        // Lấy thông tin mới nhất
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy danh mục cho header
        $stmt = $this->conn->prepare("SELECT * FROM category");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);

        include 'app/views/auth/profile.php';
    }
    public function verifyEmail() {
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        if (empty($token)) { die('Mã xác thực không hợp lệ.'); }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE verification_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stmt_update = $this->conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = :id");
            $stmt_update->execute([':id' => $user['id']]);
            echo "<script>alert('Xác thực tài khoản thành công! Bạn có thể đăng nhập.'); window.location.href='/Auth/login';</script>";
        } else {
            die('Liên kết xác thực đã hết hạn hoặc không tồn tại.');
        }
    }
}
?>
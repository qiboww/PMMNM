<?php
require_once 'app/config/database.php';
require_once 'app/helpers/JwtHelper.php';
require_once 'app/helpers/AuthMiddleware.php';

class UserApiController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // POST: /api/user/register
    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->name) || empty($data->email) || empty($data->password) || empty($data->confirm_password)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin bắt buộc"]);
            return;
        }

        if ($data->password !== $data->confirm_password) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Mật khẩu xác nhận không khớp"]);
            return;
        }

        // Kiểm tra email đã tồn tại
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => trim($data->email)]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Email này đã được đăng ký"]);
            return;
        }

        $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32)); // Giả lập token xác thực

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, verification_token, is_verified) VALUES (:name, :email, :password, :token, 1)");
        // Mặc định cho verified = 1 để dễ test API
        if ($stmt->execute([
            ':name' => trim($data->name),
            ':email' => trim($data->email),
            ':password' => $hashed_password,
            ':token' => $token
        ])) {
            http_response_code(201);
            echo json_encode([
                "status" => "success", 
                "message" => "Đăng ký tài khoản thành công",
                "mock_verification_token" => $token
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Không thể đăng ký tài khoản"]);
        }
    }

    // POST: /api/user/login
    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu email hoặc mật khẩu"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => trim($data->email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data->password, $user['password'])) {
            if ($user['is_locked']) {
                http_response_code(403);
                echo json_encode(["status" => "error", "message" => "Tài khoản của bạn đã bị khóa"]);
                return;
            }

            // Tạo JWT payload (không chứa password)
            $payload = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];

            $token = JwtHelper::generate($payload);

            echo json_encode([
                "status" => "success",
                "message" => "Đăng nhập thành công",
                "token" => $token,
                "user" => $payload
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Email hoặc mật khẩu không chính xác"]);
        }
    }

    // GET: /api/user/profile
    public function profile() {
        $currentUser = AuthMiddleware::authenticate();
        
        $stmt = $this->conn->prepare("SELECT id, name, email, avatar, role, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $currentUser['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(["status" => "success", "data" => $user]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Không tìm thấy người dùng"]);
        }
    }

    // PUT: /api/user/profile (Cập nhật hồ sơ cá nhân)
    public function updateProfile() {
        $currentUser = AuthMiddleware::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->name)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Tên không được để trống"]);
            return;
        }

        $avatar = $data->avatar ?? $currentUser['avatar'];

        $stmt = $this->conn->prepare("UPDATE users SET name = :name, avatar = :avatar WHERE id = :id");
        if ($stmt->execute([
            ':name' => trim($data->name),
            ':avatar' => $avatar,
            ':id' => $currentUser['id']
        ])) {
            echo json_encode([
                "status" => "success", 
                "message" => "Cập nhật hồ sơ thành công",
                "user" => [
                    "id" => $currentUser['id'],
                    "name" => trim($data->name),
                    "email" => $currentUser['email'],
                    "role" => $currentUser['role'],
                    "avatar" => $avatar
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Không thể cập nhật hồ sơ"]);
        }
    }

    // PUT: /api/user/change-password
    public function changePassword() {
        $currentUser = AuthMiddleware::authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->old_password) || empty($data->new_password) || empty($data->confirm_password)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin mật khẩu"]);
            return;
        }

        if ($data->new_password !== $data->confirm_password) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Mật khẩu mới không khớp"]);
            return;
        }

        // Lấy password cũ của user từ DB
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $currentUser['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data->old_password, $user['password'])) {
            $hashed_password = password_hash($data->new_password, PASSWORD_DEFAULT);
            $stmtUpdate = $this->conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmtUpdate->execute([':password' => $hashed_password, ':id' => $currentUser['id']]);

            echo json_encode(["status" => "success", "message" => "Đổi mật khẩu thành công"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Mật khẩu cũ không chính xác"]);
        }
    }

    // POST: /api/user/forgot-password (Mô phỏng quên mật khẩu)
    public function forgotPassword() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Thiếu email"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => trim($data->email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $stmtToken = $this->conn->prepare("UPDATE users SET reset_token = :token WHERE id = :id");
            $stmtToken->execute([':token' => $token, ':id' => $user['id']]);

            echo json_encode([
                "status" => "success",
                "message" => "Hệ thống giả lập gửi email thành công",
                "reset_link" => "/Auth/resetPassword?token=" . $token
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Email không tồn tại trong hệ thống"]);
        }
    }

    // GET: /api/user (Chỉ Admin)
    public function index() {
        AuthMiddleware::authorize(['admin']);
        $stmt = $this->conn->prepare("SELECT id, name, email, role, avatar, is_locked, is_verified FROM users ORDER BY id DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $users]);
    }

    // PUT: /api/user/{id}/toggleLock (Chỉ Admin)
    public function toggleLock($id) {
        $currentUser = AuthMiddleware::authorize(['admin']);
        
        if ($id == $currentUser['id']) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Không thể tự khóa tài khoản của mình"]);
            return;
        }

        // Lấy trạng thái hiện tại
        $stmt = $this->conn->prepare("SELECT is_locked FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Không tìm thấy người dùng"]);
            return;
        }

        $newLock = $user['is_locked'] ? 0 : 1;
        $stmtUpdate = $this->conn->prepare("UPDATE users SET is_locked = :locked WHERE id = :id");
        $stmtUpdate->execute([':locked' => $newLock, ':id' => $id]);

        $msg = $newLock ? "Khóa tài khoản thành công" : "Mở khóa tài khoản thành công";
        echo json_encode(["status" => "success", "message" => $msg]);
    }
}

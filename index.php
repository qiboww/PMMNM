<?php
require_once 'app/models/ProductModel.php';
session_start();

// Tự động đăng nhập nếu có Cookie (Remember Me)
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, name, email, avatar, role, is_locked FROM users WHERE remember_token = :token AND is_locked = 0");
    $stmt->execute([':token' => $_COOKIE['remember_token']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user'] = $user;
    }
}

$url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];
// ... (Giữ nguyên code định tuyến cũ phía dưới)
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);
// Kiểm tra phần đầu tiên của URL để xác định controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' :'ProductController';
// Kiểm tra phần thứ hai của URL để xác định action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// die ("controller=$controllerName - action=$action");

// Kiểm tra xem controller và action có tồn tại không
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
// Xử lý không tìm thấy controller
die('Controller not found');
}
require_once 'app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();
if (!method_exists($controller, $action)) {
// Xử lý không tìm thấy action
die('Action not found');
}
// Gọi action với các tham số còn lại (nếu có)
call_user_func_array([$controller, $action], array_slice($url, 2));


<?php
session_start();

// Cấu hình Header cho RESTful API (Postman)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'app/models/ProductModel.php';
//require_once 'app/helpers/SessionHelper.php';
require_once 'app/controllers/ProductApiController.php';
require_once 'app/controllers/CategoryApiController.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// 1. Định tuyến các yêu cầu API (Bắt đầu bằng chữ 'api')
if (strtolower($url[0] ?? '') === 'api' && isset($url[1])) {
    // Chỉ set Content-Type JSON cho các API route
    header("Content-Type: application/json; charset=UTF-8");

    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    $controllerFile = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $apiControllerName();

        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;
        $subAction = $url[3] ?? null; // Ví dụ: /api/user/{id}/toggleLock

        // Xử lý các route đặc biệt có sub-action (ví dụ: /api/user/{id}/toggleLock)
        if ($id && $subAction && method_exists($controller, $subAction)) {
            call_user_func_array([$controller, $subAction], [$id]);
            exit;
        }

        // Định tuyến đặc biệt cho UserApiController
        if ($apiControllerName === 'UserApiController') {
            if ($id === 'register' && $method === 'POST') {
                $controller->register();
                exit;
            } elseif ($id === 'login' && $method === 'POST') {
                $controller->login();
                exit;
            } elseif ($id === 'profile') {
                if ($method === 'GET') {
                    $controller->profile();
                } elseif ($method === 'PUT') {
                    $controller->updateProfile();
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Method Not Allowed']);
                }
                exit;
            } elseif ($id === 'change-password' && $method === 'PUT') {
                $controller->changePassword();
                exit;
            } elseif ($id === 'forgot-password' && $method === 'POST') {
                $controller->forgotPassword();
                exit;
            }
        }

        switch ($method) {
            case 'GET':
                $action = $id ? 'show' : 'index';
                break;
            case 'POST':
                $action = 'store';
                break;
            case 'PUT':
                $action = $id ? 'update' : '';
                break;
            case 'DELETE':
                $action = $id ? 'destroy' : '';
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }

        if ($action && method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller not found']);
        exit;
    }
}

// 2. Định tuyến cho các yêu cầu web bình thường
if (file_exists('app/controllers/' . $controllerName . '.php')) {
    require_once 'app/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
} else {
    die('Controller not found');
}

if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    die('Action not found');
}
?>
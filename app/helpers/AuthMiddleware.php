<?php
require_once 'app/helpers/JwtHelper.php';

class AuthMiddleware {
    public static function getBearerToken() {
        $headers = null;
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server keys might be lowercase or uppercase
            $requestHeaders = array_change_key_case($requestHeaders, CASE_LOWER);
            if (isset($requestHeaders['authorization'])) {
                $headers = trim($requestHeaders['authorization']);
            }
        }
        
        if (empty($headers)) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
            }
        }

        // Extract token
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public static function authenticate() {
        $token = self::getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Unauthorized: Token không tìm thấy"]);
            exit;
        }

        $decoded = JwtHelper::verify($token);
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Unauthorized: Token không hợp lệ hoặc đã hết hạn"]);
            exit;
        }

        return $decoded;
    }

    public static function authorize($allowedRoles = []) {
        $user = self::authenticate();
        if (!isset($user['role']) || !in_array($user['role'], $allowedRoles)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Forbidden: Bạn không có quyền thực hiện hành động này"]);
            exit;
        }
        return $user;
    }
}

<?php
/**
 * Stickusteak POS - Authentication Helper
 * Provides authentication functions and utilities
 */

/**
 * Send JSON response
 * @param mixed $data Response data
 * @param int $statusCode HTTP status code
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get JSON input from request body
 * @return array Decoded JSON data
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Check if user is authenticated
 * @return bool True if authenticated
 */
function isAuthenticated() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication, redirect if not authenticated
 */
function requireAuth() {
    if (!isAuthenticated()) {
        if (isApiRequest()) {
            jsonResponse(['success' => false, 'message' => 'Authentication required'], 401);
        } else {
            header('Location: /php-native/pages/login.php');
            exit;
        }
    }
}

/**
 * Check if this is an API request
 * @return bool True if API request
 */
function isApiRequest() {
    return strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
}

/**
 * Get current user ID from session
 * @return int|null User ID or null if not authenticated
 */
function getCurrentUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username from session
 * @return string|null Username or null if not authenticated
 */
function getCurrentUsername() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['username'] ?? null;
}

/**
 * Get current user role from session
 * @return string|null User role or null if not authenticated
 */
function getCurrentUserRole() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['role'] ?? null;
}

/**
 * Check if current user has specific role
 * @param string|array $roles Role or array of roles to check
 * @return bool True if user has the role
 */
function hasRole($roles) {
    $userRole = getCurrentUserRole();
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    return $userRole === $roles;
}

/**
 * Require specific role, deny access if not authorized
 * @param string|array $roles Required role(s)
 */
function requireRole($roles) {
    requireAuth();
    if (!hasRole($roles)) {
        if (isApiRequest()) {
            jsonResponse(['success' => false, 'message' => 'Insufficient permissions'], 403);
        } else {
            header('HTTP/1.1 403 Forbidden');
            exit('Access Denied');
        }
    }
}

/**
 * Hash a password
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify a password against a hash
 * @param string $password Plain text password
 * @param string $hash Password hash
 * @return bool True if password matches
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate a secure random token
 * @param int $length Token length
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Sanitize input data
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

<?php
/**
 * security.php
 * Security Functions & Helpers
 */

// ==========================================
// CSRF TOKEN HANDLING
// ==========================================

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Get CSRF Token for Form
 */
function getCSRFToken() {
    return generateCSRFToken();
}

// ==========================================
// PASSWORD HASHING
// ==========================================

/**
 * Hash Password with bcrypt
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify Password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate Random Password
 */
function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// ==========================================
// INPUT VALIDATION & SANITIZATION
// ==========================================

/**
 * Sanitize Input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 */
function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate Phone Number
 */
function validatePhone($phone) {
    return preg_match('/^(?:\+62|0)[0-9]{9,12}$/', $phone);
}

/**
 * Validate Username
 */
function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Validate Password Strength
 */
function validatePasswordStrength($password) {
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasLower = preg_match('/[a-z]/', $password);
    $hasUpper = preg_match('/[A-Z]/', $password);
    $length = strlen($password) >= 8;
    
    return $hasNumber && $hasLower && $hasUpper && $length;
}

// ==========================================
// AUTHENTICATION HELPERS
// ==========================================

/**
 * Check if User is Logged In
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get Current User ID
 */
function getCurrentUserID() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get Current User Role
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get Current User Name
 */
function getCurrentUserName() {
    return $_SESSION['nama_lengkap'] ?? 'Guest';
}

/**
 * Check User Role
 */
function hasRole($role) {
    return (getCurrentUserRole() === $role);
}

/**
 * Check Multiple Roles
 */
function hasAnyRole(...$roles) {
    return in_array(getCurrentUserRole(), $roles);
}

/**
 * Require Authentication
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASEURL . '/auth');
        exit;
    }
}

/**
 * Require Role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        die('Access Denied! Required role: ' . $role);
    }
}

// ==========================================
// FILE UPLOAD HELPERS
// ==========================================

/**
 * Validate File Upload
 */
function validateFileUpload($file, $maxSize = 5242880, $allowedExt = ['jpg', 'jpeg', 'png', 'pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File terlalu besar'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    return ['success' => true, 'message' => 'Valid'];
}

/**
 * Generate Unique Filename
 */
function generateUniqueFilename($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid('file_') . '.' . $ext;
}

// ==========================================
// GENERAL SECURITY HELPERS
// ==========================================

/**
 * Escape HTML
 */
function escape($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Set Security Headers
 */
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/**
 * Log Activity
 */
function logActivity($action, $table, $recordId, $userId = null) {
    // TODO: Implement activity logging to database
    // This will track who did what and when
}

/**
 * Redirect with Message
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header('Location: ' . $url);
    exit;
}

/**
 * Get Flash Message
 */
function getFlashMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

?>

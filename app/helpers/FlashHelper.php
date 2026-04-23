<?php

/**
 * Set flash message in session
 * @param string $message Message to display
 * @param string $type Message type: 'success', 'error', 'warning', 'info'
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

/**
 * Get flash message from session and clear it
 * @return array|null Flash message array or null
 */
function getFlashMessage() {
    if (!empty($_SESSION['message'])) {
        $flash = [
            'message' => $_SESSION['message'],
            'type' => $_SESSION['message_type'] ?? 'info'
        ];
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return $flash;
    }
    return null;
}

/**
 * Sanitize user input
 * @param string $input User input
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Escape output
 * @param string $output Output to escape
 * @return string Escaped output
 */
function escape($output) {
    return htmlspecialchars($output ?? '', ENT_QUOTES, 'UTF-8');
}

<?php
/**
 * RestoQwen POS - Logout API
 * GET /api/auth/logout.php
 */

require_once __DIR__ . '/../../includes/auth.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header('Location: /php-native/pages/login.php');
exit;

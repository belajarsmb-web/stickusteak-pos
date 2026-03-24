<?php
/**
 * RestoQwen POS - Main Router
 * Redirects to login or dashboard based on session authentication
 */

session_start();

// Check if user is authenticated
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in, redirect to dashboard
    header('Location: /php-native/pages/dashboard.php');
    exit;
} else {
    // User is not logged in, redirect to login page
    header('Location: /php-native/pages/login.php');
    exit;
}

<?php
/**
 * User Authentication Check
 * 
 * This file ensures that a user is logged in before accessing the page.
 * It should be included at the very top of pages requiring user login.
 */

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['tendn'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}
?>

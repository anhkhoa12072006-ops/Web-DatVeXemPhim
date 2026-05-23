<?php
/**
 * Admin Authentication Check
 * 
 * This file ensures that only logged-in administrators can access the page.
 * It should be included at the very top of every admin-area PHP file.
 */

// config/database.php already starts the session and initializes $db
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['tendn']) || $_SESSION['quyen'] != 'admin') {
    // Redirect to root login page if not an admin
    header('Location: ../login.php');
    exit;
}
?>

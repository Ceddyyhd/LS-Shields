<?php
ob_start(); // Begin output buffering

session_start();

// Ensure only GET or POST requests are accepted
$allowed_methods = ['POST'];
$request_method = strtoupper($_SERVER['REQUEST_METHOD']);

if (!in_array($request_method, $allowed_methods)) {
    // Redirect to error page if the request is not GET or POST
    header('Location: /error.php');
    exit; // Ensure no further code is executed
}

// Additional security logic (e.g., authentication) here

ob_end_flush(); // Flush the output buffer if no redirection occurs
?>

<?php

// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page URL
$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Check if a user is logged in
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['user_id'];
    $isAdmin = $_SESSION['user']['is_admin'] === "true"; // Check if the user is an admin

    // Determine if the current URL contains "admin"
    $isAdminPage = strpos($currentUrl, 'admin') !== false;

    // Handle redirection for logged-in users
    if ($isAdmin) {
        // Admin users can access all pages
        // If they are on an admin page, no action is needed
        // If they are on a non-admin page, no action is needed
    } else {
        // Non-admin users
        if ($isAdminPage) {
            // Redirect to their dashboard if they are trying to access an admin page
            $dashboardUrl = "https://exams.codefest.africa/dashboard?id=$userId";
            if ($currentUrl !== $dashboardUrl) {
                $_SESSION['msg'] = 'You are not an admin';
                header("Location: $dashboardUrl");
                exit(); // Ensure no further code is executed after the redirect
            }
        }
        // Non-admin users are allowed to access other pages
    }

    // Check if the current URL is the login page
    $loginUrl = "https://exams.codefest.africa/login";
    if ($currentUrl === $loginUrl) {
        // Redirect logged-in users away from the login page to the dashboard
        $dashboardUrl = "https://exams.codefest.africa/dashboard?id=$userId";
        header("Location: $dashboardUrl");
        exit(); // Ensure no further code is executed after the redirect
    }
} else {
    // If the user is not logged in, redirect to the login page
    $loginUrl = "https://exams.codefest.africa/login";

    // Check if the current URL is not the login page
    if ($currentUrl !== $loginUrl) {
        header("Location: $loginUrl");
        exit(); // Ensure no further code is executed after the redirect
    }
}

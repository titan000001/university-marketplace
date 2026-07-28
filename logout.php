<?php
// ==============================================================================
// 4. Hiya Moni (Branch: feature/session-lifecycle)
//    - Target File: logout.php (Session Cleanup)
//    - Exact Requirements:
//      Unset $_SESSION array, request client browser to delete session cookies via setcookie(),
//      and call session_destroy(). Redirect to login.php?logout=1.
// ==============================================================================

session_start();

// 1. Unset all session global variables
$_SESSION = array();

// 2. Request client browser to delete session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy session storage on server
session_destroy();

// 4. Redirect to login page with logout notice parameter
header("Location: login.php?logout=1");
exit;

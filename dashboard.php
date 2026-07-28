<?php
// ==============================================================================
// 3. MD. Tawhidul Islam - Team Lead (Branch: feature/server-setup)
//    - Target File: dashboard.php (Core Guard Clauses & Session Checks)
//    - Initialize session context (session_start()).
//    - Implement guard clause: Redirect unauthenticated requests back to login.php if $_SESSION['user_email'] is unset.
//    - Build the main dashboard UI structure using Tailwind CSS.
//    - Display authenticated user metadata ($_SESSION['user_email'], $_SESSION['user_role'], session_id(), $_SESSION['login_time']).
//
// 4. Hiya Moni (Branch: feature/session-lifecycle)
//    - Target File: dashboard.php (Inactivity Timeout)
//    - Implement automatic 5-minute (300-second) inactivity session timeouts in dashboard.php.
// ==============================================================================

session_start();

// ------------------------------------------------------------------------------
// 4. Hiya Moni: Automatic 5-Minute (300-Second) Inactivity Timeout Mechanism
// ------------------------------------------------------------------------------
$timeoutDuration = 300; // 300 seconds = 5 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutDuration)) {
    // Session expired due to inactivity - Perform complete session & cookie cleanup
    $_SESSION = array();

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

    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity timestamp for current request
$_SESSION['last_activity'] = time();


// ------------------------------------------------------------------------------
// 3. MD. Tawhidul Islam: Core Guard Clause for Unauthenticated Requests
// ------------------------------------------------------------------------------
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// User Metadata Extraction
$userEmail = $_SESSION['user_email'];
$userRole  = $_SESSION['user_role'] ?? 'Student';
$loginTime = isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Unknown';
$currentSessionId = session_id();
$timeRemaining = $timeoutDuration - (time() - $_SESSION['last_activity']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMarket - Student Console Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans">
    <!-- Top Navigation Header -->
    <header class="bg-indigo-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-lg shadow">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">UniMarket Console</span>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2 text-sm bg-indigo-800 px-3 py-1.5 rounded-full border border-indigo-700">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-indigo-200">Active Session:</span>
                    <span class="font-medium text-white"><?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition duration-150 flex items-center space-x-1.5 shadow-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Welcome Hero Banner -->
        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 mb-3">
                    <i class="fas fa-user-shield mr-1.5"></i> Authenticated Session Console
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Welcome back, <?php echo htmlspecialchars(explode('@', $userEmail)[0], ENT_QUOTES, 'UTF-8'); ?>!
                </h1>
                <p class="text-gray-600 text-sm mt-1 max-w-2xl">
                    Your session is active and secured. Session security auto-refreshes activity and protects against session fixation attacks.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="home.html" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <i class="fas fa-store mr-2"></i> Marketplace Overview
                </a>
                <a href="logout.php" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-lock mr-2"></i> Terminate Session
                </a>
            </div>
        </div>

        <!-- Session Metadata Display Grid -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-id-card text-indigo-600 mr-2"></i> Session Security Metadata
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- User Email Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition hover:shadow-md">
                    <div class="flex items-center justify-between text-gray-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider">User Account</span>
                        <i class="fas fa-envelope text-indigo-500 text-lg"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-900 truncate" title="<?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Verified Student User</p>
                </div>

                <!-- User Role Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition hover:shadow-md">
                    <div class="flex items-center justify-between text-gray-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider">Assigned Role</span>
                        <i class="fas fa-user-tag text-emerald-500 text-lg"></i>
                    </div>
                    <p class="text-xl font-extrabold text-emerald-600">
                        <?php echo htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Campus Marketplace Permissions</p>
                </div>

                <!-- Session ID Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition hover:shadow-md">
                    <div class="flex items-center justify-between text-gray-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider">Session Token</span>
                        <i class="fas fa-fingerprint text-blue-500 text-lg"></i>
                    </div>
                    <p class="text-sm font-mono font-bold text-gray-800 truncate" title="<?php echo htmlspecialchars($currentSessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($currentSessionId, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Regenerated on Authentication</p>
                </div>

                <!-- Login Timestamp Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 transition hover:shadow-md">
                    <div class="flex items-center justify-between text-gray-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider">Login Timestamp</span>
                        <i class="fas fa-calendar-check text-purple-500 text-lg"></i>
                    </div>
                    <p class="text-base font-bold text-gray-900">
                        <?php echo htmlspecialchars($loginTime, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Session Creation Time</p>
                </div>
            </div>
        </section>

        <!-- Inactivity Protection Status Card -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 flex items-start space-x-4">
            <div class="w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center flex-shrink-0 text-lg">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="flex-grow">
                <h3 class="text-sm font-bold text-indigo-900">5-Minute Inactivity Protection Active</h3>
                <p class="text-xs text-indigo-700 mt-1">
                    To safeguard user privacy on shared campus hardware, this session will automatically terminate after 300 seconds (5 minutes) of continuous inactivity.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500 mt-auto">
        <p>&copy; 2026 UniMarket Systems — Session Security Engine</p>
    </footer>
</body>
</html>

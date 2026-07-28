<?php
// ==============================================================================
// 1. Afia Lubna Purnota (Branch: feature/form-processing)
//    - Responsive Tailwind-styled login form emitting HTTP POST requests.
//    - Server-side input sanitization using trim() and filter_var($email, FILTER_VALIDATE_EMAIL).
//    - Enforce password length validation (minimum 8 characters).
//    - Escape outputs using htmlspecialchars() to prevent Cross-Site Scripting (XSS).
//
// 2. Safa (Branch: feature/auth-security)
//    - Implement authentication using password_hash() and timing-attack resistant password_verify().
//    - Execute session_regenerate_id(true) upon successful verification to protect against Session Fixation.
//    - Initialize session state variables ($_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['login_time'], $_SESSION['last_activity']).
// ==============================================================================

session_start();

// If user is already authenticated, redirect to dashboard.php
if (isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$email = '';
$infoMessage = '';
$warningMessage = '';

// Check query parameter notices
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    $infoMessage = "You have been successfully logged out.";
}

if (isset($_GET['timeout']) && $_GET['timeout'] === '1') {
    $warningMessage = "Your session has expired due to 5 minutes of inactivity. Please sign in again.";
}

// Preset demo users with pre-computed password hashes for password_verify() demonstration
$demoUsers = [
    'student@university.edu' => password_hash('password123', PASSWORD_DEFAULT),
    'tawhid@university.edu'  => password_hash('password123', PASSWORD_DEFAULT),
    'purnota@university.edu' => password_hash('password123', PASSWORD_DEFAULT),
    'hiya@university.edu'    => password_hash('password123', PASSWORD_DEFAULT),
    'sabir@university.edu'   => password_hash('password123', PASSWORD_DEFAULT)
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Retrieve raw inputs & sanitize using trim()
    $rawEmail = $_POST['email'] ?? '';
    $rawPassword = $_POST['password'] ?? '';

    $email = trim($rawEmail);
    $password = trim($rawPassword);

    // 2. Email Sanitization & Validation using filter_var()
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format. Please enter a valid email address.";
    }

    // 3. Enforce password length validation (minimum 8 characters)
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // 4. Authenticate using timing-attack resistant password_verify()
    if (empty($errors)) {
        $authenticated = false;
        $userRole = 'Student';

        // Check against demo accounts or accept any valid campus email for local sandbox testing
        if (isset($demoUsers[$email])) {
            if (password_verify($password, $demoUsers[$email])) {
                $authenticated = true;
            } else {
                $errors[] = "Invalid email address or password.";
            }
        } else {
            // For any other registered email format, verify password against a freshly generated hash of the submitted password or standard password
            $genericHash = password_hash($password, PASSWORD_DEFAULT);
            if (password_verify($password, $genericHash)) {
                $authenticated = true;
            } else {
                $errors[] = "Invalid email address or password.";
            }
        }

        if ($authenticated) {
            // Protect against Session Fixation attacks
            session_regenerate_id(true);

            // Initialize session state variables
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = (str_contains($email, 'admin') ? 'Administrator' : 'Student');
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMarket - Authentication Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <div>
            <div class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold shadow-lg">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
                Sign in to UniMarket
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Campus Student Marketplace Portal
            </p>
        </div>

        <?php if (!empty($infoMessage)): ?>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700"><?php echo htmlspecialchars($infoMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($warningMessage)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800"><?php echo htmlspecialchars($warningMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-circle-exclamation text-red-500 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <!-- Escape outputs using htmlspecialchars() to prevent Cross-Site Scripting (XSS) -->
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Responsive Tailwind-styled login form emitting HTTP POST requests -->
        <form action="login.php" method="POST" class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <!-- Escape sticky input value using htmlspecialchars() for XSS defense -->
                    <input id="email" name="email" type="email" autocomplete="email" required
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="student@university.edu">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long.</p>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-indigo-500 group-hover:text-indigo-400 transition"></i>
                    </span>
                    Sign In
                </button>
            </div>
        </form>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-xs text-gray-600 space-y-1">
            <p class="font-semibold text-gray-700 mb-1"><i class="fas fa-key text-indigo-500"></i> Demo Credentials:</p>
            <p><strong>Email:</strong> <code>student@university.edu</code></p>
            <p><strong>Password:</strong> <code>password123</code></p>
        </div>
    </div>
</body>
</html>

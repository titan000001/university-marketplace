<?php
// ==============================================================================
// 1. Afia Lubna Purnota (Branch: feature/form-processing)
// Target File: login.php (UI Interface & POST Form Sanitization)
// Exact Requirements:
//  - Responsive Tailwind-styled login form emitting HTTP POST requests.
//  - Server-side input sanitization using trim() and filter_var($email, FILTER_VALIDATE_EMAIL).
//  - Enforce password length validation (minimum 8 characters).
//  - Escape outputs using htmlspecialchars() to prevent Cross-Site Scripting (XSS).
// ==============================================================================

$errors = [];
$email = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve raw inputs
    $rawEmail = $_POST['email'] ?? '';
    $rawPassword = $_POST['password'] ?? '';

    // Server-side input sanitization using trim()
    $email = trim($rawEmail);
    $password = trim($rawPassword);

    // Email Sanitization & Validation using filter_var()
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format. Please enter a valid email address.";
    }

    // Enforce password length validation (minimum 8 characters)
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // If inputs pass validation
    if (empty($errors)) {
        $successMessage = "Sanitization and validation passed successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniMarket - Login</title>
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
            <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center mx-auto text-xl font-bold shadow-lg">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
                Sign in to UniMarket
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Campus Student Marketplace Portal
            </p>
        </div>

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

        <?php if (!empty($successMessage)): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-circle-check text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                        </p>
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
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters.</p>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-indigo-500 group-hover:text-indigo-400 transition"></i>
                    </span>
                    Sign In
                </button>
            </div>
        </form>
    </div>
</body>
</html>

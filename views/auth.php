<?php
// app/views/auth.php (previously index-standalone.php)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darae – Sign in or create an account</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>

    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Remix Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #111827;
        }
        .logo-text {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(30, 61, 89, 0.18);
        }
        .logo-subtitle {
            font-size: 0.9rem;
            color: #6B7280;
        }
        .auth-shell {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(17, 24, 39, 0.05);
            backdrop-filter: blur(14px);
        }
        .form-input { transition: all 0.25s ease; }
        .form-input:focus {
            border-color: #1E3D59;
            box-shadow: 0 0 0 3px rgba(30, 61, 89, 0.18);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #244a6c 0%, #1492a5 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(30, 61, 89, 0.2);
        }
    </style>
</head>
<body class="flex flex-col">
<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <a href="<?= htmlspecialchars($basePath) ?>/" class="text-center group" aria-label="Go to the homepage">
            <div class="logo-text group-hover:scale-[1.03] transition-transform">Darae</div>
            <div class="logo-subtitle">Cyber awareness platform</div>
        </a>
    </div>
</header>

<?php if (!empty($flash['error'] ?? null)): ?>
  <div class="container mx-auto px-4 mt-6">
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm"><?= htmlspecialchars($flash['error']) ?></div>
  </div>
<?php endif; ?>
<?php if (!empty($flash['success'] ?? null)): ?>
  <div class="container mx-auto px-4 mt-6">
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 text-sm"><?= htmlspecialchars($flash['success']) ?></div>
  </div>
<?php endif; ?>

<main class="flex-grow flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl auth-shell">
        <!-- Hero / marketing copy -->
        <div class="w-full md:w-2/5 bg-gradient-to-br from-primary to-secondary text-white p-8 flex flex-col justify-center">
            <div class="mb-8">
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                    <i class="ri-shield-keyhole-line text-2xl"></i>
                </div>
                <h2 class="text-2xl font-semibold mb-4">Join the Darae community</h2>
                <p class="opacity-90 leading-relaxed">Access curated cyber-awareness resources, stay ahead of emerging threats, and empower your organisation with secure-by-default habits.</p>
            </div>
            <div class="space-y-4 mt-8 text-sm">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-shield-check-line"></i></div>
                    <span>Featured awareness journeys and campaigns</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-calendar-event-line"></i></div>
                    <span>Quarterly live training events</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-line-chart-line"></i></div>
                    <span>Progress analytics and personal goals</span>
                </div>
            </div>
        </div>

        <!-- Authentication forms -->
        <div class="w-full md:w-3/5 bg-white p-8">
            <div class="flex border-b border-gray-200 mb-6">
                <button id="loginTab" class="py-3 px-6 font-medium text-primary border-b-2 border-primary">Sign in</button>
                <button id="registerTab" class="py-3 px-6 font-medium text-gray-500">Create account</button>
            </div>

            <form id="loginForm" class="space-y-4" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/login">
                <div>
                    <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" id="loginEmail" name="email" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="name@example.com" required>
                </div>
                <div>
                    <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="loginPassword" name="password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="Enter your password" required>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-700 gap-2">
                        <input type="checkbox" id="rememberMe" class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
                        Remember me
                    </label>
                    <a href="#" class="text-sm text-primary hover:text-primary-800">Forgot password?</a>
                </div>
                <button type="submit" class="w-full btn-primary text-white py-3 px-4 rounded-lg font-medium transition-all shadow-md">Sign in</button>
            </form>

            <form id="registerForm" class="space-y-4 hidden" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/register">
                <div>
                    <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                    <input type="text" id="fullName" name="name" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="Enter your full name" required>
                </div>
                <div>
                    <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" id="registerEmail" name="email" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="name@example.com" required>
                </div>
                <div>
                    <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="registerPassword" name="password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="Create a strong password" required>
                    <p class="text-xs text-gray-500 mt-1">Use at least eight characters including a mix of letters, numbers, and symbols.</p>
                </div>
                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="Re-enter your password" required>
                </div>
                <label class="flex items-center text-sm text-gray-700 gap-2">
                    <input type="checkbox" id="agreeTerms" class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded" required>
                    I agree to the <a href="#" class="text-primary hover:text-primary-800">Terms of Service</a> and <a href="#" class="text-primary hover:text-primary-800">Privacy Policy</a>.
                </label>
                <button type="submit" class="w-full btn-primary text-white py-3 px-4 rounded-lg font-medium transition-all shadow-md">Create account</button>
            </form>
        </div>
    </div>
</main>

<footer class="bg-gray-900 text-white py-8">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-400">© 2024 Darae Platform. All rights reserved.</p>
        <div class="mt-4 flex justify-center gap-6 text-sm">
            <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms &amp; conditions</a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy policy</a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">Contact us</a>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        loginTab.addEventListener('click', function () {
            loginTab.classList.add('text-primary', 'border-primary');
            loginTab.classList.remove('text-gray-500');
            registerTab.classList.remove('text-primary', 'border-primary');
            registerTab.classList.add('text-gray-500');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        });

        registerTab.addEventListener('click', function () {
            registerTab.classList.add('text-primary', 'border-primary');
            registerTab.classList.remove('text-gray-500');
            loginTab.classList.remove('text-primary', 'border-primary');
            loginTab.classList.add('text-gray-500');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });
    });
</script>
</body>
</html>

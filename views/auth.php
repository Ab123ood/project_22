<?php
// app/views/auth.php (was index-standalone.php)
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درع - تسجيل الدخول وإنشاء حساب</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Local Fonts (Cairo, Noto Kufi Arabic) -->
    <?php
        $bp_for_fonts = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($bp_for_fonts === '/' || $bp_for_fonts === '\\') { $bp_for_fonts = ''; }
        $fontsCssPath = __DIR__ . '/../assets/fonts/fonts.css';
        if (file_exists($fontsCssPath)) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($bp_for_fonts) . '/assets/fonts/fonts.css">';
        }
    ?>

    <!-- Remix Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Cairo', sans-serif; background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%); min-height: 100vh; display: flex; flex-direction: column; }
        .logo-text { font-family: 'Noto Kufi Arabic', sans-serif; font-weight: 700; font-size: 2rem; background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 4px 8px rgba(30, 61, 89, 0.2); }
        .logo-subtitle { font-family: 'Cairo', sans-serif; font-weight: 400; font-size: 0.875rem; color: #6B7280; }
        .auth-container { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .form-input { transition: all 0.3s ease; }
        .form-input:focus { box-shadow: 0 0 0 3px rgba(30, 61, 89, 0.3); border-color: #1E3D59; }
        .btn-primary { background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%); }
        .btn-primary:hover { background: linear-gradient(135deg, #2a5075 0%, #1e8fa5 100%); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(30, 61, 89, 0.2); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #1E3D59; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #17A2B8; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1E3D59', 50: '#f0f7ff', 100: '#e0f0ff', 200: '#bae1ff', 300: '#7cc7ff', 400: '#36abff', 500: '#0d8ce6', 600: '#006dc4', 700: '#00559e', 800: '#004782', 900: '#1E3D59' },
                        secondary: { DEFAULT: '#17A2B8', 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a' }
                    }
                }
            }
        }
    </script>
</head>
<body class="flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center">
                <a href="<?= htmlspecialchars($basePath) ?>/" class="text-center group" aria-label="الانتقال إلى الصفحة الرئيسية">
                    <div class="logo-text group-hover:scale-[1.03] transition-transform">درع</div>
                    <div class="logo-subtitle">منصة الوعي السيبراني</div>
                </a>
            </div>
    
        </div>
    </header>

    <!-- Flash messages -->
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

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-4xl flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl auth-container">
            <!-- Left Side - Info -->
            <div class="w-full md:w-2/5 bg-gradient-to-br from-primary to-secondary text-white p-8 flex flex-col justify-center">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-shield-keyhole-line text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">انضم إلى منصة درع</h2>
                    <p class="opacity-90">كن جزءاً من مجتمعنا الأمني واطلع على أحدث حملات التوعية بالأمن السيبراني</p>
                </div>
                <div class="space-y-4 mt-8">
                    <div class="flex items-center"><div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-shield-check-line"></i></div><span>محتوى توعوي مميز</span></div>
                    <div class="flex items-center"><div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-calendar-event-line"></i></div><span>حملات أمنية مستمرة</span></div>
                    <div class="flex items-center"><div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"><i class="ri-line-chart-line"></i></div><span>تتبع تقدمك في الوعي الأمني</span></div>
                </div>
            </div>

            <!-- Right Side - Forms -->
            <div class="w-full md:w-3/5 bg-white p-8">
                <div class="flex border-b border-gray-200 mb-6">
                    <button id="loginTab" class="py-3 px-6 font-medium text-primary border-b-2 border-primary">تسجيل الدخول</button>
                    <button id="registerTab" class="py-3 px-6 font-medium text-gray-500">إنشاء حساب</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-4" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/login">
                    <div>
                        <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" id="loginEmail" name="email" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="example@domain.com" required>
                    </div>
                    <div>
                        <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
                        <input type="password" id="loginPassword" name="password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="أدخل كلمة المرور" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="rememberMe" class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="rememberMe" class="mr-2 block text-sm text-gray-700">تذكرني</label>
                        </div>
                        <a href="#" class="text-sm text-primary hover:text-primary-800">نسيت كلمة المرور؟</a>
                    </div>
                    <button type="submit" class="w-full btn-primary text-white py-3 px-4 rounded-lg font-medium transition-all shadow-md">تسجيل الدخول</button>
                </form>

                <!-- Register Form -->
                <form id="registerForm" class="space-y-4 hidden" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/register">
                    <div>
                        <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل</label>
                        <input type="text" id="fullName" name="name" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="أدخل اسمك الكامل" required>
                    </div>
                    <div>
                        <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input type="email" id="registerEmail" name="email" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="example@domain.com" required>
                    </div>
                    <div>
                        <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
                        <input type="password" id="registerPassword" name="password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="كلمة مرور قوية" required>
                        <p class="text-xs text-gray-500 mt-1">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل</p>
                    </div>
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                        <input type="password" id="confirmPassword" name="confirm_password" class="w-full px-4 py-3 rounded-lg form-input border border-gray-300 focus:outline-none focus:border-primary" placeholder="أعد إدخال كلمة المرور" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="agreeTerms" class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded" required>
                        <label for="agreeTerms" class="mr-2 block text-sm text-gray-700">أوافق على <a href="#" class="text-primary hover:text-primary-800">شروط الخدمة</a> و <a href="#" class="text-primary hover:text-primary-800">سياسة الخصوصية</a></label>
                    </div>
                    <button type="submit" class="w-full btn-primary text-white py-3 px-4 rounded-lg font-medium transition-all shadow-md">إنشاء حساب</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400"> 2024 منصة درع. جميع الحقوق محفوظة.</p>
            <div class="mt-4 flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">الشروط والأحكام</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">سياسة الخصوصية</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">اتصل بنا</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            // تبديل بين نماذج تسجيل الدخول وإنشاء الحساب
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

<?php
// app/views/layout/footer.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$isLoggedIn = !empty($_SESSION['user_id']);
?>
<footer class="bg-gray-900 text-white pt-14 pb-6">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-10">
        <div>
          <a href="<?= $basePath ?>/" class="inline-block group" aria-label="الانتقال إلى الصفحة الرئيسية">
            <div class="logo-text text-white mb-1 group-hover:scale-[1.02] transition-transform">درع</div>
          </a>
          <p class="text-gray-400 text-sm mb-4">منصة لتعزيز الوعي السيبراني وحماية البيانات.</p>
          <div class="flex gap-3 text-gray-400">
            <a href="#" class="hover:text-white transition-colors" aria-label="فيسبوك" rel="noopener"><i class="ri-facebook-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="إكس" rel="noopener"><i class="ri-twitter-x-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="لينكدإن" rel="noopener"><i class="ri-linkedin-fill"></i></a>
          </div>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">المحتوى</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li>
              <a href="<?= $basePath ?>/content" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-left-line ml-1"></i>
                <span>المواد التوعوية</span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/exams') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-left-line ml-1"></i>
                <span>الاختبارات</span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/surveys') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-left-line ml-1"></i>
                <span>الاستبيانات</span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">روابط سريعة</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <?php if (!$isLoggedIn): ?>
              <li>
                <a href="<?= $basePath ?>/auth" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-left-line ml-1"></i>
                  <span>تسجيل الدخول</span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/auth#register" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-left-line ml-1"></i>
                  <span>إنشاء حساب</span>
                </a>
              </li>
            <?php else: ?>
              <li>
                <a href="<?= $basePath ?>/profile" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-left-line ml-1"></i>
                  <span>حسابي</span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/logout" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-left-line ml-1"></i>
                  <span>تسجيل الخروج</span>
                </a>
              </li>
            <?php endif; ?>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/leaderboard') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-left-line ml-1"></i>
                <span>لوحة المتصدرين</span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">تواصل معنا</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li class="flex items-start">
              <i class="ri-mail-line ml-2 mt-1"></i>
              <a href="mailto:info@darae-platform.sa" class="hover:text-white">info@darae-platform.sa</a>
            </li>
            <li class="flex items-start">
              <i class="ri-phone-line ml-2 mt-1"></i>
              <a href="tel:+966123456789" class="hover:text-white">+966 12 345 6789</a>
            </li>
            <li class="flex items-start">
              <i class="ri-map-pin-line ml-2 mt-1"></i>
              <span>المملكة العربية السعودية</span>
            </li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-800 pt-6 text-center text-gray-400 text-sm">
        <p> 2024 منصة درع. جميع الحقوق محفوظة.</p>
      </div>
    </div>
  </footer>

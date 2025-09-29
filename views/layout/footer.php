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
          <a href="<?= $basePath ?>/" class="inline-block group" aria-label="Go to the homepage">
            <div class="logo-text text-white mb-1 group-hover:scale-[1.02] transition-transform">Darae</div>
          </a>
          <p class="text-gray-400 text-sm mb-4">A platform dedicated to building cyber awareness and protecting data.</p>
          <div class="flex gap-3 text-gray-400">
            <a href="#" class="hover:text-white transition-colors" aria-label="Facebook" rel="noopener"><i class="ri-facebook-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="X" rel="noopener"><i class="ri-twitter-x-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="LinkedIn" rel="noopener"><i class="ri-linkedin-fill"></i></a>
          </div>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">Learning</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li>
              <a href="<?= $basePath ?>/content" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line mr-1"></i>
                <span>Awareness materials</span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/exams') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line mr-1"></i>
                <span>Assessments</span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/surveys') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line mr-1"></i>
                <span>Surveys</span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">Quick links</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <?php if (!$isLoggedIn): ?>
              <li>
                <a href="<?= $basePath ?>/auth" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line mr-1"></i>
                  <span>Sign in</span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/auth#register" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line mr-1"></i>
                  <span>Create an account</span>
                </a>
              </li>
            <?php else: ?>
              <li>
                <a href="<?= $basePath ?>/profile" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line mr-1"></i>
                  <span>My account</span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/logout" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line mr-1"></i>
                  <span>Sign out</span>
                </a>
              </li>
            <?php endif; ?>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/leaderboard') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line mr-1"></i>
                <span>Leaderboard</span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg">Contact</h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li class="flex items-start">
              <i class="ri-mail-line mr-2 mt-1"></i>
              <a href="mailto:info@darae-platform.sa" class="hover:text-white">info@darae-platform.sa</a>
            </li>
            <li class="flex items-start">
              <i class="ri-phone-line mr-2 mt-1"></i>
              <a href="tel:+966123456789" class="hover:text-white">+966 12 345 6789</a>
            </li>
            <li class="flex items-start">
              <i class="ri-map-pin-line mr-2 mt-1"></i>
              <span>Kingdom of Saudi Arabia</span>
            </li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-800 pt-6 text-center text-gray-400 text-sm">
        <p>© 2024 Darae Platform. All rights reserved.</p>
      </div>
    </div>
  </footer>

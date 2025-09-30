<?php
// app/views/layout/footer.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$isLoggedIn = !empty($_SESSION['user_id']);
$isRtlLayout = ($lang['dir'] ?? (($isRtl ?? false) ? 'rtl' : 'ltr')) === 'rtl';
$iconMargin = $isRtlLayout ? 'ml-1' : 'mr-1';
$contactIconMargin = $isRtlLayout ? 'ml-2' : 'mr-2';
$logoTitle = __('header.brand.title');
$logoSubtitle = __('footer.tagline');
?>
<footer class="bg-gray-900 text-white pt-14 pb-6">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-10">
          <div>
          <a href="<?= $basePath ?>/" class="inline-block group" aria-label="<?= htmlspecialchars(__('header.brand.home_aria')) ?>">
            <div class="logo-text text-white mb-1 group-hover:scale-[1.02] transition-transform"><?= htmlspecialchars($logoTitle) ?></div>
          </a>
          <p class="text-gray-400 text-sm mb-4"><?= htmlspecialchars($logoSubtitle) ?></p>
          <div class="flex gap-3 text-gray-400">
            <a href="#" class="hover:text-white transition-colors" aria-label="<?= htmlspecialchars(__('footer.social.facebook')) ?>" rel="noopener"><i class="ri-facebook-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="<?= htmlspecialchars(__('footer.social.x')) ?>" rel="noopener"><i class="ri-twitter-x-fill"></i></a>
            <a href="#" class="hover:text-white transition-colors" aria-label="<?= htmlspecialchars(__('footer.social.linkedin')) ?>" rel="noopener"><i class="ri-linkedin-fill"></i></a>
          </div>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg"><?= htmlspecialchars(__('footer.learning')) ?></h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li>
              <a href="<?= $basePath ?>/content" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                <span><?= htmlspecialchars(__('footer.awareness_materials')) ?></span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/exams') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                <span><?= htmlspecialchars(__('footer.assessments')) ?></span>
              </a>
            </li>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/surveys') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                <span><?= htmlspecialchars(__('footer.surveys')) ?></span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg"><?= htmlspecialchars(__('footer.quick_links')) ?></h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <?php if (!$isLoggedIn): ?>
              <li>
                <a href="<?= $basePath ?>/auth" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                  <span><?= htmlspecialchars(__('footer.sign_in')) ?></span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/auth#register" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                  <span><?= htmlspecialchars(__('footer.create_account')) ?></span>
                </a>
              </li>
            <?php else: ?>
              <li>
                <a href="<?= $basePath ?>/profile" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                  <span><?= htmlspecialchars(__('footer.my_account')) ?></span>
                </a>
              </li>
              <li>
                <a href="<?= $basePath ?>/logout" class="hover:text-white transition-colors flex items-center">
                  <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                  <span><?= htmlspecialchars(__('footer.sign_out')) ?></span>
                </a>
              </li>
            <?php endif; ?>
            <li>
              <a href="<?= $isLoggedIn ? ($basePath . '/leaderboard') : ($basePath . '/auth') ?>" class="hover:text-white transition-colors flex items-center">
                <i class="ri-arrow-right-line <?= $iconMargin ?>"></i>
                <span><?= htmlspecialchars(__('footer.leaderboard')) ?></span>
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-lg"><?= htmlspecialchars(__('footer.contact')) ?></h4>
          <ul class="text-gray-400 text-sm space-y-3">
            <li class="flex items-start">
              <i class="ri-mail-line <?= $contactIconMargin ?> mt-1"></i>
              <a href="mailto:info@darae-platform.sa" class="hover:text-white"><?= htmlspecialchars(__('footer.email')) ?></a>
            </li>
            <li class="flex items-start">
              <i class="ri-phone-line <?= $contactIconMargin ?> mt-1"></i>
              <a href="tel:+966123456789" class="hover:text-white"><?= htmlspecialchars(__('footer.phone')) ?></a>
            </li>
            <li class="flex items-start">
              <i class="ri-map-pin-line <?= $contactIconMargin ?> mt-1"></i>
              <span><?= htmlspecialchars(__('footer.location')) ?></span>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-800 pt-6 text-center text-gray-400 text-sm">
        <p><?= htmlspecialchars(__('footer.copyright')) ?></p>
      </div>
    </div>
  </footer>

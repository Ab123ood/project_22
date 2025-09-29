<?php
// app/views/layout/header.php
// Determine header context: defaults to the public header when omitted
$headerContext = $headerContext ?? 'public';

// The public experience depends on session awareness
if (session_status() === PHP_SESSION_NONE) { @session_start(); }

// A user is considered signed in when the session exposes user_id (matching AuthMiddleware)
$isLoggedIn = !empty($_SESSION['user_id']);
$user = null; $displayName = 'User';
$userId = (int)($_SESSION['user_id'] ?? 0);
// Load the profile when a session id is present
if (!empty($_SESSION['user_id'])) {
    try {
        $user = Database::query('SELECT id, user_name, email, department_id, role_id FROM users WHERE id = :id', [':id'=>$userId])->fetch();
    } catch (Throwable $e) { /* swallow database exceptions */ }
}

// Compose a friendly display name from the profile record, e-mail, then fallback text
$displayName = htmlspecialchars(
    trim(($user['user_name'] ?? '').' '.($user['last_name'] ?? '')) ?: (($user['email'] ?? $_SESSION['user_email'] ?? '') ?: 'User')
);

$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($basePath && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath));
    if ($currentPath === '') { $currentPath = '/'; }
}
$inAdmin = str_starts_with($currentPath, '/admin');

$roleId = (int)($user['role_id'] ?? ($_SESSION['role_id'] ?? 0));
?>
<?php if ($headerContext === 'admin'): ?>
  <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center">
              <h1 class="text-xl md:text-2xl font-bold text-gray-900 mr-4">Administration</h1>
          </div>
          <div class="flex items-center gap-3">
              <?php if ($isLoggedIn): ?>
                <div class="relative" id="adminUserMenu">
                  <button type="button" class="flex items-center gap-3 group" aria-haspopup="true" aria-expanded="false">
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                      <i class="ri-user-3-line"></i>
                    </div>
                    <span class="text-sm text-gray-700 font-medium"><?= $displayName ?></span>
                    <i class="ri-arrow-down-s-line text-gray-500 group-aria-expanded:rotate-180 transition-transform"></i>
                  </button>
                  <div class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 hidden" role="menu">
                    <a href="<?= $basePath ?>/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem"><i class="ri-user-line mr-2"></i> Profile</a>
                    <a href="<?= $basePath ?>/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem"><i class="ri-settings-3-line mr-2"></i> Settings</a>
                    <a href="<?= $basePath ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem"><i class="ri-logout-box-r-line mr-2"></i> Sign out</a>
                  </div>
                </div>
              <?php endif; ?>
          </div>
      </div>
  </header>
<?php else: ?>
  <header id="appHeader" class="app-header bg-transparent sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="<?= $basePath ?>/" class="flex items-center gap-2 group" aria-label="Go to the homepage">
          <div class="text-center leading-tight">
            <div class="logo-text group-hover:scale-[1.03] transition-transform">Darae</div>
            <div class="text-xs text-gray-500 -mt-0.5">Cyber awareness platform</div>
          </div>
        </a>
      </div>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-6">
        <a href="<?= $basePath ?>/" class="font-medium transition-colors <?= $currentPath==='/' ? 'text-primary' : 'text-gray-700 hover:text-primary' ?>">Home</a>
        <a href="<?= $basePath ?>/content" class="font-medium transition-colors <?= str_starts_with($currentPath,'/content') ? 'text-primary' : 'text-gray-700 hover:text-primary' ?>">Awareness content</a>
        <?php if ($isLoggedIn): ?>
          <a href="<?= $basePath ?>/exams" class="font-medium transition-colors <?= str_starts_with($currentPath,'/exams') ? 'text-primary' : 'text-gray-700 hover:text-primary' ?>">Assessments</a>
          <a href="<?= $basePath ?>/surveys" class="font-medium transition-colors <?= str_starts_with($currentPath,'/surveys') ? 'text-primary' : 'text-gray-700 hover:text-primary' ?>">Surveys</a>
          <a href="<?= $basePath ?>/leaderboard" class="font-medium transition-colors <?= str_starts_with($currentPath,'/leaderboard') ? 'text-primary' : 'text-gray-700 hover:text-primary' ?>">Leaderboard</a>
        <?php endif; ?>
      </nav>

      <div class="hidden md:flex items-center gap-3">
        <?php if ($isLoggedIn): ?>
          <?php if (!$inAdmin): ?>
            <?php if ($roleId === 1): ?>
              <a href="<?= $basePath ?>/profile" class="px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors">My account</a>
            <?php else: ?>
              <a href="<?= $basePath ?>/dashboard" class="px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors">Management console</a>
            <?php endif; ?>
          <?php endif; ?>
          <div class="relative" id="publicUserMenu">
            <button type="button" class="flex items-center gap-3 group" aria-haspopup="true" aria-expanded="false">
              <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                <i class="ri-user-3-line"></i>
              </div>
              <span class="text-sm text-gray-700 font-medium"><?= $displayName ?></span>
              <i class="ri-arrow-down-s-line text-gray-500 group-aria-expanded:rotate-180 transition-transform"></i>
            </button>
            <div class="absolute left-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 hidden" role="menu">
              <a href="<?= $basePath ?>/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" role="menuitem"><i class="ri-user-line mr-2"></i> Profile</a>
              <a href="<?= $basePath ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem"><i class="ri-logout-box-r-line mr-2"></i> Sign out</a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= $basePath ?>/content" class="hidden md:inline-flex bg-primary/10 text-primary px-4 py-2 rounded-lg hover:bg-primary/20">Start learning</a>
          <a href="<?= $basePath ?>/auth" class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-primary/90 shadow-sm">Sign in</a>
        <?php endif; ?>
      </div>

      <!-- Mobile controls -->
      <div class="md:hidden flex items-center gap-2">
        <a href="<?= $basePath ?>/content" class="text-primary px-3 py-1.5 rounded-lg bg-primary/10">Learn</a>
        <button id="mobileMenuBtn" class="w-10 h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50" aria-controls="mobileMenu" aria-expanded="false" aria-label="Open navigation">
          <i class="ri-menu-3-line text-xl" aria-hidden="true"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70">
      <div class="container mx-auto px-4 py-3 flex flex-col gap-3">
        <a href="<?= $basePath ?>/" class="font-medium <?= $currentPath==='/' ? 'text-primary' : 'text-gray-700' ?>">Home</a>
        <a href="<?= $basePath ?>/content" class="font-medium <?= str_starts_with($currentPath,'/content') ? 'text-primary' : 'text-gray-700' ?>">Awareness content</a>
        <?php if ($isLoggedIn): ?>
          <a href="<?= $basePath ?>/exams" class="font-medium <?= str_starts_with($currentPath,'/exams') ? 'text-primary' : 'text-gray-700' ?>">Assessments</a>
          <a href="<?= $basePath ?>/surveys" class="font-medium <?= str_starts_with($currentPath,'/surveys') ? 'text-primary' : 'text-gray-700' ?>">Surveys</a>
          <a href="<?= $basePath ?>/leaderboard" class="font-medium <?= str_starts_with($currentPath,'/leaderboard') ? 'text-primary' : 'text-gray-700' ?>">Leaderboard</a>
        <?php endif; ?>
        <?php if ($isLoggedIn): ?>
          <?php if ($roleId === 1): ?>
            <a href="<?= $basePath ?>/profile" class="font-medium text-gray-700">My account</a>
          <?php else: ?>
            <a href="<?= $basePath ?>/dashboard" class="font-medium text-gray-700">Management console</a>
          <?php endif; ?>
          <a href="<?= $basePath ?>/logout" class="font-medium text-red-600">Sign out</a>
        <?php else: ?>
          <a href="<?= $basePath ?>/auth" class="font-medium text-gray-700">Sign in</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
<?php endif; ?>
<script>
  // Enable account dropdown menus (public + admin)
  (function(){
    function bindMenu(rootId){
      const root = document.getElementById(rootId);
      if(!root) return;
      const btn = root.querySelector('button');
      const menu = root.querySelector('[role="menu"]');
      if(!btn || !menu) return;
      const toggle = (open) => {
        if(open === undefined) menu.classList.toggle('hidden');
        else menu.classList.toggle('hidden', !open);
        const expanded = !menu.classList.contains('hidden');
        btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      };
      btn.addEventListener('click', (e)=>{ e.stopPropagation(); toggle(); });
      document.addEventListener('click', (e)=>{
        if(!menu.classList.contains('hidden') && !root.contains(e.target)) toggle(false);
      });
      window.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') toggle(false); });
    }
    bindMenu('publicUserMenu');
    bindMenu('adminUserMenu');

    // Update header styling once the visitor scrolls for readability
    const header = document.getElementById('appHeader');
    const onScroll = () => {
      if (!header) return;
      const scrolled = window.scrollY > 4;
      header.classList.toggle('bg-white/90', scrolled);
      header.classList.toggle('backdrop-blur', scrolled);
      header.classList.toggle('shadow-sm', scrolled);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    // Mobile menu logic
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileBtn && mobileMenu) {
      const toggleMobile = () => {
        const willOpen = mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden');
        mobileBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        document.body.classList.toggle('overflow-hidden', willOpen);
      };
      mobileBtn.addEventListener('click', (e)=>{ e.stopPropagation(); toggleMobile(); });
      document.addEventListener('click', (e)=>{
        if (!mobileMenu.classList.contains('hidden') && !mobileMenu.contains(e.target) && e.target !== mobileBtn) {
          toggleMobile();
        }
      });
      window.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) toggleMobile(); });
    }
  })();
</script>

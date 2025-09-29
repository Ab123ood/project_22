<?php
// app/views/layout/sidebar.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
// Determine current path to highlight the active navigation item
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Session initialisation and user lookup
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$userId = (int)($_SESSION['user_id'] ?? 0);
$roleId = (int)($_SESSION['role_id'] ?? 0); // 1=Employee, 2=Awareness manager, 3=Admin
$isAdmin = ($roleId === 3);
$isManagerOrAdmin = in_array($roleId, [2, 3], true);

$user = null;
if ($userId > 0) {
  try {
    $user = Database::query('SELECT id, user_name, email, department_id, role_id FROM users WHERE id = :id', [':id' => $userId])->fetch();
  } catch (Throwable $e) { /* ignore */ }
}

$displayName = htmlspecialchars(trim((string)($user['user_name'] ?? '')) ?: 'User');

$subtitleParts = [];
if (!empty($user['department_id'])) { $subtitleParts[] = 'Department: #'.(int)$user['department_id']; }
if (!empty($user['role_id'])) { $subtitleParts[] = 'Role: #'.(int)$user['role_id']; }
$displaySubtitle = htmlspecialchars(implode(' • ', $subtitleParts));
?>

<style>
.sidebar-logo {
    font-family: 'Inter', 'Roboto', sans-serif;
    font-weight: 700;
    font-size: 1.75rem;
    background: linear-gradient(135deg, #1E3D59 0%, #17A2B8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(30, 61, 89, 0.1);
}

.sidebar-subtitle {
    font-family: 'Inter', 'Roboto', sans-serif;
    font-weight: 500;
    font-size: 1rem;
    color: #6B7280;
}

.sidebar-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    font-size: 1rem;
    font-weight: 600;
    padding: 0.875rem 1rem;
}

.sidebar-item i {
    font-size: 1.25rem;
}

.sidebar-item:hover {
    background-color: #F3F4F6;
    color: #1E3D59;
    border-left-color: #1E3D59;
}

.sidebar-item.active {
    background-color: #E6F7FF;
    color: #1E3D59;
    border-left-color: #1E3D59;
}
</style>

<!-- Mobile sidebar trigger -->
<button id="openSidebarBtn" class="md:hidden fixed bottom-4 right-4 z-40 w-12 h-12 rounded-full shadow-lg bg-white border border-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-50" aria-controls="mobileSidebar" aria-expanded="false" aria-label="Open menu">
  <i class="ri-menu-3-line text-2xl" aria-hidden="true"></i>
</button>

<!-- Mobile overlay -->
<div id="sidebarOverlay" class="md:hidden fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-40"></div>

<!-- Mobile sidebar (off-canvas) -->
<aside id="mobileSidebar" class="md:hidden fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-white border-r border-gray-200 shadow-xl transform -translate-x-full transition-transform duration-300 z-50" role="dialog" aria-modal="true" aria-label="Sidebar navigation">
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
      <a href="<?= $basePath ?>/" class="flex items-center gap-3" aria-label="Go to the homepage">
        <div class="w-10 h-10 bg-gradient-to-br from-[#1E3D59] to-[#17A2B8] rounded-lg flex items-center justify-center">
          <i class="ri-shield-check-line text-white text-xl"></i>
        </div>
        <div class="text-left">
          <div class="sidebar-logo leading-none">Darae</div>
          <div class="sidebar-subtitle text-sm">Cyber awareness platform</div>
        </div>
      </a>
      <button id="closeSidebarBtn" class="w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50" aria-label="Close menu">
        <i class="ri-close-line text-xl" aria-hidden="true"></i>
      </button>
    </div>
    <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-1">
      <?php if ($isManagerOrAdmin): ?>
      <a href="<?= $basePath ?>/dashboard" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/dashboard')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-dashboard-line text-lg text-primary"></i>
          <span>Management dashboard</span>
      </a>
      <a href="<?= $basePath ?>/admin/content" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/content')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-movie-line text-lg"></i>
          <span>Content management</span>
      </a>
      <a href="<?= $basePath ?>/admin/exams" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/exams')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-file-list-3-line text-lg"></i>
          <span>Assessments</span>
      </a>
      <a href="<?= $basePath ?>/admin/surveys" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/surveys')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-feedback-line text-lg"></i>
          <span>Surveys</span>
      </a>
      <?php endif; ?>
      <?php if ($isAdmin): ?>
      <a href="<?= $basePath ?>/admin/users" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/users')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-user-line text-lg"></i>
          <span>User management</span>
      </a>
      <a href="<?= $basePath ?>/admin/reports" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/reports')===0 ? ' active text-primary' : ' text-gray-600') ?>">
          <i class="ri-bar-chart-line text-lg"></i>
          <span>Reports &amp; analytics</span>
      </a>
      <?php endif; ?>
    </nav>
    <div class="px-4 py-4 border-t border-gray-200">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
          <i class="ri-user-3-line text-lg"></i>
        </div>
        <div class="min-w-0">
          <div class="font-semibold text-gray-900 text-base truncate"><?= $displayName ?></div>
          <?php if ($displaySubtitle): ?>
            <div class="text-sm text-gray-500 truncate"><?= $displaySubtitle ?></div>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?= $basePath ?>/logout" class="flex items-center justify-center w-full text-base text-gray-600 hover:text-white hover:bg-primary bg-gray-100 border border-gray-200 rounded-lg py-2 transition-colors">
        <i class="ri-logout-box-r-line mr-2 text-lg"></i>
        Sign out
      </a>
    </div>
  </div>
</aside>

<!-- Desktop sidebar -->
<div class="hidden md:flex md:flex-shrink-0">
  <div class="flex flex-col w-64">
    <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white border-r border-gray-200 shadow-lg">
      <!-- Logo -->
      <div class="flex flex-col items-center px-4 mb-4">
        <a href="<?= $basePath ?>/" class="flex flex-col items-center text-center transition-transform hover:scale-105">
          <div class="w-16 h-16 bg-gradient-to-br from-[#1E3D59] to-[#17A2B8] rounded-xl flex items-center justify-center mb-3 shadow-md">
            <i class="ri-shield-check-line text-white text-2xl"></i>
          </div>
          <div class="sidebar-logo">Darae</div>
          <div class="sidebar-subtitle">Cyber awareness platform</div>
        </a>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-2 space-y-1">
        <?php if ($isManagerOrAdmin): ?>
        <a href="<?= $basePath ?>/dashboard" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/dashboard')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-dashboard-line text-lg text-primary"></i>
            <span>Management dashboard</span>
        </a>
        <a href="<?= $basePath ?>/admin/content" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/content')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-movie-line text-lg"></i>
            <span>Content management</span>
        </a>
        <a href="<?= $basePath ?>/admin/exams" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/exams')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-file-list-3-line text-lg"></i>
            <span>Assessments</span>
        </a>
        <a href="<?= $basePath ?>/admin/surveys" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/surveys')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-feedback-line text-lg"></i>
            <span>Surveys</span>
        </a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <a href="<?= $basePath ?>/admin/users" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/users')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-user-line text-lg"></i>
            <span>User management</span>
        </a>
        <a href="<?= $basePath ?>/admin/reports" class="sidebar-item group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg<?= (strpos($current, $basePath.'/admin/reports')===0 ? ' active text-primary' : ' text-gray-600') ?>">
            <i class="ri-bar-chart-line text-lg"></i>
            <span>Reports &amp; analytics</span>
        </a>
        <?php endif; ?>
      </nav>

      <!-- User card -->
      <div class="mx-4 mt-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
            <i class="ri-user-3-line text-lg"></i>
          </div>
          <div class="min-w-0">
            <div class="font-semibold text-gray-900 text-base truncate"><?= $displayName ?></div>
            <?php if ($displaySubtitle): ?>
              <div class="text-sm text-gray-500 truncate"><?= $displaySubtitle ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="mt-3">
          <a href="<?= $basePath ?>/logout" class="flex items-center gap-2 text-base text-gray-500 hover:text-primary transition-colors font-medium">
              <i class="ri-logout-box-r-line text-lg"></i>
              <span>Sign out</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const openBtn = document.getElementById('openSidebarBtn');
  const closeBtn = document.getElementById('closeSidebarBtn');
  const sidebar = document.getElementById('mobileSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!openBtn || !sidebar || !overlay) return;

  function setOpen(isOpen){
    sidebar.classList.toggle('-translate-x-full', !isOpen);
    overlay.classList.toggle('hidden', !isOpen);
    openBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    document.body.classList.toggle('overflow-hidden', isOpen);
  }

  openBtn.addEventListener('click', (e)=>{ e.stopPropagation(); setOpen(true); });
  if (closeBtn) closeBtn.addEventListener('click', ()=> setOpen(false));
  overlay.addEventListener('click', ()=> setOpen(false));
  window.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') setOpen(false); });
})();
</script>

<?php
// app/views/layout/layout.php
$langCode = $lang['code'] ?? ($locale ?? 'en');
$dir = $lang['dir'] ?? ($isRtl ?? false ? 'rtl' : 'ltr');
$isRtlLayout = ($dir === 'rtl');
$pageTitle = $pageTitle ?? __('layout.default_title');
?><!DOCTYPE html>
<html lang="<?= htmlspecialchars($langCode) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!empty($metaDescription ?? '')): ?>
        <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
       <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3D59',
                        secondary: '#17A2B8',
                        accent: '#FF6B35',
                        light: '#F8F9FA',
                        dark: '#212529'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'roboto': ['Roboto', 'system-ui', 'sans-serif']
                    },
                    boxShadow: {
                        'custom': '0 10px 25px rgba(0, 0, 0, 0.1)',
                        'custom-hover': '0 15px 30px rgba(0, 0, 0, 0.15)'
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    
    <style>
        /* === Unified color system === */
        :root {
            --primary: #1E3D59;
            --primary-light: #2A5A7B;
            --primary-dark: #163041;
            --secondary: #17A2B8;
            --secondary-light: #20C4DC;
            --secondary-dark: #138496;
            --accent: #28A745;
            --warning: #FFC107;
            --danger: #DC3545;
            --info: #6C757D;
            --success: #28A745;
            
            /* Neutral scale */
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
            
            /* Backgrounds & borders */
            --bg-primary: var(--gray-50);
            --bg-card: #FFFFFF;
            --border-light: var(--gray-200);
            --border-medium: var(--gray-300);
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            
            /* Border radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        /* === Global typography === */
        body {
            font-family: <?= $isRtlLayout ? "'Tajawal', 'Cairo', sans-serif" : "'Inter', 'Roboto', sans-serif" ?>;
            line-height: 1.6;
            color: var(--gray-700);
            background-color: var(--bg-primary);
        }

        body.rtl {
            direction: rtl;
        }

        body.rtl .rtl\:space-x-reverse > * {
            --tw-space-x-reverse: 1;
        }

        html[dir="rtl"] body {
            text-align: right;
        }

        html[dir="rtl"] .text-left {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

            .logo-text {
            font-family: 'Inter', 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(30, 61, 89, 0.2);
        }
        
        .hero-bg {
            background-image: url('https://readdy.ai/api/search-image?query=modern%20cybersecurity%20digital%20shield%20protection%20network%20technology%20background%20with%20blue%20gradient%20colors%2C%20clean%20minimalist%20design%2C%20professional%20corporate%20style%2C%20soft%20lighting%2C%20high-tech%20atmosphere%2C%20digital%20security%20concept%20illustration&width=1920&height=1080&seq=hero001&orientation=landscape');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 61, 89, 0.9) 0%, rgba(23, 162, 184, 0.8) 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
    
        
        .app-header {
            transition: all 0.3s ease;
        }
        
        .app-header--scrolled {
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .app-header--scrolled .header-link {
            color: #4B5563;
        }
        
        .app-header--scrolled .header-link:hover {
            color: var(--primary);
        }
        
        .content-card, .campaign-card {
            transition: all 0.3s ease;
            border: 1px solid #E5E7EB;
            overflow: hidden;
        }
        
        .content-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .campaign-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .campaign-image {
            transition: transform 0.5s ease;
        }
        
        .campaign-card:hover .campaign-image {
            transform: scale(1.05);
        }
        
        .icon-box {
            transition: all 0.3s ease;
        }
        
        .content-card:hover .icon-box {
            transform: rotateY(180deg);
            background-color: var(--primary);
        }
        
        .content-card:hover .icon-box i {
            color: white;
        }
        
        /* Mobile refinements */
        @media (max-width: 768px) {
            .logo-text {
                font-size: 1.8rem;
            }
            
            .hero-bg {
                background-position: left center;
            }
            
            .mobile-menu {
                transition: all 0.3s ease;
            }
        }
        
        /* Accessibility */
        a:focus, button:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        /* Performance */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Reveal animations */
        .fade-in {
            animation: fadeIn 0.6s ease forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-1 {
            animation-delay: 0.2s;
        }
        
        .delay-2 {
            animation-delay: 0.4s;
        }
        
        .delay-3 {
            animation-delay: 0.6s;
        }
        /* === Heading system === */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', 'Roboto', sans-serif;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1.3;
        }
        
        h1 { font-size: 2.75rem; margin-bottom: var(--spacing-lg); }
        h2 { font-size: 2.25rem; margin-bottom: var(--spacing-md); }
        h3 { font-size: 1.875rem; margin-bottom: var(--spacing-md); }
        h4 { font-size: 1.625rem; margin-bottom: var(--spacing-sm); }
        h5 { font-size: 1.375rem; margin-bottom: var(--spacing-sm); }
        h6 { font-size: 1.25rem; margin-bottom: var(--spacing-sm); }

        /* === Type scale === */
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .text-4xl { font-size: 2.25rem; }
        .text-5xl { font-size: 3rem; }

        /* === Font weights === */
        .font-light { font-weight: 300; }
        .font-normal { font-weight: 400; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }

        /* === Button system === */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            font-family: 'Inter', 'Roboto', sans-serif;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        .btn-secondary:hover {
            background: var(--secondary-dark);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-ghost {
            background: rgba(30, 61, 89, 0.1);
            color: var(--primary);
        }
        .btn-ghost:hover {
            background: rgba(30, 61, 89, 0.2);
        }

        /* === Card patterns === */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .card-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-light);
            background: var(--gray-50);
        }
        
        .card-body {
            padding: var(--spacing-lg);
        }
        
        .card-footer {
            padding: var(--spacing-lg);
            border-top: 1px solid var(--border-light);
            background: var(--gray-50);
        }

        /* === Form system === */
        .form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--spacing-sm);
            font-size: 0.875rem;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-medium);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: var(--bg-card);
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 61, 89, 0.1);
        }
        
        .form-error {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: var(--spacing-xs);
        }

        /* === Badges & pills === */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary { background: rgba(30, 61, 89, 0.1); color: var(--primary); }
        .badge-success { background: rgba(40, 167, 69, 0.1); color: var(--success); }
        .badge-warning { background: rgba(255, 193, 7, 0.1); color: #B45309; }
        .badge-danger { background: rgba(220, 53, 69, 0.1); color: var(--danger); }
        .badge-info { background: rgba(108, 117, 125, 0.1); color: var(--info); }

        /* === Alert system === */
        .alert {
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-lg);
            border: 1px solid;
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-sm);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-color: var(--success);
            color: #155724;
        }
        
        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-color: var(--warning);
            color: #856404;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-color: var(--danger);
            color: #721c24;
        }
        
        .alert-info {
            background: rgba(23, 162, 184, 0.1);
            border-color: var(--secondary);
            color: #0c5460;
        }

        /* === Animation helpers === */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        .slide-in-right {
            animation: slideInRight 0.5s ease-out;
        }
        
        .pulse-hover:hover {
            animation: pulse 0.3s ease-in-out;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        /* === Header effects === */
        .app-header { 
            transition: all 0.25s ease;
            backdrop-filter: blur(10px);
        }
        .app-header--scrolled { 
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow-md);
        }

        /* === Table patterns === */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table th {
            background: var(--gray-50);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 1px solid var(--border-light);
        }
        
        .table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-light);
        }
        
        .table tbody tr:hover {
            background: var(--gray-50);
        }

        /* === Responsive grid === */
        .grid-responsive {
            display: grid;
            gap: var(--spacing-lg);
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        
        .grid-stats {
            display: grid;
            gap: var(--spacing-lg);
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        /* === Icon helpers === */
        .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: var(--radius-lg);
            font-size: 1.25rem;
        }
        
        .icon-primary { background: rgba(30, 61, 89, 0.1); color: var(--primary); }
        .icon-secondary { background: rgba(23, 162, 184, 0.1); color: var(--secondary); }
        .icon-success { background: rgba(40, 167, 69, 0.1); color: var(--success); }
        .icon-warning { background: rgba(255, 193, 7, 0.1); color: #B45309; }
        .icon-danger { background: rgba(220, 53, 69, 0.1); color: var(--danger); }

        /* === Layout helpers === */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-md);
        }
        
        .section-padding {
            padding: var(--spacing-2xl) 0;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        /* === Small-screen tweaks === */
        @media (max-width: 768px) {
            h1 { font-size: 2rem; }
            h2 { font-size: 1.75rem; }
            h3 { font-size: 1.5rem; }
            
            .btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
            }
            
            .card-body, .card-header, .card-footer {
                padding: var(--spacing-md);
            }
            
            .container {
                padding: 0 var(--spacing-sm);
            }
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3D59',
                        secondary: '#17A2B8'
                    }
                }
            }
        }
    </script>
</head>
<?php
// Resolve the current path and active role so we can decide when to show the admin sidebar
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($basePath && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath));
    if ($currentPath === '') { $currentPath = '/'; }
}
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
// Role mapping: 1 = employee, 2 = awareness manager, 3 = administrator
$roleId = (int)($_SESSION['role_id'] ?? 0);

// Show the sidebar for every /admin route and on /dashboard for privileged roles
$showSidebar = str_starts_with($currentPath, '/admin') || ($currentPath === '/dashboard' && in_array($roleId, [2,3], true));
?>
<?php if ($showSidebar): ?>
<body class="bg-gray-50 min-h-screen<?= $isRtlLayout ? ' rtl' : '' ?>">
    <div class="flex h-screen overflow-hidden">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Hide the marketing header/footer for admin surfaces -->
            <main class="flex-1 overflow-y-auto p-6">
                <?= $content ?>
            </main>
        </div>
    </div>
</body>
<?php else: ?>
<body class="bg-gray-50<?= $isRtlLayout ? ' rtl' : '' ?>">
    <?php $headerContext = 'public'; require __DIR__ . '/header.php'; ?>
    <main>
        <?= $content ?>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script>
        // Activate the sticky header effect on public pages only
        (function(){
          const header = document.getElementById('appHeader');
          const onScroll = () => { if (!header) return; if (window.scrollY > 10) header.classList.add('app-header--scrolled'); else header.classList.remove('app-header--scrolled'); };
          document.addEventListener('scroll', onScroll, { passive: true });
          onScroll();
        })();
    </script>
</body>
<?php endif; ?>
</html>

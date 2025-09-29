<?php

$pageTitle = $content['title'] ?? 'عرض المحتوى';
$currentPage = 'content';
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة درع - نحو وعي سيبراني أفضل</title>
    <meta name="description" content="منصة درع الإلكترونية الشاملة لتعزيز الثقافة الأمنية السيبرانية بين الموظفين من خلال محتوى توعوي جذاب وبسيط">
    
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
                        'cairo': ['Cairo', 'sans-serif'],
                        'noto': ['Noto Kufi Arabic', 'sans-serif']
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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Noto+Kufi+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    
    <style>
        :root {
            --primary: #1E3D59;
            --secondary: #17A2B8;
            --accent: #FF6B35;
            --light:rgb(61, 133, 206);
            --dark: #212529;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.6;
            scroll-behavior: smooth;
        }
        
        .logo-text {
            font-family: 'Noto Kufi Arabic', sans-serif;
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
        
        /* تحسينات للجوال */
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
        
        /* تحسينات إمكانية الوصول */
        a:focus, button:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        /* تحسينات الأداء */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* أنيميشن للعناصر عند الظهور */
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
    </style>
</head>
<body class="bg-gray-50">


  <section class="hero-bg min-h-screen flex items-center pt-16">
    <div class="container mx-auto px-4 hero-content">
      <div class="max-w-3xl fade-in">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight text-glow">نحو وعي سيبراني أفضل</h1>
        <p class="text-lg md:text-xl text-gray-100/95 mb-10 text-glow">منصة درع الإلكترونية الشاملة لتعزيز الثقافة الأمنية السيبرانية من خلال محتوى توعوي جذاب وبسيط</p>
        <div class="flex gap-3 flex-wrap">
          <a href="<?= $basePath ?>/content" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 shadow transition-all hover:shadow-md">تصفح المحتوى</a>
          <a href="<?= $basePath ?>/exams" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary shadow-sm transition-all">ابدأ الاختبارات</a>
        </div>
      </div>
    </div>
  </section>

  <section id="content" class="py-20 bg-white">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12 fade-in">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">المحتوى التوعوي</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">اكتشف أحدث المواد المنشورة لتعزيز الوعي السيبراني والحماية من المخاطر الإلكترونية</p>
      </div>

      <?php if (!empty($contents)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($contents as $index => $c): ?>
            <div class="content-card bg-white rounded-xl p-6 fade-in delay-<?= $index % 3 ?>">
              <div class="icon-box w-14 h-14 flex items-center justify-center bg-blue-100 rounded-xl mb-5">
                <i class="ri-shield-check-line text-primary text-2xl"></i>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3"><?= htmlspecialchars($c['title']) ?></h3>
              <p class="text-gray-600 mb-4"><?= htmlspecialchars(substr($c['description'] ?? 'محتوى توعوي مهم حول الأمن السيبراني', 0, 100)) ?>...</p>
              <div class="text-sm text-gray-500 mb-4 flex items-center">
                <i class="ri-time-line ml-1"></i>
                <span>نشر في: <?= htmlspecialchars(date('Y-m-d', strtotime($c['created_at'] ?? 'now'))) ?></span>
              </div>
              <a href="<?= $basePath ?>/content/view/<?= (int)$c['id'] ?>" class="text-primary font-semibold hover:underline flex items-center transition-all group">
                اقرأ الآن
                <i class="ri-arrow-left-line mr-1 transition-transform group-hover:-translate-x-1"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center text-gray-600 py-10 fade-in">
          <i class="ri-inbox-line text-4xl text-gray-400 mb-3"></i>
          <p>لا توجد مواد منشورة بعد.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Quick Security Tips -->
  <section id="quick-tips" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12 fade-in">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">نصائح أمنية سريعة</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">اتبع هذه الممارسات البسيطة لتعزيز أمانك الرقمي بشكل يومي</p>
      </div>

      <?php 
        $tips = [
          [
            'icon' => 'ri-key-2-line',
            'title' => 'استخدم كلمات مرور قوية',
            'text' => 'اجعل كلمة المرور طويلة ومعقّدة وتجنب إعادة استخدامها في أكثر من موقع.'
          ],
          [
            'icon' => 'ri-shield-check-line',
            'title' => 'فعّل التحقق الثنائي (2FA)',
            'text' => 'إضافة طبقة حماية إضافية تقلل من خطر اختراق الحساب حتى لو انكشفت كلمة المرور.'
          ],
          [
            'icon' => 'ri-mail-line',
            'title' => 'تحقق من رسائل البريد',
            'text' => 'احذر من الروابط والمرفقات المجهولة، وتحقق من عنوان المُرسل بعناية.'
          ],
          [
            'icon' => 'ri-lock-line',
            'title' => 'حدّث أجهزتك باستمرار',
            'text' => 'التحديثات تسد الثغرات الأمنية وتحسن الحماية ضد الهجمات المعروفة.'
          ],
          [
            'icon' => 'ri-wifi-line',
            'title' => 'تجنب الشبكات العامة',
            'text' => 'لا تدخل بيانات حساسة على شبكات واي فاي عامة بدون استخدام VPN موثوق.'
          ],
          [
            'icon' => 'ri-smartphone-line',
            'title' => 'قفل الشاشة والبصمة',
            'text' => 'استخدم قفل الشاشة والبصمة على هاتفك لتقليل الوصول غير المصرح به.'
          ],
        ];
      ?>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($tips as $i => $tip): ?>
          <div class="content-card bg-white rounded-xl p-6 fade-in <?= $i % 3 === 1 ? 'delay-1' : ($i % 3 === 2 ? 'delay-2' : '') ?>">
            <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-xl mb-4">
              <i class="<?= htmlspecialchars($tip['icon']) ?> text-primary text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($tip['title']) ?></h3>
            <p class="text-gray-600 text-sm"><?= htmlspecialchars($tip['text']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="text-center mt-10">
        <a href="<?= $basePath ?>/content" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
          تعلّم المزيد من خلال المحتوى التوعوي
          <i class="ri-arrow-left-line"></i>
        </a>
      </div>
    </div>
  </section>

  <section id="stats" class="py-16 bg-primary text-white">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div class="fade-in">
          <div class="text-3xl md:text-4xl font-bold mb-2">500+</div>
          <div class="text-gray-200">مادة توعوية</div>
        </div>
        <div class="fade-in delay-1">
          <div class="text-3xl md:text-4xl font-bold mb-2">25+</div>
          <div class="text-gray-200">حملة توعوية</div>
        </div>
        <div class="fade-in delay-2">
          <div class="text-3xl md:text-4xl font-bold mb-2">10,000+</div>
          <div class="text-gray-200">مستفيد</div>
        </div>
        <div class="fade-in delay-3">
          <div class="text-3xl md:text-4xl font-bold mb-2">98%</div>
          <div class="text-gray-200">رضى المستخدمين</div>
        </div>
      </div>
    </div>
  </section>

  

  <script>
    // تبديل حالة الهيدر عند التمرير لتحسين الوضوح خارج قسم البطل
    (function(){
      const header = document.getElementById('appHeader');
      const onScroll = () => {
        if (window.scrollY > 10) {
          header.classList.add('app-header--scrolled');
          header.classList.add('bg-white');
          header.classList.remove('bg-transparent');
        } else {
          header.classList.remove('app-header--scrolled');
          header.classList.remove('bg-white');
          header.classList.add('bg-transparent');
        }
      };
      
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
      
      // إضافة أنيميشن للعناصر عند التمرير
      const fadeElements = document.querySelectorAll('.fade-in');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = 1;
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold: 0.1 });
      
      fadeElements.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
      });
    })();
  </script>
</body>
</html>
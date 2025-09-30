<?php
$pageTitle = __('home.meta.title');
$metaDescription = __('home.meta.description');
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="hero-bg min-h-screen flex items-center pt-16">
  <div class="container mx-auto px-4 hero-content">
    <div class="max-w-3xl fade-in">
      <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight text-glow"><?= __('home.hero.title') ?></h1>
      <p class="text-lg md:text-xl text-gray-100/95 mb-10 text-glow"><?= __('home.hero.subtitle') ?></p>
      <div class="flex gap-3 flex-wrap">
        <a href="<?= $basePath ?>/content" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 shadow transition-all hover:shadow-md">
          <?= __('home.hero.browse_button') ?>
        </a>
        <a href="<?= $basePath ?>/exams" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary shadow-sm transition-all">
          <?= __('home.hero.assessment_button') ?>
        </a>
      </div>
    </div>
  </div>
</section>

<section id="content" class="py-20 bg-white">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12 fade-in">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"><?= __('home.content.title') ?></h2>
      <p class="text-gray-600 max-w-2xl mx-auto"><?= __('home.content.subtitle') ?></p>
    </div>

    <?php if (!empty($contents)): ?>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($contents as $index => $c): ?>
          <div class="content-card bg-white rounded-xl p-6 fade-in delay-<?= $index % 3 ?>">
            <div class="icon-box w-14 h-14 flex items-center justify-center bg-blue-100 rounded-xl mb-5">
              <i class="ri-shield-check-line text-primary text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3"><?= htmlspecialchars($c['title']) ?></h3>
            <p class="text-gray-600 mb-4"><?= htmlspecialchars(substr($c['description'] ?? __('home.content.fallback_description'), 0, 100)) ?>...</p>
            <div class="text-sm text-gray-500 mb-4 flex items-center">
              <i class="ri-time-line <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
              <span><?= __('home.content.published', ['date' => htmlspecialchars(date('Y-m-d', strtotime($c['created_at'] ?? 'now')))]) ?></span>
            </div>
            <a href="<?= $basePath ?>/content/view/<?= (int)$c['id'] ?>" class="text-primary font-semibold hover:underline flex items-center transition-all group">
              <?= __('home.content.read_now') ?>
              <i class="ri-arrow-<?= $isRtl ? 'left' : 'right' ?>-line <?= $isRtl ? 'mr-1' : 'ml-1' ?> transition-transform group-hover:translate-<?= $isRtl ? '-x' : 'x' ?>-1"></i>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center text-gray-600 py-10 fade-in">
        <i class="ri-inbox-line text-4xl text-gray-400 mb-3"></i>
        <p><?= __('home.content.empty') ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<section id="quick-tips" class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12 fade-in">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"><?= __('home.tips.title') ?></h2>
      <p class="text-gray-600 max-w-2xl mx-auto"><?= __('home.tips.subtitle') ?></p>
    </div>

    <?php
    $tips = [
        ['icon' => 'ri-key-2-line', 'title' => __('home.tips.items.strong_passwords.title'), 'text' => __('home.tips.items.strong_passwords.text')],
        ['icon' => 'ri-shield-check-line', 'title' => __('home.tips.items.two_factor.title'), 'text' => __('home.tips.items.two_factor.text')],
        ['icon' => 'ri-mail-line', 'title' => __('home.tips.items.suspicious_emails.title'), 'text' => __('home.tips.items.suspicious_emails.text')],
        ['icon' => 'ri-lock-line', 'title' => __('home.tips.items.software_updates.title'), 'text' => __('home.tips.items.software_updates.text')],
        ['icon' => 'ri-wifi-line', 'title' => __('home.tips.items.untrusted_wifi.title'), 'text' => __('home.tips.items.untrusted_wifi.text')],
        ['icon' => 'ri-smartphone-line', 'title' => __('home.tips.items.device_lock.title'), 'text' => __('home.tips.items.device_lock.text')],
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
        <?= __('home.tips.cta') ?>
        <i class="ri-arrow-<?= $isRtl ? 'left' : 'right' ?>-line"></i>
      </a>
    </div>
  </div>
</section>

<section id="stats" class="py-16 bg-primary text-white">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
      <div class="fade-in">
        <div class="text-3xl md:text-4xl font-bold mb-2">500+</div>
        <div class="text-gray-200"><?= __('home.stats.resources') ?></div>
      </div>
      <div class="fade-in delay-1">
        <div class="text-3xl md:text-4xl font-bold mb-2">25+</div>
        <div class="text-gray-200"><?= __('home.stats.campaigns') ?></div>
      </div>
      <div class="fade-in delay-2">
        <div class="text-3xl md:text-4xl font-bold mb-2">10,000+</div>
        <div class="text-gray-200"><?= __('home.stats.learners') ?></div>
      </div>
      <div class="fade-in delay-3">
        <div class="text-3xl md:text-4xl font-bold mb-2">98%</div>
        <div class="text-gray-200"><?= __('home.stats.satisfaction') ?></div>
      </div>
    </div>
  </div>
</section>

<script>
  (function(){
    const header = document.getElementById('appHeader');
    const onScroll = () => {
      if (!header) return;
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

<?php
// app/views/employee/certificates.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-4">
        <a href="<?= $basePath ?>/dashboard" class="text-gray-500 hover:text-primary">
          <i class="ri-arrow-right-line text-xl"></i>
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">الشهادات والشارات</h1>
      </div>
      <p class="text-gray-600">اعرض إنجازاتك وشهاداتك في مجال الأمن السيبراني</p>
    </div>

    <!-- إحصائيات الإنجازات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5 border border-yellow-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
            <i class="ri-award-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $totalCertificates ?? 5 ?></div>
        <div class="text-sm text-gray-600">شهادات مكتسبة</div>
      </div>
      
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center">
            <i class="ri-medal-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $totalBadges ?? 12 ?></div>
        <div class="text-sm text-gray-600">شارات محققة</div>
      </div>
      
      <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center">
            <i class="ri-trophy-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $completionRate ?? 85 ?>%</div>
        <div class="text-sm text-gray-600">معدل الإنجاز</div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-purple-500 text-white flex items-center justify-center">
            <i class="ri-star-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $totalPoints ?? 1250 ?></div>
        <div class="text-sm text-gray-600">إجمالي النقاط</div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- الشهادات -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">شهاداتي</h2>
            <div class="flex items-center gap-2">
              <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                <option value="">جميع الشهادات</option>
                <option value="completed">مكتملة</option>
                <option value="in_progress">قيد التقدم</option>
              </select>
            </div>
          </div>

          <?php if (!empty($certificates)): ?>
          <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($certificates as $cert): ?>
            <div class="relative bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200 hover:shadow-lg transition-shadow">
              <!-- شريط الحالة -->
              <div class="absolute top-4 left-4">
                <?php if ($cert['status'] === 'completed'): ?>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">مكتملة</span>
                <?php else: ?>
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">قيد التقدم</span>
                <?php endif; ?>
              </div>

              <div class="text-center mt-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                  <i class="ri-award-line text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2"><?= htmlspecialchars($cert['title'] ?? '') ?></h3>
                <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($cert['description'] ?? '') ?></p>
                
                <?php if ($cert['status'] === 'completed'): ?>
                <div class="text-sm text-gray-500 mb-4">
                  تاريخ الإنجاز: <?= date('Y/m/d', strtotime($cert['completed_at'] ?? 'now')) ?>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm">
                    <i class="ri-download-line ml-1"></i>
                    تحميل الشهادة
                  </button>
                  <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <i class="ri-share-line"></i>
                  </button>
                </div>
                <?php else: ?>
                <div class="mb-4">
                  <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>التقدم</span>
                    <span><?= (int)($cert['progress'] ?? 0) ?>%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: <?= (int)($cert['progress'] ?? 0) ?>%"></div>
                  </div>
                </div>
                <button class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm">
                  متابعة التقدم
                </button>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
              <i class="ri-award-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد شهادات بعد</h3>
            <p class="text-gray-600 mb-6">ابدأ في إكمال الاختبارات والدورات للحصول على شهاداتك الأولى</p>
            <a href="<?= $basePath ?>/exams" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
              <i class="ri-play-circle-line"></i>
              ابدأ الآن
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- الشارات -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">الشارات</h2>
            <span class="text-sm text-gray-500"><?= count($badges ?? []) ?> شارة</span>
          </div>

          <?php if (!empty($badges)): ?>
          <div class="grid grid-cols-3 gap-4">
            <?php foreach ($badges as $badge): ?>
            <div class="text-center group cursor-pointer" title="<?= htmlspecialchars($badge['description'] ?? '') ?>">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full <?= $badge['earned'] ? 'bg-gradient-to-br from-yellow-400 to-orange-500' : 'bg-gray-200' ?> flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="<?= htmlspecialchars($badge['icon'] ?? 'ri-medal-line') ?> text-xl <?= $badge['earned'] ? 'text-white' : 'text-gray-400' ?>"></i>
              </div>
              <div class="text-xs font-medium <?= $badge['earned'] ? 'text-gray-900' : 'text-gray-400' ?> truncate">
                <?= htmlspecialchars($badge['name'] ?? '') ?>
              </div>
              <?php if ($badge['earned']): ?>
              <div class="text-xs text-gray-500 mt-1">
                <?= date('Y/m/d', strtotime($badge['earned_at'] ?? 'now')) ?>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
              <i class="ri-medal-line text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm text-gray-600">لا توجد شارات محققة بعد</p>
          </div>
          <?php endif; ?>

          <!-- تقدم الشارات -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">الشارات القادمة</h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                  <i class="ri-fire-line text-sm text-gray-400"></i>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">متحمس التعلم</div>
                  <div class="text-xs text-gray-500">أكمل 10 دروس متتالية</div>
                  <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                    <div class="bg-primary h-1 rounded-full" style="width: 70%"></div>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                  <i class="ri-shield-check-line text-sm text-gray-400"></i>
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium text-gray-900">خبير الأمان</div>
                  <div class="text-xs text-gray-500">اجتز 5 اختبارات بدرجة ممتازة</div>
                  <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                    <div class="bg-primary h-1 rounded-full" style="width: 40%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- الإنجازات الأخيرة -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mt-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-6">الإنجازات الأخيرة</h2>
      <div class="space-y-4">
        <?php 
        $recentAchievements = [
          ['type' => 'certificate', 'title' => 'شهادة أساسيات الأمن السيبراني', 'date' => '2024/01/15', 'points' => 100],
          ['type' => 'badge', 'title' => 'شارة المبتدئ المتميز', 'date' => '2024/01/12', 'points' => 50],
          ['type' => 'exam', 'title' => 'اختبار التصيد الإلكتروني - درجة ممتازة', 'date' => '2024/01/10', 'points' => 75],
        ];
        foreach ($recentAchievements as $achievement): ?>
        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
          <div class="w-12 h-12 rounded-full <?= $achievement['type'] === 'certificate' ? 'bg-blue-100 text-blue-600' : ($achievement['type'] === 'badge' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600') ?> flex items-center justify-center">
            <i class="<?= $achievement['type'] === 'certificate' ? 'ri-award-line' : ($achievement['type'] === 'badge' ? 'ri-medal-line' : 'ri-trophy-line') ?> text-lg"></i>
          </div>
          <div class="flex-1">
            <div class="font-medium text-gray-900"><?= htmlspecialchars($achievement['title']) ?></div>
            <div class="text-sm text-gray-500"><?= $achievement['date'] ?> • +<?= $achievement['points'] ?> نقطة</div>
          </div>
          <button class="text-primary hover:underline text-sm">عرض</button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحميل الشهادة
    document.querySelectorAll('[data-action="download"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const certificateId = this.dataset.id;
            window.open(`<?= $basePath ?>/certificates/download/${certificateId}`, '_blank');
        });
    });

    // مشاركة الشهادة
    document.querySelectorAll('[data-action="share"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const certificateId = this.dataset.id;
            const shareUrl = `${window.location.origin}<?= $basePath ?>/certificates/view/${certificateId}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'شهادتي في الأمن السيبراني',
                    url: shareUrl
                });
            } else {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    alert('تم نسخ رابط الشهادة');
                });
            }
        });
    });

    // تفاعل مع الشارات
    document.querySelectorAll('.badge-item').forEach(badge => {
        badge.addEventListener('click', function() {
            const badgeInfo = this.dataset.info;
            // يمكن إضافة modal لعرض تفاصيل الشارة
        });
    });

    // فلترة الشهادات
    document.querySelector('select').addEventListener('change', function() {
        const filter = this.value;
        const certificates = document.querySelectorAll('.certificate-item');
        
        certificates.forEach(cert => {
            if (!filter || cert.dataset.status === filter) {
                cert.style.display = 'block';
            } else {
                cert.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.certificate-item {
    transition: all 0.3s ease;
}

.certificate-item:hover {
    transform: translateY(-2px);
}

.badge-item {
    transition: all 0.2s ease;
}

.badge-item:hover {
    transform: scale(1.05);
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse 2s infinite;
}
</style>

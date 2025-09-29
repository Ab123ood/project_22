<?php
// app/views/employee/notifications.php
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
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">مركز الإشعارات</h1>
      </div>
      <p class="text-gray-600">تابع جميع الإشعارات والتحديثات المهمة</p>
    </div>

    <!-- إحصائيات الإشعارات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center">
            <i class="ri-notification-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= count($notifications ?? []) ?></div>
        <div class="text-sm text-gray-600">إجمالي الإشعارات</div>
      </div>
      
      <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center">
            <i class="ri-notification-badge-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $unreadCount ?? 0 ?></div>
        <div class="text-sm text-gray-600">غير مقروءة</div>
      </div>
      
      <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center">
            <i class="ri-check-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $readCount ?? 0 ?></div>
        <div class="text-sm text-gray-600">مقروءة</div>
      </div>
      
      <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5 border border-yellow-200">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
            <i class="ri-star-line"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= $importantCount ?? 0 ?></div>
        <div class="text-sm text-gray-600">مهمة</div>
      </div>
    </div>

    <!-- أدوات التحكم -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-4">
          <button id="markAllRead" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm">
            <i class="ri-check-double-line ml-1"></i>
            تمييز الكل كمقروء
          </button>
          <select id="filterType" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            <option value="">جميع الإشعارات</option>
            <option value="unread">غير مقروءة</option>
            <option value="read">مقروءة</option>
            <option value="important">مهمة</option>
          </select>
        </div>
        
        <div class="flex items-center gap-4">
          <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            <option value="">جميع الفئات</option>
            <option value="exam">اختبارات</option>
            <option value="content">محتوى جديد</option>
            <option value="survey">استبيانات</option>
            <option value="achievement">إنجازات</option>
            <option value="system">النظام</option>
          </select>
          <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
            <i class="ri-settings-line"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- قائمة الإشعارات -->
    <div class="bg-white rounded-xl border border-gray-200">
      <?php if (!empty($notifications)): ?>
      <div class="divide-y divide-gray-200">
        <?php foreach ($notifications as $notification): ?>
        <div class="notification-item p-6 hover:bg-gray-50 transition-colors <?= !($notification['is_read'] ?? false) ? 'bg-blue-50/50' : '' ?>" 
             data-id="<?= (int)$notification['id'] ?>"
             data-category="<?= htmlspecialchars($notification['category'] ?? '') ?>"
             data-read="<?= ($notification['is_read'] ?? false) ? 'true' : 'false' ?>"
             data-important="<?= ($notification['is_important'] ?? false) ? 'true' : 'false' ?>">
          
          <div class="flex items-start gap-4">
            <!-- أيقونة الإشعار -->
            <div class="flex-shrink-0">
              <?php 
              $iconClass = 'ri-notification-line';
              $bgColor = 'bg-gray-100 text-gray-600';
              
              switch ($notification['category'] ?? '') {
                case 'exam':
                  $iconClass = 'ri-file-list-line';
                  $bgColor = 'bg-blue-100 text-blue-600';
                  break;
                case 'content':
                  $iconClass = 'ri-play-circle-line';
                  $bgColor = 'bg-green-100 text-green-600';
                  break;
                case 'survey':
                  $iconClass = 'ri-feedback-line';
                  $bgColor = 'bg-purple-100 text-purple-600';
                  break;
                case 'achievement':
                  $iconClass = 'ri-trophy-line';
                  $bgColor = 'bg-yellow-100 text-yellow-600';
                  break;
                case 'system':
                  $iconClass = 'ri-settings-line';
                  $bgColor = 'bg-red-100 text-red-600';
                  break;
              }
              ?>
              <div class="w-12 h-12 rounded-full <?= $bgColor ?> flex items-center justify-center">
                <i class="<?= $iconClass ?> text-lg"></i>
              </div>
            </div>

            <!-- محتوى الإشعار -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-gray-900 <?= !($notification['is_read'] ?? false) ? 'font-bold' : '' ?>">
                  <?= htmlspecialchars($notification['title'] ?? '') ?>
                </h3>
                <div class="flex items-center gap-2 ml-4">
                  <?php if ($notification['is_important'] ?? false): ?>
                  <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                  <?php endif; ?>
                  <?php if (!($notification['is_read'] ?? false)): ?>
                  <span class="w-2 h-2 bg-primary rounded-full"></span>
                  <?php endif; ?>
                  <span class="text-xs text-gray-500 whitespace-nowrap">
                    <?= date('Y/m/d H:i', strtotime($notification['created_at'] ?? 'now')) ?>
                  </span>
                </div>
              </div>
              
              <p class="text-gray-600 mb-3 line-clamp-2">
                <?= htmlspecialchars($notification['message'] ?? '') ?>
              </p>
              
              <!-- أزرار الإجراءات -->
              <div class="flex items-center gap-3">
                <?php if (!($notification['is_read'] ?? false)): ?>
                <button class="mark-read-btn text-primary hover:underline text-sm" data-id="<?= (int)$notification['id'] ?>">
                  تمييز كمقروء
                </button>
                <?php endif; ?>
                
                <?php if (!empty($notification['action_url'])): ?>
                <a href="<?= htmlspecialchars($notification['action_url']) ?>" 
                   class="text-primary hover:underline text-sm">
                  <?= htmlspecialchars($notification['action_text'] ?? 'عرض') ?>
                </a>
                <?php endif; ?>
                
                <button class="delete-notification text-red-600 hover:underline text-sm" data-id="<?= (int)$notification['id'] ?>">
                  حذف
                </button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <!-- التصفح -->
      <?php if (($totalPages ?? 1) > 1): ?>
      <div class="p-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">
            عرض <?= ($currentPage - 1) * $perPage + 1 ?> - <?= min($currentPage * $perPage, $totalNotifications) ?> من <?= $totalNotifications ?> إشعار
          </div>
          <div class="flex items-center gap-2">
            <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
              السابق
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
            <a href="?page=<?= $i ?>" 
               class="px-3 py-2 border rounded-lg text-sm <?= $i === $currentPage ? 'bg-primary text-white border-primary' : 'border-gray-300 hover:bg-gray-50' ?>">
              <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
              التالي
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <?php else: ?>
      <!-- رسالة فارغة -->
      <div class="text-center py-16">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
          <i class="ri-notification-line text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد إشعارات</h3>
        <p class="text-gray-600 mb-6">ستظهر هنا جميع الإشعارات والتحديثات المهمة</p>
        <a href="<?= $basePath ?>/dashboard" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
          <i class="ri-arrow-right-line"></i>
          العودة للوحة التحكم
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تمييز إشعار واحد كمقروء
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            
            fetch(`<?= $basePath ?>/notifications/${notificationId}/mark-read`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = this.closest('.notification-item');
                    item.classList.remove('bg-blue-50/50');
                    item.dataset.read = 'true';
                    this.remove();
                    updateCounts();
                }
            })
            .catch(console.error);
        });
    });

    // تمييز جميع الإشعارات كمقروءة
    document.getElementById('markAllRead').addEventListener('click', function() {
        if (confirm('هل تريد تمييز جميع الإشعارات كمقروءة؟')) {
            fetch(`<?= $basePath ?>/notifications/mark-all-read`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(console.error);
        }
    });

    // حذف إشعار
    document.querySelectorAll('.delete-notification').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل تريد حذف هذا الإشعار؟')) {
                const notificationId = this.dataset.id;
                
                fetch(`<?= $basePath ?>/notifications/${notificationId}/delete`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.notification-item').remove();
                        updateCounts();
                    }
                })
                .catch(console.error);
            }
        });
    });

    // فلترة الإشعارات
    function filterNotifications() {
        const typeFilter = document.getElementById('filterType').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const items = document.querySelectorAll('.notification-item');

        items.forEach(item => {
            let show = true;

            // فلترة حسب النوع
            if (typeFilter === 'unread' && item.dataset.read === 'true') show = false;
            if (typeFilter === 'read' && item.dataset.read === 'false') show = false;
            if (typeFilter === 'important' && item.dataset.important === 'false') show = false;

            // فلترة حسب الفئة
            if (categoryFilter && item.dataset.category !== categoryFilter) show = false;

            item.style.display = show ? 'block' : 'none';
        });
    }

    // تحديث العدادات
    function updateCounts() {
        const items = document.querySelectorAll('.notification-item');
        const unreadItems = document.querySelectorAll('.notification-item[data-read="false"]');
        const readItems = document.querySelectorAll('.notification-item[data-read="true"]');
        const importantItems = document.querySelectorAll('.notification-item[data-important="true"]');

        // تحديث العدادات في الواجهة
        // يمكن إضافة كود لتحديث العدادات هنا
    }

    // Event listeners للفلاتر
    document.getElementById('filterType').addEventListener('change', filterNotifications);
    document.getElementById('categoryFilter').addEventListener('change', filterNotifications);

    // تحديث تلقائي للإشعارات كل دقيقة
    setInterval(() => {
        fetch(`<?= $basePath ?>/notifications/check-new`)
            .then(response => response.json())
            .then(data => {
                if (data.hasNew) {
                    // إظهار تنبيه بوجود إشعارات جديدة
                    const banner = document.createElement('div');
                    banner.className = 'fixed top-4 right-4 bg-primary text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    banner.innerHTML = '<i class="ri-notification-line ml-2"></i>لديك إشعارات جديدة';
                    document.body.appendChild(banner);
                    
                    setTimeout(() => banner.remove(), 5000);
                }
            })
            .catch(console.error);
    }, 60000);
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

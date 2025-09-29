<?php
// app/views/employee/exams.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8">
    
    <!-- Header Section -->
    <div class="mb-8">
      <div class="card card-gradient-primary text-white">
        <div class="card-body">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-3 mb-4">
                <a href="<?= $basePath ?>/dashboard" class="text-white/80 hover:text-white transition-colors">
                  <i class="ri-arrow-right-line text-xl"></i>
                </a>
                <h1 class="text-4xl font-bold">الاختبارات المتاحة</h1>
              </div>
              <p class="text-white/90 text-lg">اختبر معلوماتك في مجال الأمن السيبراني واكسب النقاط</p>
            </div>
            <div class="hidden md:block">
              <div class="icon icon-white text-5xl">
                <i class="ri-question-answer-line"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      <div class="card animate-fade-in">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-primary text-2xl">
              <i class="ri-file-list-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= count($exams ?? []) ?></div>
          <div class="text-base text-gray-500">اختبارات متاحة</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-100">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-success text-2xl">
              <i class="ri-checkbox-circle-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['completed_exams'] ?? 0) ?></div>
          <div class="text-base text-gray-500">اختبارات مكتملة</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-200">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-warning text-2xl">
              <i class="ri-trophy-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['average_score'] ?? 0) ?>%</div>
          <div class="text-base text-gray-500">متوسط النتائج</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-300">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-secondary text-2xl">
              <i class="ri-star-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['earned_points'] ?? 0) ?></div>
          <div class="text-base text-gray-500">نقاط مكتسبة</div>
        </div>
      </div>
    </div>

    <!-- فلاتر محسنة -->
    <div class="card mb-8">
      <div class="card-body">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
          <i class="ri-filter-line text-primary ml-3"></i>
          فلترة الاختبارات
        </h3>
        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
            <div class="relative">
              <input type="text" id="searchInput" placeholder="ابحث عن اختبار..." 
                     class="form-input w-full pl-10 pr-4 py-3">
              <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
            <select id="categoryFilter" class="form-select w-full px-4 py-3">
              <option value="">جميع الفئات</option>
              <option value="phishing">التصيد الإلكتروني</option>
              <option value="passwords">كلمات المرور</option>
              <option value="malware">البرمجيات الخبيثة</option>
              <option value="social">الهندسة الاجتماعية</option>
              <option value="network">أمان الشبكات</option>
              <option value="data">حماية البيانات</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الصعوبة</label>
            <select id="difficultyFilter" class="form-select w-full px-4 py-3">
              <option value="">جميع المستويات</option>
              <option value="beginner">مبتدئ</option>
              <option value="intermediate">متوسط</option>
              <option value="advanced">متقدم</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select id="statusFilter" class="form-select w-full px-4 py-3">
              <option value="">جميع الاختبارات</option>
              <option value="available">متاح</option>
              <option value="completed">مكتمل</option>
              <option value="in_progress">قيد التنفيذ</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- قائمة الاختبارات المحسنة -->
    <div id="examsContainer">
      <?php if (!empty($exams)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($exams as $exam): ?>
            <div class="exam-card card animate-fade-in" 
                 data-category="<?= htmlspecialchars($exam['category'] ?? '') ?>" 
                 data-difficulty="<?= htmlspecialchars($exam['difficulty_level'] ?? '') ?>"
                 data-status="<?= htmlspecialchars($exam['status'] ?? 'available') ?>">
              
              <!-- Header -->
              <div class="card-body">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="badge <?= 
                        ($exam['difficulty_level'] ?? 'beginner') === 'beginner' ? 'badge-success' : 
                        (($exam['difficulty_level'] ?? 'beginner') === 'intermediate' ? 'badge-warning' : 'badge-danger') ?>">
                        <?= 
                          ($exam['difficulty_level'] ?? 'beginner') === 'beginner' ? 'مبتدئ' : 
                          (($exam['difficulty_level'] ?? 'beginner') === 'intermediate' ? 'متوسط' : 'متقدم') 
                        ?>
                      </span>
                      <?php if (($exam['status'] ?? 'available') === 'completed'): ?>
                        <span class="badge badge-primary">
                          <i class="ri-checkbox-circle-line ml-1"></i>
                          مكتمل
                        </span>
                      <?php endif; ?>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($exam['title'] ?? '') ?></h3>
                    <p class="text-base text-gray-600 mb-4 line-clamp-3"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                  </div>
                </div>

                <!-- معلومات الاختبار -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line ml-2"></i>
                    <span><?= (int)($exam['duration'] ?? 30) ?> دقيقة</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-question-line ml-2"></i>
                    <span><?= (int)($exam['questions_count'] ?? 10) ?> سؤال</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-star-line ml-2"></i>
                    <span><?= (int)($exam['points'] ?? 50) ?> نقطة</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-user-line ml-2"></i>
                    <span><?= (int)($exam['attempts'] ?? 0) ?> محاولة</span>
                  </div>
                </div>

                <!-- شريط التقدم (إذا كان الاختبار مكتملاً) -->
                <?php if (($exam['status'] ?? 'available') === 'completed' && isset($exam['last_score'])): ?>
                  <div class="mb-4">
                    <div class="flex items-center justify-between text-sm mb-1">
                      <span class="text-gray-600">آخر نتيجة</span>
                      <span class="font-medium text-gray-900"><?= (int)$exam['last_score'] ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full transition-all duration-300" 
                           style="width: <?= (int)$exam['last_score'] ?>%"></div>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Footer -->
                <?php if (($exam['status'] ?? 'available') === 'completed'): ?>
                  <div class="flex gap-2">
                    <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/results" 
                       class="btn btn-outline flex-1">
                      <i class="ri-bar-chart-line ml-1"></i>
                      عرض النتائج
                    </a>
                    <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/take" 
                       class="btn btn-primary flex-1">
                      <i class="ri-refresh-line ml-1"></i>
                      إعادة الاختبار
                    </a>
                  </div>
                <?php else: ?>
                  <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/take" 
                     class="btn btn-primary w-full">
                    <i class="ri-play-circle-line ml-2"></i>
                    بدء الاختبار
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center py-16">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="ri-file-list-line text-4xl text-gray-400"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد اختبارات متاحة</h3>
          <p class="text-gray-500 mb-6">لم يتم العثور على أي اختبارات في الوقت الحالي</p>
          
          <!-- تشخيص مباشر للمشكلة -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-right max-w-4xl mx-auto">
            <h4 class="font-medium text-yellow-800 mb-2">تشخيص المشكلة:</h4>
            <div class="text-sm text-yellow-700 space-y-2">
              <!-- فحص متغير $exams المرسل من الكنترولر -->
              <p><strong>0. فحص متغير $exams من الكنترولر:</strong></p>
              <p>• نوع المتغير: <?= gettype($exams ?? null) ?></p>
              <p>• هل محدد: <?= isset($exams) ? 'نعم' : 'لا' ?></p>
              <?php if (isset($exams)): ?>
                <p>• عدد العناصر: <?= is_array($exams) ? count($exams) : 'ليس مصفوفة' ?></p>
                <?php if (is_array($exams) && count($exams) > 0): ?>
                  <p>• أول عنصر: <?= htmlspecialchars(json_encode($exams[0], JSON_UNESCAPED_UNICODE)) ?></p>
                <?php elseif (is_array($exams)): ?>
                  <p class="text-red-600">• المصفوفة فارغة رغم نجاح الاستعلام!</p>
                <?php endif; ?>
              <?php endif; ?>
              
              <?php
              try {
                // فحص قاعدة البيانات مباشرة
                echo '<p><strong>1. فحص جدول الاختبارات:</strong></p>';
                $totalExams = Database::query('SELECT COUNT(*) as count FROM exams')->fetch();
                $activeExams = Database::query('SELECT COUNT(*) as count FROM exams WHERE is_active = 1')->fetch();
                echo '<p>• إجمالي الاختبارات: ' . $totalExams['count'] . '</p>';
                echo '<p>• الاختبارات المفعلة: ' . $activeExams['count'] . '</p>';
                
                if ($activeExams['count'] > 0) {
                  echo '<p><strong>2. عينة من الاختبارات المفعلة:</strong></p>';
                  $sampleExams = Database::query('SELECT id, title, is_active, created_at FROM exams WHERE is_active = 1 LIMIT 3')->fetchAll();
                  foreach ($sampleExams as $exam) {
                    echo '<p>• ID: ' . $exam['id'] . ', العنوان: ' . htmlspecialchars($exam['title']) . ', تاريخ الإنشاء: ' . $exam['created_at'] . '</p>';
                  }
                }
                
                echo '<p><strong>3. فحص الجداول المرتبطة:</strong></p>';
                $userProgressCount = Database::query('SELECT COUNT(*) as count FROM user_progress')->fetch();
                $examAttemptsCount = Database::query('SELECT COUNT(*) as count FROM exam_attempts')->fetch();
                $examQuestionsCount = Database::query('SELECT COUNT(*) as count FROM exam_questions')->fetch();
                
                echo '<p>• جدول user_progress: ' . $userProgressCount['count'] . ' سجل</p>';
                echo '<p>• جدول exam_attempts: ' . $examAttemptsCount['count'] . ' سجل</p>';
                echo '<p>• جدول exam_questions: ' . $examQuestionsCount['count'] . ' سجل</p>';
                
                echo '<p><strong>4. اختبار الاستعلام المعقد:</strong></p>';
                $userId = $_SESSION['user_id'] ?? 0;
                echo '<p>• معرف المستخدم الحالي: ' . $userId . '</p>';
                
                // تجربة الاستعلام المعقد
                $complexQuery = Database::query(
                    'SELECT e.id, e.title, e.is_active,
                            COALESCE(up.status, "not_started") as progress_status,
                            COALESCE(up.progress_percentage, 0) as progress_percentage,
                            COUNT(q.id) as questions_count
                     FROM exams e
                     LEFT JOIN user_progress up ON e.id = up.content_id 
                         AND up.user_id = :user_id 
                         AND up.content_type = "exam"
                     LEFT JOIN exam_questions q ON q.exam_id = e.id
                     WHERE e.is_active = 1
                     GROUP BY e.id
                     ORDER BY e.created_at DESC',
                    [':user_id' => $userId]
                )->fetchAll();
                
                echo '<p>• نتائج الاستعلام المعقد: ' . count($complexQuery) . ' اختبار</p>';
                
                if (count($complexQuery) > 0 && (!isset($exams) || !is_array($exams) || count($exams) === 0)) {
                  echo '<p class="text-red-600"><strong>المشكلة محددة:</strong> الاستعلام يعمل لكن البيانات لا تصل للعرض!</p>';
                  echo '<p class="text-green-600"><strong>الحل:</strong> مشكلة في تمرير البيانات من الكنترولر للعرض</p>';
                }
                
              } catch (Exception $e) {
                echo '<p class="text-red-600">خطأ في التشخيص: ' . htmlspecialchars($e->getMessage()) . '</p>';
              }
              ?>
            </div>
          </div>
          
          <a href="<?= $basePath ?>/dashboard" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <i class="ri-arrow-right-line ml-2"></i>
            العودة للوحة التحكم
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
// فلترة الاختبارات
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const difficultyFilter = document.getElementById('difficultyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const examCards = document.querySelectorAll('.exam-card');

    function filterExams() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedDifficulty = difficultyFilter.value;
        const selectedStatus = statusFilter.value;

        examCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            const category = card.dataset.category;
            const difficulty = card.dataset.difficulty;
            const status = card.dataset.status;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesDifficulty = !selectedDifficulty || difficulty === selectedDifficulty;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesCategory && matchesDifficulty && matchesStatus) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease-in';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // إضافة مستمعي الأحداث
    searchInput.addEventListener('input', filterExams);
    categoryFilter.addEventListener('change', filterExams);
    difficultyFilter.addEventListener('change', filterExams);
    statusFilter.addEventListener('change', filterExams);
});

// إضافة CSS للرسوم المتحركة
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
`;
document.head.appendChild(style);

// دالة إلغاء الاختبار
function confirmAbandon(examId) {
    if (confirm('هل أنت متأكد من إلغاء الاختبار الحالي؟ سيتم فقدان جميع الإجابات المحفوظة.')) {
        // إرسال طلب إلغاء الاختبار
        fetch(`<?= $basePath ?>/exams/abandon`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `exam_id=${examId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إعادة تحميل الصفحة لتحديث الحالات
                location.reload();
            } else {
                alert('خطأ في إلغاء الاختبار: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('خطأ في إلغاء الاختبار:', error);
            alert('حدث خطأ أثناء إلغاء الاختبار');
        });
    }
}
</script>

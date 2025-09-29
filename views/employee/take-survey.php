<?php /* المحتوى فقط؛ التخطيط العام يُدرج عبر Controller::render */
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="flex flex-col flex-1 overflow-hidden">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center">
                <a href="<?= $basePath ?>/surveys" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 ml-4">
                    <i class="ri-arrow-right-line text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($survey['title'] ?? 'استبيان') ?></h1>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="flex items-center bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="ri-question-line text-lg ml-1"></i>
                    <span id="total-questions"><?= count($questions ?? []) ?></span> سؤال
                </div>
                <div class="flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="ri-time-line text-lg ml-1"></i>
                    <span id="timer">--:--</span>
                </div>
            </div>
        </div>
    </header>

    <div class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">التقدم</span>
            <span class="text-sm font-medium text-gray-700"><span id="current-question">1</span> من <span id="total-questions-2"><?= count($questions ?? []) ?></span></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            <div id="survey-intro" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-questionnaire-line text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">مرحباً بك</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        يحتوي هذا الاستبيان على <span id="questions-count-1"><?= count($questions ?? []) ?></span> سؤال.
                    </p>
                    <button id="start-btn" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        ابدأ الاستبيان
                    </button>
                </div>
            </div>

            <div id="survey-questions" class="hidden">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6 <?= $index === 0 ? '' : 'hidden' ?>" data-question-index="<?= $index ?>" data-question-id="<?= (int)$q['id'] ?>" data-type="<?= htmlspecialchars($q['type']) ?>">
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($q['text'] ?? '') ?></h3>
                                <p class="text-gray-600 text-sm">السؤال <?= $index + 1 ?> من <?= count($questions) ?></p>
                            </div>

                            <div class="space-y-3">
                                <?php if ($q['type'] === 'single'): ?>
                                    <?php foreach (($q['options'] ?? []) as $opt): ?>
                                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="q_<?= (int)$q['id'] ?>" value="<?= (int)$opt['id'] ?>" class="ml-3">
                                            <span class="text-gray-900"><?= htmlspecialchars($opt['text'] ?? '') ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php elseif ($q['type'] === 'multiple'): ?>
                                    <?php foreach (($q['options'] ?? []) as $opt): ?>
                                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="q_<?= (int)$q['id'] ?>[]" value="<?= (int)$opt['id'] ?>" class="ml-3">
                                            <span class="text-gray-900"><?= htmlspecialchars($opt['text'] ?? '') ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php elseif ($q['type'] === 'likert'): ?>
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <?php foreach (array_values($q['options'] ?? []) as $idx => $opt): ?>
                                            <?php $starNum = $idx + 1; ?>
                                            <label class="w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center rating-circle cursor-pointer hover:scale-105 transition" title="<?= htmlspecialchars($opt['text'] ?? '') ?>">
                                                <input type="radio" name="q_<?= (int)$q['id'] ?>" value="<?= (int)$opt['id'] ?>" data-rating="<?= $starNum ?>" class="hidden rating-input">
                                                <i class="ri-star-line text-lg"></i>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($q['type'] === 'rating'): ?>
                                    <div class="flex items-center space-x-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <div class="w-6 h-6 bg-gray-100 rounded-full border border-gray-300 flex items-center justify-center rating-circle">
                                                <input type="radio" name="q_<?= (int)$q['id'] ?>" value="<?= $i ?>" class="hidden rating-input">
                                                <i class="ri-star-line text-lg"></i>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php elseif ($q['type'] === 'ranking'): ?>
                                    <div id="ranking-container-<?= (int)$q['id'] ?>" class="space-y-3">
                                        <?php foreach (($q['options'] ?? []) as $opt): ?>
                                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex items-center ranking-item" data-option-id="<?= (int)$opt['id'] ?>">
                                                <span class="text-gray-900"><?= htmlspecialchars($opt['text'] ?? '') ?></span>
                                                <input type="hidden" name="q_<?= (int)$q['id'] ?>[]" value="<?= (int)$opt['id'] ?>">
                                                <span class="ml-auto text-gray-600 ranking-number">1</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <textarea name="q_<?= (int)$q['id'] ?>" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" rows="5" placeholder="اكتب إجابتك..."></textarea>
                                <?php endif; ?>
                            </div>

                            <div class="flex justify-between mt-8">
                                <button class="prev-btn px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors <?= $index === 0 ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= $index === 0 ? 'disabled' : '' ?>>
                                    <i class="ri-arrow-right-line ml-1"></i>
                                    السابق
                                </button>
                                <button class="next-btn px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <?= $index === count($questions) - 1 ? 'إنهاء الاستبيان' : 'التالي' ?>
                                    <i class="ri-arrow-left-line mr-1"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">لا توجد أسئلة.</div>
                <?php endif; ?>
            </div>

            <div id="survey-results" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-check-line text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">تم إرسال الاستبيان بنجاح</h2>
                    <p class="text-gray-600 mb-6">شكراً لمشاركتك. سيتم معالجة إجاباتك وإضافتها إلى تحليلات المنصة.</p>
                    <div class="flex justify-center">
                        <a href="<?= $basePath ?>/surveys" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">العودة إلى صفحة الاستبيانات</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const surveyId = <?= (int)($survey['id'] ?? 0) ?>;
    const saved = <?= json_encode($saved ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const totalQuestions = <?= (int)count($questions ?? []) ?>;
    let currentIndex = 0;
    let timerSec = 300; // 5 دقائق
    const timerEl = document.getElementById('timer');
    const pb = document.getElementById('progress-bar');
    const curEl = document.getElementById('current-question');

    // بدء الاستبيان
    document.getElementById('start-btn')?.addEventListener('click', () => {
        document.getElementById('survey-intro')?.classList.add('hidden');
        document.getElementById('survey-questions')?.classList.remove('hidden');
        startTimer();
        updateHeader();
        initializeNewQuestionTypes();
    });

    function startTimer(){
        updateTimer();
        const h = setInterval(()=>{
            timerSec--;
            updateTimer();
            if (timerSec <= 0) { clearInterval(h); finish(); }
        }, 1000);
    }

    function updateTimer(){
        const m = Math.floor(Math.max(0, timerSec) / 60);
        const s = Math.max(0, timerSec) % 60;
        if (timerEl) timerEl.textContent = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
    }

    function updateHeader(){
        if (curEl) curEl.textContent = String(Math.min(currentIndex + 1, totalQuestions));
        if (pb) pb.style.width = totalQuestions > 0 ? `${Math.round(((currentIndex+1)/totalQuestions)*100)}%` : '0%';
    }

    // تهيئة أنواع الأسئلة الجديدة
    function initializeNewQuestionTypes() {
        // تهيئة أسئلة التقييم (Rating/Likert كنجوم)
        document.querySelectorAll('.rating-circle').forEach(circle => {
            circle.addEventListener('click', function() {
                const input = this.querySelector('input[type="radio"]');
                if (input) {
                    input.checked = true;
                    
                    const container = this.closest('[data-question-id]');
                    const questionType = container.getAttribute('data-type');
                    const allCircles = container.querySelectorAll('.rating-circle');
                    const currentIndex = Array.from(allCircles).indexOf(this);
                    
                    // تحديث المظهر البصري للنجوم
                    allCircles.forEach((c, index) => {
                        const star = c.querySelector('i');
                        if (index <= currentIndex) {
                            c.classList.add('bg-yellow-400', 'border-yellow-400', 'text-white');
                            c.classList.remove('bg-gray-100', 'border-gray-300');
                            if (star) {
                                star.classList.remove('ri-star-line');
                                star.classList.add('ri-star-fill');
                            }
                        } else {
                            c.classList.remove('bg-yellow-400', 'border-yellow-400', 'text-white');
                            c.classList.add('bg-gray-100', 'border-gray-300');
                            if (star) {
                                star.classList.remove('ri-star-fill');
                                star.classList.add('ri-star-line');
                            }
                        }
                    });
                    
                    // إطلاق حدث التغيير للحفظ التلقائي
                    const changeEvent = new Event('change', { bubbles: true });
                    input.dispatchEvent(changeEvent);
                }
            });
        });

        // تهيئة أسئلة الترتيب (Ranking) - Drag and Drop
        document.querySelectorAll('[id^="ranking-container-"]').forEach(container => {
            initializeDragAndDrop(container);
        });
    }

    // تهيئة السحب والإفلات للترتيب
    function initializeDragAndDrop(container) {
        let draggedElement = null;

        container.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('ranking-item')) {
                draggedElement = e.target;
                e.target.style.opacity = '0.5';
            }
        });

        container.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('ranking-item')) {
                e.target.style.opacity = '1';
                draggedElement = null;
            }
        });

        container.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedElement && e.target.closest('.ranking-item') && e.target.closest('.ranking-item') !== draggedElement) {
                const targetElement = e.target.closest('.ranking-item');
                const allItems = Array.from(container.querySelectorAll('.ranking-item'));
                const draggedIndex = allItems.indexOf(draggedElement);
                const targetIndex = allItems.indexOf(targetElement);

                if (draggedIndex < targetIndex) {
                    targetElement.parentNode.insertBefore(draggedElement, targetElement.nextSibling);
                } else {
                    targetElement.parentNode.insertBefore(draggedElement, targetElement);
                }

                updateRankingNumbers(container);
                updateRankingInputs(container);
            }
        });

        // جعل العناصر قابلة للسحب
        container.querySelectorAll('.ranking-item').forEach(item => {
            item.draggable = true;
        });
    }

    // تحديث أرقام الترتيب
    function updateRankingNumbers(container) {
        container.querySelectorAll('.ranking-number').forEach((number, index) => {
            number.textContent = index + 1;
        });
    }

    // تحديث قيم الإدخال المخفية للترتيب
    function updateRankingInputs(container) {
        const items = container.querySelectorAll('.ranking-item');
        const hiddenInputs = container.querySelectorAll('input[type="hidden"]');
        
        items.forEach((item, index) => {
            const optionId = item.getAttribute('data-option-id');
            if (hiddenInputs[index]) {
                hiddenInputs[index].value = optionId;
            }
        });
    }

    // تعبئة الإجابات المحفوظة
    Object.keys(saved).forEach(qid => {
        const container = document.querySelector(`[data-question-id="${qid}"]`);
        if (!container) return;
        const data = saved[qid];
        const questionType = container.getAttribute('data-type');
        
        if (data.option_id) {
            if (questionType === 'likert' || questionType === 'rating') {
                const input = container.querySelector(`input[value="${data.option_id}"]`);
                if (input) {
                    input.checked = true;
                    // تطبيق التأثيرات البصرية للتقييم
                    if (questionType === 'rating') {
                        const event = new Event('change');
                        input.dispatchEvent(event);
                    }
                }
            } else {
                const radio = container.querySelector(`input[type="radio"][value="${data.option_id}"]`);
                if (radio) radio.checked = true;
            }
        } else if (data.answer_text) {
            const textarea = container.querySelector('textarea');
            if (textarea) textarea.value = data.answer_text;
        }
    });

    // حفظ فوري عند التغيير - محدث لدعم الأنواع الجديدة
    document.querySelectorAll('[data-question-id]').forEach(container => {
        const qid = container.getAttribute('data-question-id');
        const questionType = container.getAttribute('data-type');
        
        container.addEventListener('change', e => {
            const t = e.target;
            if (!t) return;
            const fd = new FormData();
            fd.append('survey_id', String(surveyId));
            fd.append('question_id', String(qid));
            
            if (t.type === 'radio' || t.type === 'checkbox') {
                if (t.checked) {
                    fd.append('option_id', String(t.value));
                    if (questionType === 'likert') {
                        // أرسل أيضاً القيمة الرقمية لمقياس ليكرت
                        fd.append('rating_value', String(t.dataset.rating || t.value));
                    } else if (questionType === 'rating') {
                        fd.append('rating_value', String(t.value));
                    }
                }
            } else if (t.tagName === 'TEXTAREA') {
                fd.append('answer_text', String(t.value));
            }
            
            fetch(`<?= $basePath ?>/surveys/${surveyId}/save-progress`, { method: 'POST', body: fd }).catch(()=>{});
        });

        // حفظ خاص للترتيب عند السحب والإفلات
        if (questionType === 'ranking') {
            const rankingContainer = container.querySelector('[id^="ranking-container-"]');
            if (rankingContainer) {
                // مراقبة تغييرات الترتيب
                const observer = new MutationObserver(() => {
                    const fd = new FormData();
                    fd.append('survey_id', String(surveyId));
                    fd.append('question_id', String(qid));
                    
                    const items = rankingContainer.querySelectorAll('.ranking-item');
                    const ranking = Array.from(items).map((item, index) => ({
                        option_id: item.getAttribute('data-option-id'),
                        rank: index + 1
                    }));
                    
                    fd.append('ranking_data', JSON.stringify(ranking));
                    fetch(`<?= $basePath ?>/surveys/${surveyId}/save-progress`, { method: 'POST', body: fd }).catch(()=>{});
                });
                
                observer.observe(rankingContainer, { childList: true });
            }
        }
    });

    // تنقل الأسئلة
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const cards = Array.from(document.querySelectorAll('[data-question-index]'));
            if (currentIndex < cards.length - 1) {
                cards[currentIndex].classList.add('hidden');
                currentIndex++;
                cards[currentIndex].classList.remove('hidden');
                updateHeader();
            } else {
                finish();
            }
        });
    });
    
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const cards = Array.from(document.querySelectorAll('[data-question-index]'));
            if (currentIndex > 0) {
                cards[currentIndex].classList.add('hidden');
                currentIndex--;
                cards[currentIndex].classList.remove('hidden');
                updateHeader();
            }
        });
    });

    function finish(){
        const fd = new FormData();
        fd.append('survey_id', String(surveyId));
        fetch(`<?= $basePath ?>/surveys/${surveyId}/submit`, { method: 'POST', body: fd })
            .then(r=>{
                if (!r.ok) {
                    throw new Error(`HTTP ${r.status}: ${r.statusText}`);
                }
                return r.json();
            })
            .then(res=>{
                if (res.success) {
                    document.getElementById('survey-questions')?.classList.add('hidden');
                    document.getElementById('survey-intro')?.classList.add('hidden');
                    document.getElementById('survey-results')?.classList.remove('hidden');
                } else {
                    alert('فشل الإرسال: ' + (res.message || 'خطأ غير معروف'));
                }
            })
            .catch(err=>{
                console.error('Survey submit error:', err);
                alert('تعذر إرسال الاستبيان: ' + err.message);
            });
    }
});
</script>

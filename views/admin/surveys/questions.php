<?php
// app/views/admin/surveys/questions.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="max-w-6xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">أسئلة الاستبيان</h2>
        <p class="text-sm text-gray-600 mt-1">الاستبيان: <span class="font-semibold text-gray-900"><?= htmlspecialchars($survey['title'] ?? '') ?></span></p>
      </div>
      <a href="<?= $basePath ?>/admin/surveys" class="text-sm text-gray-600 hover:text-gray-900">عودة إلى قائمة الاستبيانات</a>
    </div>
  </div>

  <?php if (!empty($flashError ?? '')): ?>
    <div class="mb-6 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
      <?= htmlspecialchars($flashError) ?>
    </div>
  <?php endif; ?>

  <!-- واجهة بناء الأسئلة (نفس نمط صفحة الإنشاء) -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
          <i class="ri-question-line text-blue-600 text-xl"></i>
        </div>
        <h2 class="text-lg font-medium text-gray-900">أسئلة الاستبيان</h2>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" id="addQuestionBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center">
          <i class="ri-add-line text-lg ml-2"></i>
          إضافة سؤال
        </button>
        <button type="button" id="addSectionBtn" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center">
          <i class="ri-layout-row-line text-lg ml-2"></i>
          إضافة قسم
        </button>
        <button type="button" id="saveAllBtn" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors flex items-center">
          <i class="ri-save-3-line text-lg ml-2"></i>
          حفظ جميع الأسئلة
        </button>
      </div>
    </div>

    <div id="questionsBuilder" data-survey-id="<?= (int)($survey['id'] ?? 0) ?>">
      <input type="hidden" id="surveyId" value="<?= (int)($survey['id'] ?? 0) ?>">
      <div id="questionsContainer"></div>
      <div id="noQuestions" class="text-center py-12 text-gray-500">
        <i class="ri-question-mark text-4xl mb-4"></i>
        <p>لم يتم إضافة أي أسئلة بعد</p>
        <p class="text-sm">انقر على "إضافة سؤال" لبدء إنشاء الاستبيان</p>
      </div>
    </div>
  </div>

  <!-- قائمة الأسئلة المحفوظة بالفعل -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">الأسئلة المحفوظة</h3>
    <div id="questionsContainerSaved">
      <?php if (!empty($questions)): ?>
        <?php
          // تجميع الأسئلة حسب عنوان القسم
          $grouped = [];
          foreach ($questions as $q) {
            $sec = trim((string)($q['section_title'] ?? ''));
            $grouped[$sec][] = $q;
          }
          // ترتيب بحيث تظهر الأقسام المسماة ثم بدون قسم في النهاية
          uksort($grouped, function($a,$b){
            if ($a==='' && $b==='') return 0;
            if ($a==='') return 1; // ضع "بدون قسم" في النهاية
            if ($b==='') return -1;
            return strnatcasecmp($a,$b);
          });
        ?>

        <?php foreach ($grouped as $sectionTitle => $items): ?>
          <div class="border border-gray-200 rounded-xl mb-6">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 rounded-t-xl flex items-center justify-between">
              <h4 class="text-base font-semibold text-gray-900">
                <?= $sectionTitle !== '' ? htmlspecialchars($sectionTitle) : 'بدون قسم' ?>
              </h4>
              <span class="text-xs text-gray-500">عدد الأسئلة: <?= count($items) ?></span>
            </div>

            <div class="p-5">
              <?php foreach ($items as $index => $q): ?>
                <div class="border border-gray-200 rounded-xl p-5 mb-4">
                  <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-semibold text-gray-900">السؤال <?= $index+1 ?> • نوع: <span class="font-medium text-gray-800"><?= htmlspecialchars($q['type']) ?></span></h5>
                    <form method="post" action="<?= $basePath ?>/admin/surveys/questions/delete" onsubmit="return confirm('حذف هذا السؤال؟');">
                      <input type="hidden" name="survey_id" value="<?= (int)($survey['id'] ?? 0) ?>">
                      <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                      <button class="text-red-600 hover:text-red-700" title="حذف"><i class="ri-delete-bin-line"></i></button>
                    </form>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">نص السؤال</label>
                    <form method="post" action="<?= $basePath ?>/admin/surveys/questions/update" class="flex items-center gap-3">
                      <input type="hidden" name="survey_id" value="<?= (int)($survey['id'] ?? 0) ?>">
                      <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                      <textarea name="text" rows="3" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($q['text']) ?></textarea>
                      <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">حفظ</button>
                    </form>
                  </div>

                  <?php if ($q['type'] !== 'text'): ?>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">الخيارات</label>
                      <ul class="space-y-2">
                        <?php foreach (($optionsByQ[$q['id']] ?? []) as $op): ?>
                          <li class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                            <form method="post" action="<?= $basePath ?>/admin/surveys/options/update" class="flex items-center gap-2 flex-1">
                              <input type="hidden" name="survey_id" value="<?= (int)($survey['id'] ?? 0) ?>">
                              <input type="hidden" name="option_id" value="<?= (int)$op['id'] ?>">
                              <input type="text" name="text" value="<?= htmlspecialchars($op['text']) ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                              <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs">حفظ</button>
                            </form>
                            <form method="post" action="<?= $basePath ?>/admin/surveys/options/delete" onsubmit="return confirm('حذف هذا الخيار؟');">
                              <input type="hidden" name="survey_id" value="<?= (int)($survey['id'] ?? 0) ?>">
                              <input type="hidden" name="option_id" value="<?= (int)$op['id'] ?>">
                              <button class="px-3 py-1.5 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg text-xs">حذف</button>
                            </form>
                          </li>
                        <?php endforeach; ?>
                      </ul>

                      <form method="post" action="<?= $basePath ?>/admin/surveys/options" class="mt-3 flex items-end gap-3">
                        <input type="hidden" name="survey_id" value="<?= (int)($survey['id'] ?? 0) ?>">
                        <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                        <div class="flex-1">
                          <label class="block text-xs font-medium text-gray-700 mb-1">خيار جديد</label>
                          <input type="text" name="option_text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="نص الخيار">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">إضافة خيار</button>
                      </form>
                    </div>
                  <?php else: ?>
                    <p class="text-xs text-gray-500">سؤال بإجابة نصية.</p>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-sm text-gray-500">لا توجد أسئلة محفوظة بعد لهذا الاستبيان.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // واجهة بناء الأسئلة (من صفحة الإنشاء) + حفظ إلى قاعدة البيانات
  let blockCount = 0; // الأقسام
  let questionCount = 0;

  document.addEventListener('DOMContentLoaded', function() {
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const addSectionBtn = document.getElementById('addSectionBtn');
    const saveAllBtn = document.getElementById('saveAllBtn');
    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestions = document.getElementById('noQuestions');
    const builder = document.getElementById('questionsBuilder');
    const surveyIdHidden = document.getElementById('surveyId');

    addQuestionBtn?.addEventListener('click', function() { window.addQuestion(); });
    addSectionBtn?.addEventListener('click', function() { addSection(); });
    saveAllBtn?.addEventListener('click', saveAll);

    function addSection() {
      blockCount++;
      const sectionHTML = `
        <div class="survey-section border border-gray-200 rounded-xl p-4 mb-6 bg-gray-50" data-section="${blockCount}">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <i class="ri-layout-row-line text-gray-500 ml-2"></i>
              <h3 class="text-md font-medium text-gray-900">قسم ${blockCount}</h3>
            </div>
            <div class="flex items-center gap-2">
              <button type="button" class="text-gray-600 hover:text-gray-800 px-3 py-1 text-sm border rounded-lg" onclick="moveSection(${blockCount}, -1)">فوق</button>
              <button type="button" class="text-gray-600 hover:text-gray-800 px-3 py-1 text-sm border rounded-lg" onclick="moveSection(${blockCount}, 1)">تحت</button>
              <button type="button" class="text-red-600 hover:text-red-700 p-1" onclick="removeSection(${blockCount})">
                <i class="ri-delete-bin-line"></i>
              </button>
            </div>
          </div>
          <label class="block text-sm font-medium text-gray-700 mb-2">عنوان القسم (اختياري)</label>
          <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4 section-title" value="قسم ${blockCount}" placeholder="مثال: أسئلة عامة">
          <div class="section-questions space-y-4"></div>
          <div class="mt-4">
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" onclick="addQuestion(${blockCount})">
              إضافة سؤال داخل هذا القسم
            </button>
          </div>
        </div>`;
      questionsContainer.insertAdjacentHTML('beforeend', sectionHTML);
      noQuestions.style.display = 'none';
    }

    window.removeSection = function(sectionNum) {
      const el = document.querySelector(`[data-section="${sectionNum}"]`);
      if (el) {
        el.remove();
        if (questionsContainer.children.length === 0) noQuestions.style.display = 'block';
      }
    }

    window.moveSection = function(sectionNum, dir) {
      const el = document.querySelector(`[data-section="${sectionNum}"]`);
      if (!el) return;
      if (dir < 0 && el.previousElementSibling) {
        el.parentNode.insertBefore(el, el.previousElementSibling);
      } else if (dir > 0 && el.nextElementSibling) {
        el.parentNode.insertBefore(el.nextElementSibling, el);
      }
    }

    window.addQuestion = function(sectionNum = null) {
      questionCount++;
      const container = sectionNum ? document.querySelector(`[data-section="${sectionNum}"] .section-questions`) : questionsContainer;
      const questionHTML = `
        <div class="question-item border border-gray-200 rounded-lg p-4 mb-4 bg-white" data-question="${questionCount}">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded">س${questionCount}</span>
              <h3 class="text-md font-medium text-gray-900">سؤال</h3>
            </div>
            <div class="flex items-center gap-2">
              <button type="button" class="text-gray-600 hover:text-gray-800 px-3 py-1 text-sm border rounded-lg" onclick="moveQuestion(${questionCount}, -1)">فوق</button>
              <button type="button" class="text-gray-600 hover:text-gray-800 px-3 py-1 text-sm border rounded-lg" onclick="moveQuestion(${questionCount}, 1)">تحت</button>
              <button type="button" class="text-red-600 hover:text-red-700 p-1" onclick="removeQuestion(${questionCount})">
                <i class="ri-delete-bin-line"></i>
              </button>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">نص السؤال</label>
              <textarea class="q-text w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="اكتب السؤال هنا..."></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">نوع السؤال</label>
              <select class="q-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="updateAnswerOptions(${questionCount})">
                <option value="multiple">اختيار من متعدد</option>
                <option value="likert">مقياس ليكرت (1-5)</option>
                <option value="yesno">نعم / لا</option>
                <option value="short">نص قصير</option>
                <option value="long">نص طويل</option>
              </select>
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">إجابات السؤال</label>
            <div class="answer-options" id="answers-${questionCount}">${multipleChoiceTemplate(questionCount)}</div>
            <div class="flex items-center gap-2 mt-3">
              <button type="button" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded" onclick="addChoice(${questionCount})">إضافة خيار</button>
              <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" class="ml-2 q-required w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"> سؤال إلزامي
              </label>
            </div>
          </div>
        </div>`;
      container.insertAdjacentHTML('beforeend', questionHTML);
      noQuestions.style.display = 'none';
    }

    window.removeQuestion = function(questionNum) {
      const el = document.querySelector(`[data-question="${questionNum}"]`);
      if (el) {
        el.remove();
        if (!questionsContainer.querySelector('.question-item') && !questionsContainer.querySelector('.survey-section')) {
          noQuestions.style.display = 'block';
        }
      }
    }

    window.moveQuestion = function(questionNum, dir) {
      const el = document.querySelector(`[data-question="${questionNum}"]`);
      if (!el) return;
      if (dir < 0 && el.previousElementSibling) {
        el.parentNode.insertBefore(el, el.previousElementSibling);
      } else if (dir > 0 && el.nextElementSibling) {
        el.parentNode.insertBefore(el.nextElementSibling, el);
      }
    }

    window.updateAnswerOptions = function(questionNum) {
      const qType = document.querySelector(`[data-question="${questionNum}"] .q-type`).value;
      const answersContainer = document.getElementById(`answers-${questionNum}`);
      if (qType === 'multiple') {
        answersContainer.innerHTML = multipleChoiceTemplate(questionNum);
      } else if (qType === 'likert') {
        answersContainer.innerHTML = likertTemplate();
      } else if (qType === 'yesno') {
        answersContainer.innerHTML = yesNoTemplate(questionNum);
      } else if (qType === 'short') {
        answersContainer.innerHTML = shortTextTemplate();
      } else if (qType === 'long') {
        answersContainer.innerHTML = longTextTemplate();
      }
    }

    window.addChoice = function(questionNum) {
      const container = document.querySelector(`#answers-${questionNum} .choices`);
      if (!container) return;
      const index = container.children.length + 1;
      const choice = document.createElement('div');
      choice.className = 'flex items-center gap-2';
      choice.innerHTML = `
        <input type="radio" name="correct-${questionNum}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
        <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="الخيار ${index}">
        <button type="button" class="text-red-600 hover:text-red-700" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>`;
      container.appendChild(choice);
    }

    // قوالب الإجابات
    function multipleChoiceTemplate(num){
      return `
        <div class="space-y-2 choices">
          <div class="flex items-center gap-2">
            <input type="radio" name="correct-${num}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="الخيار 1">
          </div>
          <div class="flex items-center gap-2">
            <input type="radio" name="correct-${num}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="الخيار 2">
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">يمكنك تحديد الإجابة الصحيحة أو تركها فارغة إذا كان السؤال غير مُقيّم بالنقاط.</p>`;
    }

    function likertTemplate(){
      return `
        <div class="grid grid-cols-5 gap-2 text-center">
          <div class="p-2 bg-gray-50 border rounded">1<br><span class="text-xs text-gray-500">أعارض بشدة</span></div>
          <div class="p-2 bg-gray-50 border rounded">2</div>
          <div class="p-2 bg-gray-50 border rounded">3<br><span class="text-xs text-gray-500">محايد</span></div>
          <div class="p-2 bg-gray-50 border rounded">4</div>
          <div class="p-2 bg-gray-50 border rounded">5<br><span class="text-xs text-gray-500">أوافق بشدة</span></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">سيختار المجيب قيمة من 1 إلى 5.</p>`;
    }

    function yesNoTemplate(num){
      return `
        <div class="flex items-center gap-4">
          <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio" name="yn-${num}"> نعم</label>
          <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio" name="yn-${num}"> لا</label>
        </div>`;
    }

    function shortTextTemplate(){
      return `<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="إجابة قصيرة">`;
    }

    function longTextTemplate(){
      return `<textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="إجابة طويلة"></textarea>`;
    }

    // حفظ كل العناصر المنشأة إلى قاعدة البيانات
    async function saveAll(){
      // ثلاث محاولات للحصول على survey_id
      const fromDataAttr = parseInt(builder?.dataset.surveyId || '0');
      const fromHidden = parseInt(surveyIdHidden?.value || '0');
      const fromUrl = parseInt(new URLSearchParams(location.search).get('survey_id') || '0');
      const surveyId = fromDataAttr || fromHidden || fromUrl || 0;

      if (!surveyId) {
        console.warn('لم يتمكن السكربت من تحديد survey_id', { fromDataAttr, fromHidden, fromUrl });
        alert('survey_id غير صالح');
        return;
      }

      // قائمة كل عمليات الحفظ (جذر + داخل الأقسام) مع الحفاظ على الترتيب
      const payloads = [];

      // 1) جمع جميع الأسئلة مع ترتيبها الحالي في DOM
      const allQuestions = [];
      
      // أسئلة الجذر
      document.querySelectorAll('#questionsContainer > .question-item').forEach((item, idx) => {
        allQuestions.push({ 
          item, 
          sectionTitle: '', 
          domOrder: idx,
          isSection: false 
        });
      });

      // أسئلة داخل الأقسام
      document.querySelectorAll('#questionsContainer > .survey-section').forEach((section, sectionIdx) => {
        const title = (section.querySelector('.section-title')?.value || `قسم ${section.dataset.section || ''}`).trim();
        section.querySelectorAll('.section-questions > .question-item').forEach((item, questionIdx) => {
          allQuestions.push({ 
            item, 
            sectionTitle: title, 
            domOrder: sectionIdx * 1000 + questionIdx, // ضمان ترتيب الأقسام
            isSection: true 
          });
        });
      });

      // ترتيب حسب الظهور في DOM
      allQuestions.sort((a, b) => a.domOrder - b.domOrder);

      if (!allQuestions.length) { 
        alert('لا توجد أسئلة للحفظ'); 
        return; 
      }

      // البدء من عدد الأسئلة المحفوظة بالفعل لضمان ترتيب صحيح
      let orderIndex = <?= (int)count($questions ?? []) ?>;
      const failures = [];

      for (const { item, sectionTitle } of allQuestions){
        const text = (item.querySelector('.q-text')?.value || '').trim();
        let qType = item.querySelector('.q-type')?.value || 'multiple';
        if (!text) { continue; }

        // تحويل الأنواع لتتوافق مع backend: mcq | checkbox | text
        // multiple/yesno/likert -> mcq، short/long -> text
        let backendType = 'mcq';
        if (qType === 'short' || qType === 'long') backendType = 'text';

        const form = new FormData();
        form.append('survey_id', String(surveyId));
        form.append('text', text);
        form.append('type', backendType);
        form.append('order_index', String(orderIndex++)); // ترتيب متسلسل
        form.append('section_title', sectionTitle);

        // بناء الخيارات إن كانت مطلوبة
        const options = [];
        if (qType === 'multiple') {
          item.querySelectorAll('.choices input[type="text"]').forEach(inp => { 
            const v = inp.value.trim(); 
            if (v) options.push(v); 
          });
        } else if (qType === 'yesno') {
          options.push('نعم'); 
          options.push('لا');
        } else if (qType === 'likert') {
          options.push('أعارض بشدة (1)');
          options.push('2');
          options.push('محايد (3)');
          options.push('4');
          options.push('أوافق بشدة (5)');
        }

        if (backendType === 'mcq' && options.length){
          options.forEach(v => form.append('option_text[]', v));
        }

        try {
          const res = await fetch('<?= $basePath ?>/admin/surveys/questions', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: form
          });
          const data = await res.json().catch(()=>({ ok:false, error:'استجابة غير صالحة' }));
          if (!res.ok || !data.ok) {
            failures.push({ text, error: data.error || ('HTTP '+res.status) });
          }
        } catch (e) {
          failures.push({ text, error: e.message || String(e) });
        }
      }

      if (failures.length){
        console.error('أخطاء حفظ الأسئلة:', failures);
        alert('تمت بعض المحاولات لكن فشل حفظ '+failures.length+' سؤال/أسئلة. راجع Console للتفاصيل.');
        return;
      }

      window.location.reload();
    }
  });
</script>

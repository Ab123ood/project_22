<?php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-[#1E3D59]/10 rounded-lg flex items-center justify-center mr-3"><i class="ri-file-add-line text-[#1E3D59] text-xl"></i></div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">إنشاء محتوى</h1>
        <p class="text-sm text-gray-600">أضف مواد التوعية بالمؤسسة</p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/content" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">عودة</a>
  </div>
</div>

<?php if (!empty($_GET['error'])): ?>
  <script>
    (function(){
      try {
        console.group('%cرمز الخطأ','color:#b91c1c;font-weight:bold;');
        console.error('<?= htmlspecialchars($_GET['error']) ?>');
        console.groupEnd();
      } catch(_){}
    })();
  </script>
<?php endif; ?>

<?php if (!empty($errors ?? [])): ?>
  <script>
    (function(){
      try {
        const errs = <?= json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        console.group('%cأخطاء إنشاء المحتوى','color:#b91c1c;font-weight:bold;');
        errs.forEach(e=>console.error(e));
        console.groupEnd();
      } catch(_){}
    })();
  </script>
<?php endif; ?>

<?php if (!empty($devError ?? null)): ?>
  <script>
    (function(){
      try {
        const detail = <?= json_encode($devError, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        console.group('%cتفاصيل تقنية (للتشخيص المحلي فقط)','color:#334155;font-weight:bold;');
        console.info('النوع:', detail.type || '');
        console.info('الرسالة:', detail.message || '');
        if (detail.sqlstate) console.info('SQLSTATE:', detail.sqlstate);
        if (detail.driver) console.info('Driver:', detail.driver);
        console.info('الوقت:', detail.time || '');
        console.groupEnd();
      } catch(_){}
    })();
  </script>
<?php endif; ?>

<form id="contentCreateForm" method="post" action="<?= $basePath ?>/admin/content" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6 max-w-5xl mx-auto">
  <!-- أعمدة مطلوبة من الكنترولر/الجدول -->
  <input type="hidden" name="type" id="typeHidden" value="article">
  <input type="hidden" name="body" id="bodyHidden" value="<?= htmlspecialchars($old['body'] ?? '') ?>">
  <input type="hidden" name="media_url" id="mediaUrlHidden" value="<?= htmlspecialchars($old['media_url'] ?? '') ?>">

  <!-- معلومات أساسية -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المحتوى</label>
      <input type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="مثال: أهم ممارسات أمان كلمة المرور" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
      <select name="category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" aria-label="اختيار الفئة">
        <option value="">اختر الفئة</option>
        <?php if (!empty($categories ?? [])): ?>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (isset($old['category_id']) && (int)$old['category_id']===(int)$cat['id'])?'selected':''; ?>>
              <?= htmlspecialchars($cat['name'] ?? ('#'.(int)$cat['id'])) ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="1">الحماية الأساسية</option>
          <option value="2">أمان البريد الإلكتروني</option>
          <option value="3">حماية الأجهزة المحمولة</option>
          <option value="4">إدارة كلمات المرور</option>
          <option value="5">أمان الشبكات</option>
          <option value="6">التخزين السحابي</option>
        <?php endif; ?>
      </select>
    </div>
  </div>

  <!-- نوع المحتوى -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحتوى</label>
    <div class="flex flex-wrap gap-3 mb-2" role="tablist" aria-label="نوع المحتوى">
      <button type="button" id="btnText" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">محتوى نصي</button>
      <button type="button" id="btnVideo" class="px-4 py-2 text-sm rounded-lg text-white" style="background:#1E3D59;" aria-selected="true">محتوى فيديو</button>
      
    </div>
    <p id="typeHelp" class="text-xs text-gray-500">اختر النوع المناسب ليتم عرض الحقول ذات الصلة فقط.</p>

    <!-- القسم النصي -->
    <div id="textSection" class="hidden mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى النصي</label>
      <textarea id="textBody" rows="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="اكتب المحتوى هنا... (يدعم النصوص الطويلة)"></textarea>
      <p class="text-xs text-gray-500 mt-1">سيتم الحفظ في الحقل `body` ضمن جدول المحتوى.</p>
    </div>

    <!-- القسم المرئي (فيديو/دليل/إنفوجرافيك) -->
    <div id="mediaSection" class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">رابط الوسائط (media_url)</label>
      <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex flex-col gap-3 max-w-xl">
          <input type="url" id="videoUrl" placeholder="أدخل رابط الوسائط: YouTube, Vimeo, أو ملف خارجي" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
          <div class="text-xs text-gray-500">مثال: https://www.youtube.com/watch?v=xxxx — سيتم الحفظ في `media_url`.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- الوصف والإعدادات -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">وصف المحتوى</label>
    <textarea id="descTextarea" name="description" rows="4" maxlength="500" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="اكتب وصفًا مختصرًا للمحتوى...">&ZeroWidthSpace;<?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    <div class="text-xs text-gray-500 mt-1"><span id="descCount">0</span>/500 حرف</div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الصعوبة</label>
      <?php $ps = $old['difficulty_level'] ?? ''; ?>
      <select name="difficulty_level" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
        <option value="">اختر المستوى</option>
        <option value="beginner" <?= $ps==='beginner'?'selected':''; ?>>مبتدئ</option>
        <option value="intermediate" <?= $ps==='intermediate'?'selected':''; ?>>متوسط</option>
        <option value="advanced" <?= $ps==='advanced'?'selected':''; ?>>متقدم</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">وقت القراءة/المشاهدة المتوقع (بالدقائق)</label>
      <input type="number" name="est_duration" value="<?= htmlspecialchars((string)($old['est_duration'] ?? 0)) ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" min="0" placeholder="مثال: 5">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">نقاط المكافأة</label>
      <input type="number" name="reward_points" value="<?= htmlspecialchars((string)($old['reward_points'] ?? 0)) ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" min="0">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">حالة النشر</label>
      <?php $ps = $old['publish_status'] ?? 'draft'; ?>
      <select name="publish_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
        <option value="draft" <?= $ps==='draft'?'selected':''; ?>>مسودة</option>
        <option value="published" <?= $ps==='published'?'selected':''; ?>>منشور</option>
        <option value="archived" <?= $ps==='archived'?'selected':''; ?>>مؤرشف</option>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">رابط الصورة المصغرة (thumbnail_url)</label>
      <input type="url" name="thumbnail_url" value="<?= htmlspecialchars($old['thumbnail_url'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="https://...">
    </div>
  </div>

  <div class="flex items-center gap-3">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
      <input type="checkbox" name="is_featured" value="1" <?= !empty($old['is_featured']) ? 'checked' : '' ?> class="w-4 h-4">
      محتوى مميز
    </label>
  </div>

  <div class="flex items-center justify-end gap-3 mt-6">
    <a href="<?= $basePath ?>/admin/content" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">إلغاء</a>
    <button type="submit" class="bg-[#1E3D59] text-white px-6 py-2 rounded hover:opacity-90">نشر المحتوى</button>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const desc = document.getElementById('descTextarea');
    const descCount = document.getElementById('descCount');
    const btnText = document.getElementById('btnText');
    const btnVideo = document.getElementById('btnVideo');
    const btnGuide = document.getElementById('btnGuide');
    const btnInfographic = document.getElementById('btnInfographic');

    const textSection = document.getElementById('textSection');
    const mediaSection = document.getElementById('mediaSection');

    const typeHidden = document.getElementById('typeHidden');
    const bodyHidden = document.getElementById('bodyHidden');
    const mediaUrlHidden = document.getElementById('mediaUrlHidden');

    const textBody = document.getElementById('textBody');
    const videoUrl = document.getElementById('videoUrl');

    const updateCount = () => { descCount.textContent = (desc.value || '').length; };
    desc?.addEventListener('input', updateCount); updateCount();

    function setSelected(btn){
      [btnText, btnVideo, btnGuide, btnInfographic].forEach(b=>{
        if (!b) return;
        const active = (b === btn);
      });
    }

    function selectType(type){
      typeHidden.value = type;
      // زر تفعيل بصري
      [btnText, btnVideo, btnGuide, btnInfographic].forEach(b=>{
        if (!b) return;
        b.classList.remove('text-white');
        b.style.background = '';
        b.classList.add('border','border-gray-300','text-gray-700');
        b.setAttribute('aria-selected','false');
      });
      let activeBtn = btnVideo;
      if (type === 'article') activeBtn = btnText;
      if (type === 'guide') activeBtn = btnGuide;
      if (type === 'infographic') activeBtn = btnInfographic;
      activeBtn.classList.remove('border','border-gray-300','text-gray-700');
      activeBtn.classList.add('text-white');
      activeBtn.style.background = '#1E3D59';
      activeBtn.setAttribute('aria-selected','true');

      // إظهار/إخفاء الحقول
      const isArticle = type === 'article';
      textSection.classList.toggle('hidden', !isArticle);
      mediaSection.classList.toggle('hidden', isArticle);
    }

    btnText?.addEventListener('click', ()=>selectType('article'));
    btnVideo?.addEventListener('click', ()=>selectType('video'));
    btnGuide?.addEventListener('click', ()=>selectType('guide'));
    btnInfographic?.addEventListener('click', ()=>selectType('infographic'));
    // افتراضي: فيديو
    selectType('video');

    // عند الإرسال طبّق القيم الصحيحة
    document.getElementById('contentCreateForm')?.addEventListener('submit', function(e){
      const type = typeHidden.value;
      if (type === 'article'){
        mediaUrlHidden.value = '';
        bodyHidden.value = (textBody.value || '').trim();
        if (!bodyHidden.value){
          alert('يرجى كتابة المحتوى النصي.');
          e.preventDefault();
          return;
        }
      } else {
        bodyHidden.value = '';
        mediaUrlHidden.value = (videoUrl.value || '').trim();
        if (!mediaUrlHidden.value){
          alert('يرجى إدراج رابط الوسائط المناسب لهذا النوع.');
          e.preventDefault();
          return;
        }
      }
    });
  });
</script>

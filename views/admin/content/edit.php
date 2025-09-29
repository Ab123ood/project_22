<?php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-[#1E3D59]/10 rounded-lg flex items-center justify-center mr-3"><i class="ri-edit-2-line text-[#1E3D59] text-xl"></i></div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">تعديل محتوى</h1>
        <p class="text-sm text-gray-600">ID: <?= (int)($item['id'] ?? 0) ?></p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/content" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">عودة</a>
  </div>
</div>

<form id="contentEditForm" method="post" action="<?= $basePath ?>/admin/content/update" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6 max-w-6xl mx-auto">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <input type="hidden" id="typeHidden" name="type" value="<?= htmlspecialchars($item['type'] ?? 'article') ?>">

  <!-- العنوان والفئة -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
      <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="مثال: أهم ممارسات أمان كلمة المرور" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
      <select name="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" aria-label="اختيار الفئة">
        <option value="">اختر الفئة</option>
        <?php if (!empty($categories ?? [])): ?>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= ((int)($item['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name'] ?? ('#'.(int)$cat['id'])) ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </div>
  </div>

  <!-- نوع المحتوى -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحتوى</label>
    <div class="flex flex-wrap gap-3 mb-2" role="tablist" aria-label="نوع المحتوى">
      <?php $t = $item['type'] ?? 'article'; ?>
      <button type="button" id="btnText" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">محتوى نصي</button>
      <button type="button" id="btnVideo" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">محتوى فيديو</button>
    </div>
    <p id="typeHelp" class="text-xs text-gray-500">اختر النوع المناسب ليتم عرض الحقول ذات الصلة فقط.</p>

    <!-- القسم النصي -->
    <div id="textSection" class="mt-4 hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى النصي</label>
      <textarea id="textBody" name="body_textarea" rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="اكتب المحتوى هنا... (يدعم النصوص الطويلة)"><?= htmlspecialchars($item['body'] ?? '') ?></textarea>
      <p class="text-xs text-gray-500 mt-1">سيتم الحفظ في الحقل <code>body</code> ضمن جدول المحتوى.</p>
    </div>

    <!-- القسم المرئي (فيديو) -->
    <div id="mediaSection" class="mt-4 hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">رابط الوسائط (media_url)</label>
      <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex flex-col gap-3 max-w-xl">
          <input type="url" id="mediaUrlInput" placeholder="أدخل رابط الوسائط: YouTube, Vimeo, أو ملف خارجي" value="<?= htmlspecialchars($item['media_url'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
          <div class="text-xs text-gray-500">مثال: https://www.youtube.com/watch?v=xxxx — سيتم الحفظ في <code>media_url</code>.</div>
        </div>
      </div>
    </div>

    <!-- الحقول الفعلية التي يعتمد عليها الحفظ -->
    <input type="hidden" name="body" id="bodyHidden" value="<?= htmlspecialchars($item['body'] ?? '') ?>">
    <input type="hidden" name="media_url" id="mediaUrlHidden" value="<?= htmlspecialchars($item['media_url'] ?? '') ?>">
  </div>

  <!-- الوصف والإعدادات العامة -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">وصف المحتوى</label>
      <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="وصف مختصر."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">وقت القراءة/المشاهدة المتوقع (بالدقائق)</label>
      <input type="number" name="est_duration" value="<?= (int)($item['est_duration'] ?? 0) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" min="0" placeholder="مثال: 5">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">نقاط المكافأة</label>
      <input type="number" name="reward_points" value="<?= (int)($item['reward_points'] ?? 0) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" min="0">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">حالة النشر</label>
      <?php $ps = $item['publish_status'] ?? 'draft'; ?>
      <select name="publish_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]">
        <option value="draft" <?= $ps==='draft'?'selected':''; ?>>مسودة</option>
        <option value="published" <?= $ps==='published'?'selected':''; ?>>منشور</option>
        <option value="archived" <?= $ps==='archived'?'selected':''; ?>>مؤرشف</option>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">رابط الصورة المصغرة (thumbnail_url)</label>
      <input type="url" name="thumbnail_url" value="<?= htmlspecialchars($item['thumbnail_url'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="https://...">
    </div>
  </div>

  <div class="flex items-center gap-3">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
      <input type="checkbox" name="is_featured" value="1" <?= !empty($item['is_featured']) ? 'checked' : '' ?> class="w-4 h-4">
      محتوى مميز
    </label>
  </div>

  <div class="flex items-center justify-between">
    <a href="<?= $basePath ?>/admin/content" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">إلغاء</a>
    <button type="submit" class="px-6 py-3 bg-[#1E3D59] text-white rounded-lg hover:opacity-90">تحديث</button>
  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const typeHidden = document.getElementById('typeHidden');
    const btnText = document.getElementById('btnText');
    const btnVideo = document.getElementById('btnVideo');

    const textSection = document.getElementById('textSection');
    const mediaSection = document.getElementById('mediaSection');

    const textBody = document.getElementById('textBody');
    const mediaUrlInput = document.getElementById('mediaUrlInput');

    const bodyHidden = document.getElementById('bodyHidden');
    const mediaUrlHidden = document.getElementById('mediaUrlHidden');

    function selectType(type){
      typeHidden.value = type;
      [btnText, btnVideo].forEach(b=>{
        if (!b) return;
        b.classList.remove('text-white');
        b.style.background = '';
        b.classList.add('border','border-gray-300','text-gray-700');
        b.setAttribute('aria-selected','false');
      });
      let activeBtn = btnText;
      if (type === 'video') activeBtn = btnVideo;
      activeBtn.classList.remove('border','border-gray-300','text-gray-700');
      activeBtn.classList.add('text-white');
      activeBtn.style.background = '#1E3D59';
      activeBtn.setAttribute('aria-selected','true');

      const isArticle = (type === 'article');
      textSection.classList.toggle('hidden', !isArticle);
      mediaSection.classList.toggle('hidden', isArticle);
    }

    btnText?.addEventListener('click', ()=>selectType('article'));
    btnVideo?.addEventListener('click', ()=>selectType('video'));

    // تهيئة حسب نوع العنصر الحالي
    selectType((typeHidden.value || 'article'));

    // عند الإرسال: املأ الحقول المخفية بالقيمة الصحيحة
    document.getElementById('contentEditForm')?.addEventListener('submit', function(e){
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
        mediaUrlHidden.value = (mediaUrlInput.value || '').trim();
        if (!mediaUrlHidden.value){
          alert('يرجى إدراج رابط الوسائط المناسب لهذا النوع.');
          e.preventDefault();
          return;
        }
      }
    });
  });
</script>

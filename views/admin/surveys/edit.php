<?php
// app/views/admin/surveys/edit.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="max-w-3xl mx-auto">
  <div class="flex items-center gap-4 mb-6">
    <a href="<?= $basePath ?>/admin/surveys" class="text-gray-600 hover:text-gray-900">
      <i class="ri-arrow-right-line text-xl"></i>
    </a>
    <h2 class="text-xl font-bold text-gray-900">تعديل الاستبيان</h2>
  </div>

  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <?php if (!empty($errors ?? [])): ?>
      <div class="mb-4 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
        <ul class="list-disc mr-6">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= $basePath ?>/admin/surveys/update" class="space-y-6">
      <input type="hidden" name="id" value="<?= (int)($survey['id'] ?? 0) ?>" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاستبيان</label>
          <input type="text" name="title" value="<?= htmlspecialchars($survey['title'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
          <input type="text" name="category" value="<?= htmlspecialchars($survey['category'] ?? 'عام') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
          <?php $st = $survey['status'] ?? 'draft'; ?>
          <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="draft" <?= $st==='draft'?'selected':''; ?>>مسودة</option>
            <option value="published" <?= $st==='published'?'selected':''; ?>>منشور</option>
            <option value="archived" <?= $st==='archived'?'selected':''; ?>>مؤرشف</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
          <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($survey['description'] ?? '') ?></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">إتاحة من</label>
          <input type="date" name="availability_from" value="<?= htmlspecialchars(substr((string)($survey['start_date'] ?? ''),0,10)) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">إتاحة إلى</label>
          <input type="date" name="availability_to" value="<?= htmlspecialchars(substr((string)($survey['end_date'] ?? ''),0,10)) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        </div>
      </div>

      <div class="flex items-center gap-3">
        <?php $isAnon = (int)($survey['is_anonymous'] ?? 0) === 1; ?>
        <label class="flex items-center text-sm text-gray-700">
          <input type="checkbox" name="anonymous" class="ml-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= $isAnon ? 'checked' : '' ?> />
          استبيان مجهول الهوية
        </label>
      </div>

      <div class="flex items-center justify-between">
        <a href="<?= $basePath ?>/admin/surveys" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">إلغاء</a>
        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">حفظ التغييرات</button>
      </div>
    </form>
  </div>
</div>

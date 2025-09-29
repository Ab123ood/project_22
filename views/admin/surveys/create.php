<?php
// app/views/admin/surveys/create.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<!-- Page head -->
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
        <i class="ri-survey-line text-blue-600 text-xl"></i>
      </div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Create a new survey</h1>
        <p class="text-sm text-gray-600">Design your survey and specify his fans</p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/surveys" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">return</a>
  </div>
</div>

<?php if (!empty($errors ?? [])): ?>
  <div class="mb-6 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
    <ul class="list-disc mr-6">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form id="surveyForm" method="post" action="<?= $basePath ?>/admin/surveys" class="max-w-6xl mx-auto">
  <!-- Basic survey information -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center mb-6">
      <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
        <i class="ri-information-line text-blue-600 text-xl"></i>
      </div>
      <h2 class="text-lg font-medium text-gray-900">Basic survey information</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Survey title</label>
        <input type="text" id="surveyTitle" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Example: Awareness of the passwords of passwords. required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Class of the survey</label>
        <?php $cat = $old['category'] ?? ''; ?>
        <select id="surveyCategory" name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="" <?= $cat===''?'selected':''; ?>>Choose category</option>
          <option value="phishing" <?= $cat==='phishing'?'selected':''; ?>>E -hunter</option>
          <option value="passwords" <?= $cat==='passwords'?'selected':''; ?>>Passwords</option>
          <option value="malware" <?= $cat==='malware'?'selected':''; ?>>Malignant software</option>
          <option value="social" <?= $cat==='social'?'selected':''; ?>>Social engineering</option>
          <option value="network" <?= $cat==='network'?'selected':''; ?>>Network safety</option>
          <option value="data" <?= $cat==='data'?'selected':''; ?>>Data protection</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Type of survey (optional)</label>
        <select id="surveyType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="assessment">Consciousness evaluation</option>
          <option value="feedback">Content notes</option>
          <option value="campaign">Measurement of an awareness campaign</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
        <?php $st = $old['status'] ?? 'draft'; ?>
        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="draft" <?= $st==='draft'?'selected':''; ?>>draft</option>
          <option value="published" <?= $st==='published'?'selected':''; ?>>Published</option>
          <option value="archived" <?= $st==='archived'?'selected':''; ?>>Archived</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <?php
        // Date -filling in format datetime-local (T) When there is old
        $af = $old['availabilityFrom'] ?? '';
        $at = $old['availabilityTo'] ?? '';
      ?>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">The beginning of availability (optional)</label>
        <input type="datetime-local" id="availableFrom" name="availability_from" value="<?= htmlspecialchars($af) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">End of availability (optional)</label>
        <input type="datetime-local" id="availableTo" name="availability_to" value="<?= htmlspecialchars($at) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
      </div>
    </div>

    <div class="mt-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">Description of the survey</label>
      <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="A brief description of the goal of the survey ... "><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <div class="mt-6 space-y-4">
      <?php $an = (int)($old['anonymous'] ?? 0); $am = (int)($old['allowMultiple'] ?? 0); ?>
      <div class="flex items-center">
        <input type="checkbox" id="anonymous" name="anonymous" value="1" <?= $an ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="anonymous" class="mr-3 text-sm text-gray-700">Allow anonymous response</label>
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="allowMultiple" name="allow_multiple" value="1" <?= $am ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="allowMultiple" class="mr-3 text-sm text-gray-700">Allow more than one response to each user</label>
      </div>
    </div>
  </div>

  <!-- Caution: Questions Management will be on the next page -->
  <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 text-sm">
    After creating a survey, you will be converted automatically to the "Question Questions" page to add questions and options.
  </div>

  <!-- Save buttons -->
  <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <a href="<?= $basePath ?>/admin/surveys" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">cancellation</a>
    <div class="flex space-x-3 space-x-reverse">
      <button type="button" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">Save as a draft</button>
      <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Create a survey</button>
    </div>
  </div>
</form>

<!-- No script for building questions here anymore -->

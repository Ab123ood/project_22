<?php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
$isAdmin = (session_status() === PHP_SESSION_ACTIVE || @session_start() === null) && (int)($_SESSION['role_id'] ?? 0) === 3;
?>

<!-- Page head -->
<div class="card mb-6">
  <div class="card-body">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="flex items-center">
        <div class="icon icon-primary text-2xl mr-3">
          <i class="ri-file-list-2-line"></i>
        </div>
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Educational content management</h1>
          <p class="text-base text-gray-600">Add and edit awareness content in the platform</p>
        </div>
      </div>
      <a href="<?= $basePath ?>/admin/content/create" class="btn btn-primary">Add new content</a>
    </div>
  </div>
</div>

<!-- Filters tape -->
<form class="card mb-6">
  <div class="card-body">
    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
      <div class="relative flex-1 max-w-xl">
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="ri-search-line"></i></span>
        <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>"
               placeholder="Search content ... "
               class="form-input w-full pr-10 pl-4 py-2">
      </div>

      <input type="text" name="type" value="<?= htmlspecialchars($type ?? '') ?>" placeholder="Type (article/video/pdf)"
             class="form-input w-full md:w-48 px-4 py-2">

      <?php $st = $status ?? ''; ?>
      <select name="publish_status" class="form-select w-full md:w-48 px-4 py-2">
        <option value="" <?= $st===''?'selected':''; ?>>All statuses</option>
        <option value="draft" <?= $st==='draft'?'selected':''; ?>>draft</option>
        <option value="published" <?= $st==='published'?'selected':''; ?>>Published</option>
        <option value="archived" <?= $st==='archived'?'selected':''; ?>>Archived</option>
      </select>

      <button class="btn btn-primary">Filter</button>
    </div>
  </div>
</form>

<!-- Table -->
<div class="card">
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr class="text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">
          <th class="px-6 py-4">Content title</th>
          <th class="px-6 py-4">Format</th>
          <th class="px-6 py-4">Points</th>
          <th class="px-6 py-4">Duration</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200 text-base">
        <?php foreach (($items ?? []) as $it): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center">
                <div class="icon icon-primary text-lg mr-3">
                  <i class="ri-shield-check-line"></i>
                </div>
                <span class="font-semibold text-gray-900"><?= htmlspecialchars($it['title']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($it['type']) ?></td>
            <td class="px-6 py-4 text-gray-600"><?= (int)$it['reward_points'] ?></td>
            <td class="px-6 py-4 text-gray-600"><?= (int)$it['est_duration'] ?> min</td>
            <td class="px-6 py-4">
              <?php
                $statusVal = (string)($it['publish_status'] ?? 'draft');
                $badgeClass = 'badge-secondary';
                if ($statusVal === 'published') $badgeClass = 'badge-success';
                elseif ($statusVal === 'archived') $badgeClass = 'badge-warning';
              ?>
              <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusVal) ?></span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <?php if ($isAdmin): ?>
                  <a class="text-gray-400 hover:text-primary text-xl" title="Edit" href="<?= $basePath ?>/admin/content/edit?id=<?= (int)$it['id'] ?>">
                    <i class="ri-edit-line"></i>
                  </a>
                  <form method="post" action="<?= $basePath ?>/admin/content/delete" class="inline" onsubmit="return confirm('Delete this content?');">
                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                    <button class="text-gray-400 hover:text-red-500 text-xl" title="Delete"><i class="ri-delete-bin-line"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
          <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-lg">No content available</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

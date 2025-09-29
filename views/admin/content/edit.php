<?php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-[#1E3D59]/10 rounded-lg flex items-center justify-center mr-3"><i class="ri-edit-2-line text-[#1E3D59] text-xl"></i></div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Content modification</h1>
        <p class="text-sm text-gray-600">ID: <?= (int)($item['id'] ?? 0) ?></p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/content" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">return</a>
  </div>
</div>

<form id="contentEditForm" method="post" action="<?= $basePath ?>/admin/content/update" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6 max-w-6xl mx-auto">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <input type="hidden" id="typeHidden" name="type" value="<?= htmlspecialchars($item['type'] ?? 'article') ?>">

  <!-- Address and category -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">the address</label>
      <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="Example: The most important password safety practices " required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
      <select name="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" aria-label="Category Choose ">
        <option value="">Choose category</option>
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

  <!-- Content type -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
    <div class="flex flex-wrap gap-3 mb-2" role="tablist" aria-label="The type of content ">
      <?php $t = $item['type'] ?? 'article'; ?>
      <button type="button" id="btnText" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">Text content</button>
      <button type="button" id="btnVideo" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">Video content</button>
    </div>
    <p id="typeHelp" class="text-xs text-gray-500">Choose the right type to display only relevant fields.</p>

    <!-- Text section -->
    <div id="textSection" class="mt-4 hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">Textual content</label>
      <textarea id="textBody" name="body_textarea" rows="8" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="Write the content here ... (Supports long texts) "><?= htmlspecialchars($item['body'] ?? '') ?></textarea>
      <p class="text-xs text-gray-500 mt-1">The field will be saved <code>body</code> Within the content schedule.</p>
    </div>

    <!-- Video section (Video) - -->
    <div id="mediaSection" class="mt-4 hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">Media link (media_url)</label>
      <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex flex-col gap-3 max-w-xl">
          <input type="url" id="mediaUrlInput" placeholder="Enter the media link: YouTube, Vimeo, Or an external file " value="<?= htmlspecialchars($item['media_url'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
          <div class="text-xs text-gray-500">Example: https://www.youtube.com/watch?v=xxxx — It will be saved in <code>media_url</code>.</div>
        </div>
      </div>
    </div>

    <!-- The actual fields on which memorization depends -->
    <input type="hidden" name="body" id="bodyHidden" value="<?= htmlspecialchars($item['body'] ?? '') ?>">
    <input type="hidden" name="media_url" id="mediaUrlHidden" value="<?= htmlspecialchars($item['media_url'] ?? '') ?>">
  </div>

  <!-- General Description and Settings -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Content description</label>
      <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="A brief description. "><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Reading time/expected viewing (minutes)</label>
      <input type="Count" name="est_duration" value="<?= (int)($item['est_duration'] ?? 0) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" min="0" placeholder="Example: 5 ">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">The reward points</label>
      <input type="Count" name="reward_points" value="<?= (int)($item['reward_points'] ?? 0) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" min="0">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Publishing status</label>
      <?php $ps = $item['publish_status'] ?? 'draft'; ?>
      <select name="publish_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]">
        <option value="draft" <?= $ps==='draft'?'selected':''; ?>>draft</option>
        <option value="published" <?= $ps==='published'?'selected':''; ?>>Published</option>
        <option value="archived" <?= $ps==='archived'?'selected':''; ?>>Archived</option>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Mini image link (thumbnail_url)</label>
      <input type="url" name="thumbnail_url" value="<?= htmlspecialchars($item['thumbnail_url'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/30 focus:border-[#1E3D59]" placeholder="https://...">
    </div>
  </div>

  <div class="flex items-center gap-3">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
      <input type="checkbox" name="is_featured" value="1" <?= !empty($item['is_featured']) ? 'checked' : '' ?> class="w-4 h-4">
      Featured content
    </label>
  </div>

  <div class="flex items-center justify-between">
    <a href="<?= $basePath ?>/admin/content" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">cancellation</a>
    <button type="submit" class="px-6 py-3 bg-[#1E3D59] text-white rounded-lg hover:opacity-90">Update</button>
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

    // Preparing by the current type of element
    selectType((typeHidden.value || 'article'));

    // When sending: Fill the hidden fields with the correct value
    document.getElementById('contentEditForm')?.addEventListener('submit', function(e){
      const type = typeHidden.value;
      if (type === 'article'){
        mediaUrlHidden.value = '';
        bodyHidden.value = (textBody.value || '').trim();
        if (!bodyHidden.value){
          alert('Please write text content.');
          e.preventDefault();
          return;
        }
      } else {
        bodyHidden.value = '';
        mediaUrlHidden.value = (mediaUrlInput.value || '').trim();
        if (!mediaUrlHidden.value){
          alert('Please include the appropriate media link for this type.');
          e.preventDefault();
          return;
        }
      }
    });
  });
</script>

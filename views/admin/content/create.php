<?php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-[#1E3D59]/10 rounded-lg flex items-center justify-center mr-3"><i class="ri-file-add-line text-[#1E3D59] text-xl"></i></div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Create content</h1>
        <p class="text-sm text-gray-600">Add awareness materials in the institution</p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/content" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">return</a>
  </div>
</div>

<?php if (!empty($_GET['error'])): ?>
  <script>
    (function(){
      try {
        console.group('%cError symbol','color:#b91c1c;font-weight:bold;');
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
        console.group('%cContent creation errors','color:#b91c1c;font-weight:bold;');
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
        console.group('%cTechnical details (for local diagnosis only)','color:#334155;font-weight:bold;');
        console.info('Type:', detail.type || '');
        console.info('message:', detail.message || '');
        if (detail.sqlstate) console.info('SQLSTATE:', detail.sqlstate);
        if (detail.driver) console.info('Driver:', detail.driver);
        console.info('the time:', detail.time || '');
        console.groupEnd();
      } catch(_){}
    })();
  </script>
<?php endif; ?>

<form id="contentCreateForm" method="post" action="<?= $basePath ?>/admin/content" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6 max-w-5xl mx-auto">
  <!-- Required columns of control/table -->
  <input type="hidden" name="type" id="typeHidden" value="article">
  <input type="hidden" name="body" id="bodyHidden" value="<?= htmlspecialchars($old['body'] ?? '') ?>">
  <input type="hidden" name="media_url" id="mediaUrlHidden" value="<?= htmlspecialchars($old['media_url'] ?? '') ?>">

  <!-- Basic information -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Content title</label>
      <input type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="Example: The most important password safety practices " required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
      <select name="category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" aria-label="Category Choose ">
        <option value="">Choose category</option>
        <?php if (!empty($categories ?? [])): ?>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (isset($old['category_id']) && (int)$old['category_id']===(int)$cat['id'])?'selected':''; ?>>
              <?= htmlspecialchars($cat['name'] ?? ('#'.(int)$cat['id'])) ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="1">Basic protection</option>
          <option value="2">Email safety</option>
          <option value="3">Protect mobile devices</option>
          <option value="4">Password management</option>
          <option value="5">Network safety</option>
          <option value="6">Cloud storage</option>
        <?php endif; ?>
      </select>
    </div>
  </div>

  <!-- Content type -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
    <div class="flex flex-wrap gap-3 mb-2" role="tablist" aria-label="The type of content ">
      <button type="button" id="btnText" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100" aria-selected="false">Text content</button>
      <button type="button" id="btnVideo" class="px-4 py-2 text-sm rounded-lg text-white" style="background:#1E3D59;" aria-selected="true">Video content</button>
      
    </div>
    <p id="typeHelp" class="text-xs text-gray-500">Choose the right type to display only relevant fields.</p>

    <!-- Text section -->
    <div id="textSection" class="hidden mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Textual content</label>
      <textarea id="textBody" rows="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="Write the content here ... (Supports long texts) "></textarea>
      <p class="text-xs text-gray-500 mt-1">The field will be saved `body` Within the content schedule.</p>
    </div>

    <!-- Visible section (video/guide/infographic) - - - - - -->
    <div id="mediaSection" class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Media link (media_url)</label>
      <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex flex-col gap-3 max-w-xl">
          <input type="url" id="videoUrl" placeholder="Enter the media link: YouTube, Vimeo, Or an external file " class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
          <div class="text-xs text-gray-500">Example: https://www.youtube.com/watch?v=xxxx — It will be saved in `media_url`.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Description and settings -->
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Content description</label>
    <textarea id="descTextarea" name="description" rows="4" maxlength="500" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="Write a brief description of the content ... ">&ZeroWidthSpace;<?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    <div class="text-xs text-gray-500 mt-1"><span id="descCount">0</span>/500 Letter</div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">The level of difficulty</label>
      <?php $ps = $old['difficulty_level'] ?? ''; ?>
      <select name="difficulty_level" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
        <option value="">Choose the level</option>
        <option value="beginner" <?= $ps==='beginner'?'selected':''; ?>>junior</option>
        <option value="intermediate" <?= $ps==='intermediate'?'selected':''; ?>>Medium</option>
        <option value="advanced" <?= $ps==='advanced'?'selected':''; ?>>advanced</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Reading time/expected viewing (minutes)</label>
      <input type="Count" name="est_duration" value="<?= htmlspecialchars((string)($old['est_duration'] ?? 0)) ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" min="0" placeholder="Example: 5 ">
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">The reward points</label>
      <input type="Count" name="reward_points" value="<?= htmlspecialchars((string)($old['reward_points'] ?? 0)) ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" min="0">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Publishing status</label>
      <?php $ps = $old['publish_status'] ?? 'draft'; ?>
      <select name="publish_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]">
        <option value="draft" <?= $ps==='draft'?'selected':''; ?>>draft</option>
        <option value="published" <?= $ps==='published'?'selected':''; ?>>Published</option>
        <option value="archived" <?= $ps==='archived'?'selected':''; ?>>Archived</option>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Mini image link (thumbnail_url)</label>
      <input type="url" name="thumbnail_url" value="<?= htmlspecialchars($old['thumbnail_url'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1E3D59]/20 focus:border-[#1E3D59]" placeholder="https://...">
    </div>
  </div>

  <div class="flex items-center gap-3">
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
      <input type="checkbox" name="is_featured" value="1" <?= !empty($old['is_featured']) ? 'checked' : '' ?> class="w-4 h-4">
      Featured content
    </label>
  </div>

  <div class="flex items-center justify-end gap-3 mt-6">
    <a href="<?= $basePath ?>/admin/content" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">cancellation</a>
    <button type="submit" class="bg-[#1E3D59] text-white px-6 py-2 rounded hover:opacity-90">Publishing content</button>
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
      // Optical activation button
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

      // Show/hide the fields
      const isArticle = type === 'article';
      textSection.classList.toggle('hidden', !isArticle);
      mediaSection.classList.toggle('hidden', isArticle);
    }

    btnText?.addEventListener('click', ()=>selectType('article'));
    btnVideo?.addEventListener('click', ()=>selectType('video'));
    btnGuide?.addEventListener('click', ()=>selectType('guide'));
    btnInfographic?.addEventListener('click', ()=>selectType('infographic'));
    // Virtual: Video
    selectType('video');

    // When sending, apply the correct values
    document.getElementById('contentCreateForm')?.addEventListener('submit', function(e){
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
        mediaUrlHidden.value = (videoUrl.value || '').trim();
        if (!mediaUrlHidden.value){
          alert('Please include the appropriate media link for this type.');
          e.preventDefault();
          return;
        }
      }
    });
  });
</script>

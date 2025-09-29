<?php
// app/views/employee/content.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-white min-h-screen">
  <div class="container mx-auto px-4 py-8">



    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="card card-gradient-primary animate-fade-in">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <div class="icon icon-primary text-2xl">
              <i class="ri-play-circle-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['total_content'] ?? 0 ?></div>
          <div class="text-base text-gray-600">محتوى متاح</div>
        </div>
      </div>
      <div class="card card-gradient-success animate-fade-in animate-delay-100">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <div class="icon icon-success text-2xl">
              <i class="ri-eye-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['viewed_content'] ?? 0 ?></div>
          <div class="text-base text-gray-600">تم مشاهدته</div>
        </div>
      </div>
      <div class="card card-gradient-warning animate-fade-in animate-delay-200">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <div class="icon icon-warning text-2xl">
              <i class="ri-heart-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['favorited_content'] ?? 0 ?></div>
          <div class="text-base text-gray-600">مفضل</div>        </div>
      </div>
      <div class="card card-gradient-secondary animate-fade-in animate-delay-300">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <div class="icon icon-secondary text-2xl">
              <i class="ri-star-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= $stats['earned_points'] ?? 0 ?></div>
          <div class="text-base text-gray-600">نقاط مكتسبة</div>
        </div>
      </div>
    </div>

    <!-- المحتوى المميز -->
    <?php if (!empty($featuredContent)): ?>
    <div class="mb-10">
      <h3 class="text-2xl font-semibold text-gray-900 mb-6">المحتوى المميز</h3>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($featuredContent as $item): ?>
        <div class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all">
          <div class="relative">
            <img src="<?= htmlspecialchars($item['thumbnail'] ?? '/assets/images/default-content.jpg') ?>"
                 alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-48 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="absolute top-3 right-3">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                <i class="ri-star-line"></i> مميز
              </span>
            </div>
            <div class="absolute bottom-3 left-3">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-black/70 text-white">
                <?= $item['type'] === 'video' ? 'فيديو' : 'مقال' ?>
              </span>
            </div>
          </div>
          <div class="p-6">
            <h4 class="font-bold text-gray-900 mb-2 text-lg line-clamp-1"><?= htmlspecialchars($item['title']) ?></h4>
            <p class="text-gray-600 mb-4 line-clamp-2"><?= htmlspecialchars($item['description'] ?? '') ?></p>
            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-1"><i class="ri-time-line"></i><span><?= ($item['duration'] ?? 5) . ' دقائق' ?></span></div>
                <div class="flex items-center gap-1"><i class="ri-eye-line"></i><span><?= $item['views'] ?? 0 ?></span></div>
              </div>
              <div class="flex items-center gap-1 text-yellow-500"><i class="ri-star-fill"></i><span><?= number_format($item['rating'] ?? 4.5, 1) ?></span></div>
            </div>
            <div class="flex gap-3">
              <a href="<?= $basePath ?>/content/view/<?= (int)$item['id'] ?>"
                 class="flex-1 bg-primary text-white text-center py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">عرض المحتوى</a>
              <button class="px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors" aria-label="إضافة إلى المفضلة">
                <i class="ri-heart-line text-gray-600"></i>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- جميع المحتوى -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-2xl font-semibold text-gray-900">جميع المحتوى</h3>
        <div class="text-sm text-gray-500">
          <span id="pageInfo">صفحة 1</span>
        </div>
      </div>

      <?php if (!empty($content)): ?>
      <div id="contentGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($content as $item): ?>
        <div class="content-item group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all"
             data-category="<?= htmlspecialchars($item['category'] ?? '') ?>"
             data-type="<?= htmlspecialchars($item['type'] ?? '') ?>"
             data-views="<?= (int)($item['views'] ?? 0) ?>"
             data-rating="<?= (float)($item['rating'] ?? 0) ?>"
             data-created="<?= htmlspecialchars($item['created_at'] ?? '') ?>">
          <div class="relative">
            <img src="<?= htmlspecialchars($item['thumbnail'] ?? '/assets/images/default-content.jpg') ?>"
                 alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-48 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="absolute bottom-3 left-3">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-black/70 text-white">
                <?= $item['type'] === 'video' ? 'فيديو' : ($item['type'] === 'article' ? 'مقال' : 'محتوى') ?>
              </span>
            </div>
            <?php if ($item['is_new'] ?? false): ?>
            <div class="absolute top-3 right-3">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">جديد</span>
            </div>
            <?php endif; ?>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
              <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                <?= htmlspecialchars($item['category_name'] ?? 'عام') ?>
              </span>
            </div>
            <h4 class="font-bold text-gray-900 mb-2 text-lg line-clamp-1"><?= htmlspecialchars($item['title']) ?></h4>
            <p class="text-gray-600 mb-4 line-clamp-2"><?= htmlspecialchars($item['description'] ?? '') ?></p>
            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-1"><i class="ri-time-line"></i><span><?= ($item['duration'] ?? 5) . ' دقائق' ?></span></div>
                <div class="flex items-center gap-1"><i class="ri-eye-line"></i><span><?= $item['views'] ?? 0 ?></span></div>
                <div class="flex items-center gap-1 text-yellow-500"><i class="ri-star-fill"></i><span><?= number_format($item['rating'] ?? 4.5, 1) ?></span></div>
              </div>
            </div>
            <div class="flex gap-3">
              <a href="<?= $basePath ?>/content/view/<?= (int)$item['id'] ?>"
                 class="flex-1 bg-primary text-white text-center py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">عرض المحتوى</a>
              <button class="px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors favorite-btn" 
                      data-id="<?= (int)$item['id'] ?>" aria-label="إضافة إلى المفضلة">
                <i class="ri-heart-line text-gray-600"></i>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- لا توجد نتائج بعد الفلترة -->
      <div id="noMatches" class="hidden text-center py-16">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
          <i class="ri-search-eye-line text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">لا توجد نتائج مطابقة</h3>
        <p class="text-lg text-gray-600 mb-6">جرّب تغيير كلمات البحث أو تصفية مختلفة.</p>
        <button id="resetFiltersBtn" class="btn btn-outline">
          <i class="ri-refresh-line"></i>
          إعادة تعيين البحث
        </button>
      </div>

      <!-- ترقيم الصفحات (عميل) -->
      <div id="pager" class="mt-6 flex justify-center gap-2"></div>

      <?php else: ?>
      <!-- رسالة فارغة -->
      <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
          <i class="ri-file-text-line text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-2">لا يوجد محتوى متاح</h3>
        <p class="text-lg text-gray-600 mb-6">لم يتم نشر أي محتوى توعوي حتى الآن. تحقق مرة أخرى لاحقاً.</p>
        <a href="<?= $basePath ?>/dashboard" class="btn btn-primary">
          <i class="ri-arrow-right-line"></i>
          العودة للوحة التحكم
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const categoryFilter = document.getElementById('categoryFilter');
  const typeFilter = document.getElementById('typeFilter');
  const sortBy = document.getElementById('sortBy');
  const clearFilters = document.getElementById('clearFilters');
  const contentGrid = document.getElementById('contentGrid');
  const items = Array.from(document.querySelectorAll('.content-item'));
  const resultsCount = document.getElementById('resultsCount');
  const activeChips = document.getElementById('activeChips');
  const noMatches = document.getElementById('noMatches');
  const resetFiltersBtn = document.getElementById('resetFiltersBtn');
  const pager = document.getElementById('pager');
  const pageInfo = document.getElementById('pageInfo');

  const PAGE_SIZE = 9; // 3 أعمدة * 3 صفوف
  let currentPage = 1;

  function debounce(fn, delay=250){
    let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), delay); };
  }

  function updateChips(){
    activeChips.innerHTML = '';
    const chips = [];
    if (searchInput.value.trim()) chips.push({label: 'بحث', value: searchInput.value.trim()});
    if (categoryFilter.value) chips.push({label: 'فئة', value: categoryFilter.options[categoryFilter.selectedIndex].text});
    if (typeFilter.value) chips.push({label: 'نوع', value: typeFilter.options[typeFilter.selectedIndex].text});
    chips.forEach(ch => {
      const span = document.createElement('span');
      span.className = 'px-2 py-1 rounded-full text-xs bg-primary/10 text-primary border border-primary/20';
      span.textContent = `${ch.label}: ${ch.value}`;
      activeChips.appendChild(span);
    });
  }

  function compareBy(a, b, key){
    if (key === 'popular') return (parseInt(b.dataset.views||'0')) - (parseInt(a.dataset.views||'0'));
    if (key === 'rating') return (parseFloat(b.dataset.rating||'0')) - (parseFloat(a.dataset.rating||'0'));
    // newest (fallback by created or keep order)
    const ta = Date.parse(a.dataset.created || '');
    const tb = Date.parse(b.dataset.created || '');
    if (!isNaN(tb) && !isNaN(ta)) return tb - ta; // desc
    return 0;
  }

  function applySort(list){
    const sort = sortBy?.value || 'newest';
    list.sort((a,b)=>compareBy(a,b,sort));
  }

  function applyFiltersAndRender(){
    const term = (searchInput.value||'').toLowerCase();
    const cat = categoryFilter.value;
    const typ = typeFilter.value;

    // filter
    const filtered = items.filter(el => {
      const title = el.querySelector('h4')?.textContent.toLowerCase() || '';
      const desc = el.querySelector('p')?.textContent.toLowerCase() || '';
      const matchesSearch = !term || title.includes(term) || desc.includes(term);
      const matchesCategory = !cat || el.dataset.category === cat;
      const matchesType = !typ || el.dataset.type === typ;
      return matchesSearch && matchesCategory && matchesType;
    });

    // sort
    applySort(filtered);

    // pagination
    const total = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    const start = (currentPage-1) * PAGE_SIZE;
    const pageSlice = filtered.slice(start, start + PAGE_SIZE);

    // render
    contentGrid?.replaceChildren(...pageSlice);

    // counts and states
    resultsCount.textContent = total ? `عدد النتائج: ${total}` : 'لا توجد نتائج';
    noMatches.classList.toggle('hidden', total !== 0);
    pageInfo.textContent = `صفحة ${currentPage} من ${totalPages}`;

    // pager
    renderPager(totalPages);
    updateChips();
  }

  function renderPager(totalPages){
    pager.innerHTML = '';
    if (totalPages <= 1) return;
    for (let p=1; p<=totalPages; p++){
      const btn = document.createElement('button');
      btn.className = 'px-3 py-1.5 rounded border ' + (p===currentPage ? 'bg-primary text-white border-primary' : 'border-gray-200 hover:bg-gray-50');
      btn.textContent = p;
      btn.addEventListener('click', ()=>{ currentPage = p; applyFiltersAndRender(); window.scrollTo({top:0, behavior:'smooth'}); });
      pager.appendChild(btn);
    }
  }

  // favorite toggle
  document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const icon = this.querySelector('i');
      const contentId = this.dataset.id;
      const isFavorited = icon.classList.contains('ri-heart-fill');
      const url = isFavorited ? `<?= $basePath ?>/content/unfavorite/${contentId}` : `<?= $basePath ?>/content/favorite/${contentId}`;
      fetch(url, { method: 'POST', headers: {'Content-Type': 'application/json'} })
        .then(r=>r.json()).then(data=>{
          if (data.success) {
            icon.classList.toggle('ri-heart-line', isFavorited);
            icon.classList.toggle('ri-heart-fill', !isFavorited);
            icon.classList.toggle('text-red-500', !isFavorited);
            icon.classList.toggle('text-gray-600', isFavorited);
          }
        }).catch(()=>{});
    });
  });

  // events
  const debounced = debounce(()=>{ currentPage=1; applyFiltersAndRender(); }, 250);
  searchInput.addEventListener('input', debounced);
  categoryFilter.addEventListener('change', ()=>{ currentPage=1; applyFiltersAndRender(); });
  typeFilter.addEventListener('change', ()=>{ currentPage=1; applyFiltersAndRender(); });
  sortBy.addEventListener('change', ()=>{ currentPage=1; applyFiltersAndRender(); });
  clearFilters.addEventListener('click', ()=>{
    searchInput.value=''; categoryFilter.value=''; typeFilter.value=''; sortBy.value='newest'; currentPage=1; applyFiltersAndRender();
  });
  resetFiltersBtn?.addEventListener('click', ()=>{ searchInput.value=''; categoryFilter.value=''; typeFilter.value=''; sortBy.value='newest'; currentPage=1; applyFiltersAndRender(); });

  // initial
  applyFiltersAndRender();
});
</script>

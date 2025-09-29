<?php
// app/views/employee/content-view.php
$pageTitle = $content['title'] ?? 'Content display';
$currentPage = 'content';
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="animate-fade-in">
  <div class="container mx-auto px-4 py-8">
    <!-- Page Header Card -->
    <div class="card mb-8">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <a href="<?= $basePath ?>/content" class="btn btn-ghost">
              <i class="ri-arrow-right-line text-xl rtl:rotate-180 mr-1"></i>
              Return to the awareness content list
            </a>
          </div>
          <div class="flex items-center gap-2">
            <button onclick="shareContent()" class="btn btn-ghost">
              <i class="ri-share-line"></i>
              sharing
            </button>
            <button id="likeBtn" onclick="toggleLike(<?= $content['id'] ?>)" class="btn btn-outline">
              <i id="likeIcon" class="ri-heart-line"></i>
              <span id="likeCount"><?= $content['like_count'] ?? 0 ?></span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto">
      <!-- Content Header -->
      <div class="card overflow-hidden mb-8">
        <div class="card-body pb-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <?php if ($content['category_name']): ?>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                    style="background-color: <?= htmlspecialchars($content['category_color'] ?? '#3B82F6') ?>20; color: <?= htmlspecialchars($content['category_color'] ?? '#3B82F6') ?>">
                <?php if ($content['category_icon']): ?>
                <i class="<?= htmlspecialchars($content['category_icon']) ?> mr-1 rtl:mr-1"></i>
                <?php endif; ?>
                <?= htmlspecialchars($content['category_name']) ?>
              </span>
              <?php endif; ?>
              
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <i class="ri-time-line mr-1 rtl:mr-1"></i>
                <?= $content['est_duration'] ?> minute
              </span>
              
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <?= htmlspecialchars($content['difficulty_display']) ?>
              </span>
            </div>
            
            <div class="flex items-center text-sm text-gray-500">
              <i class="ri-eye-line mr-1 rtl:mr-1"></i>
              <span><?= $content['view_count'] ?? 0 ?> to watch</span>
            </div>
          </div>

          <!-- Title -->
          <h1 class="text-3xl font-bold text-gray-900 mb-4">
            <?= htmlspecialchars($content['title']) ?>
          </h1>

          <!-- Description -->
          <?php if ($content['description']): ?>
          <p class="text-lg text-gray-600 mb-6">
            <?= htmlspecialchars($content['description']) ?>
          </p>
          <?php endif; ?>

          <!-- Meta Info -->
          <div class="flex items-center justify-between text-sm text-gray-500 border-t border-gray-200 pt-4">
            <div class="flex items-center gap-4">
              <?php if ($content['author_name']): ?>
              <div class="flex items-center">
                <i class="ri-user-line mr-1 rtl:mr-1"></i>
                <span><?= htmlspecialchars($content['author_name']) ?></span>
              </div>
              <?php endif; ?>
              
              <div class="flex items-center">
                <i class="ri-calendar-line mr-1 rtl:mr-1"></i>
                <span><?= date('Y/m/d', strtotime($content['created_at'])) ?></span>
              </div>
              
              <div class="flex items-center">
                <i class="ri-star-line mr-1 rtl:mr-1"></i>
                <span><?= $content['reward_points'] ?> a point</span>
              </div>
            </div>
            
            <div class="flex items-center">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <i class="ri-file-text-line mr-1 rtl:mr-1"></i>
                <?= htmlspecialchars($content['type_display']) ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Body -->
      <div class="card overflow-hidden mb-8">
        <div class="card-body">
          <!-- Media Content -->
          <?php if ($content['type'] === 'video' && $content['media_url']): ?>
          <div class="mb-6">
            <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
              <?php if (strpos($content['media_url'], 'youtube.com') !== false || strpos($content['media_url'], 'youtu.be') !== false): ?>
                <?php
                // Extract YouTube video ID
                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $content['media_url'], $matches);
                $videoId = $matches[1] ?? '';
                ?>
                <?php if ($videoId): ?>
                <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        class="w-full h-full">
                </iframe>
                <?php endif; ?>
              <?php else: ?>
                <video controls class="w-full h-full">
                  <source src="<?= htmlspecialchars($content['media_url']) ?>" type="video/mp4">
                  Your browser does not support video play.
                </video>
              <?php endif; ?>
            </div>
          </div>
          <?php elseif ($content['type'] === 'infographic' && $content['media_url']): ?>
          <div class="mb-6">
            <img src="<?= htmlspecialchars($content['media_url']) ?>" 
                 alt="<?= htmlspecialchars($content['title']) ?>"
                 class="w-full rounded-lg shadow-sm">
          </div>
          <?php endif; ?>

          <!-- Text Content -->
          <div class="prose prose-lg max-w-none">
            <?= nl2br(htmlspecialchars($content['body'])) ?>
          </div>
        </div>
      </div>

      <!-- Related Content -->
      <?php if (!empty($relatedContent)): ?>
      <div class="card overflow-hidden">
        <div class="card-body">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="ri-links-line mr-2 rtl:mr-2 text-blue-600"></i>
            Confused content
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($relatedContent as $item): ?>
            <a href="<?= $basePath ?>/content/view/<?= $item['id'] ?>" class="group block">
              <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                <div class="flex items-start gap-3">
                  <?php if ($item['thumbnail_url']): ?>
                  <img src="<?= htmlspecialchars($item['thumbnail_url']) ?>" 
                       alt="<?= htmlspecialchars($item['title']) ?>"
                       class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                  <?php else: ?>
                  <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="ri-file-text-line text-2xl text-blue-600"></i>
                  </div>
                  <?php endif; ?>
                  
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                      <?= htmlspecialchars($item['title']) ?>
                    </h3>
                    
                    <div class="flex items-center space-x-2 rtl:space-x-reverse mt-2">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                        <?= htmlspecialchars($item['type_display']) ?>
                      </span>
                      
                      <?php if ($item['category_name']): ?>
                      <span class="text-xs text-gray-500">
                        <?= htmlspecialchars($item['category_name']) ?>
                      </span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Like functionality
async function toggleLike(contentId) {
  try {
    const response = await fetch(`<?= $basePath ?>/content/like/${contentId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
    });
    
    const result = await response.json();
    
    if (result.success) {
      const likeIcon = document.getElementById('likeIcon');
      const likeCount = document.getElementById('likeCount');
      
      if (result.liked) {
        likeIcon.className = 'ri-heart-fill text-xl text-red-500';
      } else {
        likeIcon.className = 'ri-heart-line text-xl';
      }
      
      likeCount.textContent = result.likeCount;
    } else {
      alert(result.message || 'An error occurred');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('A mistake occurred in the contact');
  }
}

// Share functionality
function shareContent() {
  if (navigator.share) {
    navigator.share({
      title: '<?= addslashes($content['title']) ?>',
      text: '<?= addslashes($content['description'] ?? '') ?>',
      url: window.location.href
    });
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href).then(() => {
      // Show toast notification
      showToast('The link was copied to the portfolio');
    });
  }
}

// Toast notification
function showToast(message) {
  const toast = document.createElement('div');
  toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
  toast.textContent = message;
  
  document.body.appendChild(toast);
  
  // Animate in
  setTimeout(() => {
    toast.classList.remove('translate-x-full');
  }, 100);
  
  // Remove after 3 seconds
  setTimeout(() => {
    toast.classList.add('translate-x-full');
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}

// Check if user has already liked this content
document.addEventListener('DOMContentLoaded', function() {
  // Hook for future state hydration
});
</script>

<?php
// app/views/employee/surveys.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8">
    
    <!-- Header Section -->
    <div class="mb-8">
      <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <a href="<?= $basePath ?>/dashboard" class="text-green-100 hover:text-white transition-colors">
                <i class="ri-arrow-right-line text-xl"></i>
              </a>
              <h1 class="text-3xl font-bold">Interactive surveys</h1>
            </div>
            <p class="text-green-100 text-lg">Share your opinion and help us improve security awareness programs</p>
          </div>
          <div class="hidden md:block">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
              <i class="ri-feedback-line text-4xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Improved statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="ri-feedback-line text-blue-600 text-xl"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= (int)($stats['completed_surveys'] ?? 8) ?></div>
        <div class="text-sm text-gray-500">Complete surveys</div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="ri-star-line text-green-600 text-xl"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= Count_format($stats['average_rating'] ?? 4.8, 1) ?></div>
        <div class="text-sm text-gray-500">Average</div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <i class="ri-time-line text-yellow-600 text-xl"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= count($surveys ?? []) ?></div>
        <div class="text-sm text-gray-500">Available surveys</div>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <i class="ri-gift-line text-purple-600 text-xl"></i>
          </div>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1"><?= (int)($stats['earned_points'] ?? 120) ?></div>
        <div class="text-sm text-gray-500">Equally acquired points</div>
      </div>
    </div>

    <!-- Filled filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
      <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
        <i class="ri-filter-line text-green-600 mr-3"></i>
        Filter surveys
      </h3>
      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Research</label>
          <div class="relative">
            <input type="text" id="searchInput" placeholder="I am looking for a survey ... " 
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
          <select id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <option value="">All types</option>
            <option value="feedback">Program evaluation</option>
            <option value="awareness">The level of awareness</option>
            <option value="satisfaction">User satisfaction</option>
            <option value="improvement">Improvement suggestions</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select id="statusFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <option value="">All surveys</option>
            <option value="available">available</option>
            <option value="completed">complete</option>
            <option value="expired">Expired</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Question list - -->
    <div id="surveysContainer">
      <?php if (!empty($surveys)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($surveys as $survey): ?>
            <div class="survey-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-green-200 transform hover:-translate-y-1" 
                 data-type="<?= htmlspecialchars($survey['type'] ?? 'feedback') ?>" 
                 data-status="<?= htmlspecialchars($survey['status'] ?? 'available') ?>">
              
              <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        Program evaluation
                      </span>
                      <?php if (($survey['status'] ?? 'available') === 'completed'): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                          <i class="ri-checkbox-circle-line mr-1"></i>
                          complete
                        </span>
                      <?php endif; ?>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($survey['title'] ?? 'Security awareness survey') ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($survey['description'] ?? 'Help us improve awareness programs by sharing your opinion') ?></p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2"></i>
                    <span><?= (int)($survey['estimated_time'] ?? 5) ?> minutes</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-question-line mr-2"></i>
                    <span><?= (int)($survey['questions_count'] ?? 8) ?> Question</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-star-line mr-2"></i>
                    <span><?= (int)($survey['points'] ?? 20) ?> a point</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-calendar-line mr-2"></i>
                    <span><?= date('Y/m/d', strtotime($survey['deadline'] ?? '+7 days')) ?></span>
                  </div>
                </div>

                <?php if (($survey['status'] ?? 'available') === 'completed'): ?>
                  <div class="flex gap-2">
                    <a href="<?= $basePath ?>/surveys/<?= $survey['id'] ?? 1 ?>/results" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg text-center text-sm font-medium transition-colors">
                      <i class="ri-bar-chart-line mr-1"></i>
                      View results
                    </a>
                    <button class="flex-1 bg-green-100 text-green-700 px-4 py-3 rounded-lg text-center text-sm font-medium cursor-not-allowed">
                      <i class="ri-check-line mr-1"></i>
                      complete
                    </button>
                  </div>
                <?php else: ?>
                  <a href="<?= $basePath ?>/surveys/<?= $survey['id'] ?? 1 ?>/take" 
                     class="w-full bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-6 py-3 rounded-lg text-center font-medium transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                    <i class="ri-edit-line mr-2"></i>
                    The start of the survey
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center py-16">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="ri-feedback-line text-4xl text-gray-400"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">There are no surveys available</h3>
          <p class="text-gray-500 mb-6">No surveys have been found at the present time</p>
          <a href="<?= $basePath ?>/dashboard" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="ri-arrow-right-line mr-2"></i>
            Back to the control panel
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const surveyCards = document.querySelectorAll('.survey-card');

    function filterSurveys() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const selectedStatus = statusFilter.value;

        surveyCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            const type = card.dataset.type;
            const status = card.dataset.status;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesType = !selectedType || type === selectedType;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesType && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterSurveys);
    typeFilter.addEventListener('change', filterSurveys);
    statusFilter.addEventListener('change', filterSurveys);
});
</script>

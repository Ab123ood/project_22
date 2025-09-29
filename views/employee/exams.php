<?php
// app/views/employee/exams.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8">
    
    <!-- Header Section -->
    <div class="mb-8">
      <div class="card card-gradient-primary text-white">
        <div class="card-body">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-3 mb-4">
                <a href="<?= $basePath ?>/dashboard" class="text-white/80 hover:text-white transition-colors">
                  <i class="ri-arrow-right-line text-xl"></i>
                </a>
                <h1 class="text-4xl font-bold">Available assessments</h1>
              </div>
              <p class="text-white/90 text-lg">Assessment your information in the field of cyber security and earn points</p>
            </div>
            <div class="hidden md:block">
              <div class="icon icon-white text-5xl">
                <i class="ri-question-answer-line"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      <div class="card animate-fade-in">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-primary text-2xl">
              <i class="ri-file-list-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= count($exams ?? []) ?></div>
          <div class="text-base text-gray-500">Exams available</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-100">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-success text-2xl">
              <i class="ri-checkbox-circle-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['completed_exams'] ?? 0) ?></div>
          <div class="text-base text-gray-500">Complete assessments</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-200">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-warning text-2xl">
              <i class="ri-trophy-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['average_score'] ?? 0) ?>%</div>
          <div class="text-base text-gray-500">Average results</div>
        </div>
      </div>
      
      <div class="card animate-fade-in animate-delay-300">
        <div class="card-body">
          <div class="flex items-center justify-between mb-4">
            <div class="icon icon-secondary text-2xl">
              <i class="ri-star-line"></i>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900 mb-1"><?= (int)($stats['earned_points'] ?? 0) ?></div>
          <div class="text-base text-gray-500">Equally acquired points</div>
        </div>
      </div>
    </div>

    <!-- Filled filters -->
    <div class="card mb-8">
      <div class="card-body">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
          <i class="ri-filter-line text-primary mr-3"></i>
          Filter assessments
        </h3>
        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Research</label>
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Find a assessment ... " 
                     class="form-input w-full pl-10 pr-4 py-3">
              <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select id="categoryFilter" class="form-select w-full px-4 py-3">
              <option value="">All categories</option>
              <option value="phishing">E -hunter</option>
              <option value="passwords">Passwords</option>
              <option value="malware">Malignant software</option>
              <option value="social">Social engineering</option>
              <option value="network">Network safety</option>
              <option value="data">Data protection</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">The level of difficulty</label>
            <select id="difficultyFilter" class="form-select w-full px-4 py-3">
              <option value="">All levels</option>
              <option value="beginner">junior</option>
              <option value="intermediate">Medium</option>
              <option value="advanced">advanced</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="statusFilter" class="form-select w-full px-4 py-3">
              <option value="">All assessments</option>
              <option value="available">available</option>
              <option value="completed">complete</option>
              <option value="in_progress">Implemented</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- List of improved assessments -->
    <div id="examsContainer">
      <?php if (!empty($exams)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($exams as $exam): ?>
            <div class="exam-card card animate-fade-in" 
                 data-category="<?= htmlspecialchars($exam['category'] ?? '') ?>" 
                 data-difficulty="<?= htmlspecialchars($exam['difficulty_level'] ?? '') ?>"
                 data-status="<?= htmlspecialchars($exam['status'] ?? 'available') ?>">
              
              <!-- Header -->
              <div class="card-body">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="badge <?= 
                        ($exam['difficulty_level'] ?? 'beginner') === 'beginner' ? 'badge-success' : 
                        (($exam['difficulty_level'] ?? 'beginner') === 'intermediate' ? 'badge-warning' : 'badge-danger') ?>">
                        <?= 
                          ($exam['difficulty_level'] ?? 'beginner') === 'beginner' ? 'junior' : 
                          (($exam['difficulty_level'] ?? 'beginner') === 'intermediate' ? 'Medium' : 'advanced') 
                        ?>
                      </span>
                      <?php if (($exam['status'] ?? 'available') === 'completed'): ?>
                        <span class="badge badge-primary">
                          <i class="ri-checkbox-circle-line mr-1"></i>
                          complete
                        </span>
                      <?php endif; ?>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($exam['title'] ?? '') ?></h3>
                    <p class="text-base text-gray-600 mb-4 line-clamp-3"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                  </div>
                </div>

                <!-- Assessment information -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-time-line mr-2"></i>
                    <span><?= (int)($exam['duration'] ?? 30) ?> minute</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-question-line mr-2"></i>
                    <span><?= (int)($exam['questions_count'] ?? 10) ?> Question</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-star-line mr-2"></i>
                    <span><?= (int)($exam['points'] ?? 50) ?> a point</span>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-user-line mr-2"></i>
                    <span><?= (int)($exam['attempts'] ?? 0) ?> attempt</span>
                  </div>
                </div>

                <!-- The progress bar (if the assessment is complete) - -->
                <?php if (($exam['status'] ?? 'available') === 'completed' && isset($exam['last_score'])): ?>
                  <div class="mb-4">
                    <div class="flex items-center justify-between text-sm mb-1">
                      <span class="text-gray-600">Last</span>
                      <span class="font-medium text-gray-900"><?= (int)$exam['last_score'] ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full transition-all duration-300" 
                           style="width: <?= (int)$exam['last_score'] ?>%"></div>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Footer -->
                <?php if (($exam['status'] ?? 'available') === 'completed'): ?>
                  <div class="flex gap-2">
                    <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/results" 
                       class="btn btn-outline flex-1">
                      <i class="ri-bar-chart-line mr-1"></i>
                      View results
                    </a>
                    <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/take" 
                       class="btn btn-primary flex-1">
                      <i class="ri-refresh-line mr-1"></i>
                      Re -assessment
                    </a>
                  </div>
                <?php else: ?>
                  <a href="<?= $basePath ?>/exams/<?= $exam['id'] ?>/take" 
                     class="btn btn-primary w-full">
                    <i class="ri-play-circle-line mr-2"></i>
                    Start the assessment
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center py-16">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="ri-file-list-line text-4xl text-gray-400"></i>
          </div>
          <h3 class="text-xl font-medium text-gray-900 mb-2">There are no assessments available</h3>
          <p class="text-gray-500 mb-6">No assessments have been found at the present time</p>
          
          <!-- Direct diagnosis of the problem -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-right max-w-4xl mx-auto">
            <h4 class="font-medium text-yellow-800 mb-2">Diagnosis of the problem:</h4>
            <div class="text-sm text-yellow-700 space-y-2">
              <!-- Variable $exams The sender from the control -->
              <p><strong>0. Variable $exams From the control:</strong></p>
              <p>• Variable type: <?= gettype($exams ?? null) ?></p>
              <p>• Is it specific: <?= isset($exams) ? 'Yes' : 'no' ?></p>
              <?php if (isset($exams)): ?>
                <p>• Number of elements: <?= is_array($exams) ? count($exams) : 'Not a matrix' ?></p>
                <?php if (is_array($exams) && count($exams) > 0): ?>
                  <p>• The first element: <?= htmlspecialchars(json_encode($exams[0], JSON_UNESCAPED_UNICODE)) ?></p>
                <?php elseif (is_array($exams)): ?>
                  <p class="text-red-600">• The matrix is ​​empty despite the success of the query!</p>
                <?php endif; ?>
              <?php endif; ?>
              
              <?php
              try {
                // Check the database directly
                echo '<p><strong>1. Examination of the assessment schedule:</strong></p>';
                $totalExams = Database::query('SELECT COUNT(*) as count FROM exams')->fetch();
                $activeExams = Database::query('SELECT COUNT(*) as count FROM exams WHERE is_active = 1')->fetch();
                echo '<p>• Total assessments: ' . $totalExams['count'] . '</p>';
                echo '<p>• Pre -assessments: ' . $activeExams['count'] . '</p>';
                
                if ($activeExams['count'] > 0) {
                  echo '<p><strong>2. A sample of activated assessments:</strong></p>';
                  $sampleExams = Database::query('SELECT id, title, is_active, created_at FROM exams WHERE is_active = 1 LIMIT 3')->fetchAll();
                  foreach ($sampleExams as $exam) {
                    echo '<p>• ID: ' . $exam['id'] . ', the address: ' . htmlspecialchars($exam['title']) . ', Construction date: ' . $exam['created_at'] . '</p>';
                  }
                }
                
                echo '<p><strong>3. Associated tables examination:</strong></p>';
                $userProgressCount = Database::query('SELECT COUNT(*) as count FROM user_progress')->fetch();
                $examAttemptsCount = Database::query('SELECT COUNT(*) as count FROM exam_attempts')->fetch();
                $examQuestionsCount = Database::query('SELECT COUNT(*) as count FROM exam_questions')->fetch();
                
                echo '<p>• Table user_progress: ' . $userProgressCount['count'] . ' register</p>';
                echo '<p>• Table exam_attempts: ' . $examAttemptsCount['count'] . ' register</p>';
                echo '<p>• Table exam_questions: ' . $examQuestionsCount['count'] . ' register</p>';
                
                echo '<p><strong>4. Complex query assessment:</strong></p>';
                $userId = $_SESSION['user_id'] ?? 0;
                echo '<p>• Current user ID: ' . $userId . '</p>';
                
                // The complex inquiry experience
                $complexQuery = Database::query(
                    'SELECT e.id, e.title, e.is_active,
                            COALESCE(up.status, "not_started") as progress_status,
                            COALESCE(up.progress_percentage, 0) as progress_percentage,
                            COUNT(q.id) as questions_count
                     FROM exams e
                     LEFT JOIN user_progress up ON e.id = up.content_id 
                         AND up.user_id = :user_id 
                         AND up.content_type = "exam"
                     LEFT JOIN exam_questions q ON q.exam_id = e.id
                     WHERE e.is_active = 1
                     GROUP BY e.id
                     ORDER BY e.created_at DESC',
                    [':user_id' => $userId]
                )->fetchAll();
                
                echo '<p>• Complex inquiry results: ' . count($complexQuery) . ' a assessment</p>';
                
                if (count($complexQuery) > 0 && (!isset($exams) || !is_array($exams) || count($exams) === 0)) {
                  echo '<p class="text-red-600"><strong>The problem is specific:</strong> The query works, but the data does not reach the offer!</p>';
                  echo '<p class="text-green-600"><strong>the solution:</strong> A problem with passing data from controller for display</p>';
                }
                
              } catch (Exception $e) {
                echo '<p class="text-red-600">Diagnosis error: ' . htmlspecialchars($e->getMessage()) . '</p>';
              }
              ?>
            </div>
          </div>
          
          <a href="<?= $basePath ?>/dashboard" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <i class="ri-arrow-right-line mr-2"></i>
            Back to the control panel
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
// Filter assessments
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const difficultyFilter = document.getElementById('difficultyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const examCards = document.querySelectorAll('.exam-card');

    function filterExams() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedDifficulty = difficultyFilter.value;
        const selectedStatus = statusFilter.value;

        examCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            const category = card.dataset.category;
            const difficulty = card.dataset.difficulty;
            const status = card.dataset.status;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesDifficulty = !selectedDifficulty || difficulty === selectedDifficulty;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesCategory && matchesDifficulty && matchesStatus) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease-in';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add the listeners of the events
    searchInput.addEventListener('input', filterExams);
    categoryFilter.addEventListener('change', filterExams);
    difficultyFilter.addEventListener('change', filterExams);
    statusFilter.addEventListener('change', filterExams);
});

// addition CSS For animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
`;
document.head.appendChild(style);

// Assessment cancellation function
function confirmAbandon(examId) {
    if (confirm('Are you sure to cancel the current assessment? All reserved answers will be lost.')) {
        // Send the request to cancel the assessment
        fetch(`<?= $basePath ?>/exams/abandon`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `exam_id=${examId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload this page to refresh statuses
                location.reload();
            } else {
                alert('Error canceling the assessment: ' + (data.message || 'An unknown mistake'));
            }
        })
        .catch(error => {
            console.error('Error canceling the assessment:', error);
            alert('An error occurred during the cancellation of the assessment');
        });
    }
}
</script>

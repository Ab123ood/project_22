<?php
// app/views/employee/profile.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-4">
        <a href="<?= $basePath ?>/dashboard" class="text-gray-500 hover:text-primary">
          <i class="ri-arrow-right-line text-xl"></i>
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">The profile</h1>
      </div>
      <p class="text-gray-600">Manage your personal data and account settings</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Profile -->
      <div class="lg:col-span-2">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
          <div class="flex items-center gap-6 mb-6">
            <div class="relative">
              <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-white text-2xl font-bold">
                <?= strtoupper(substr($user['name'] ?? '', 0, 1)) ?>
              </div>
              <button class="absolute -bottom-1 -right-1 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors">
                <i class="ri-camera-line text-sm"></i>
              </button>
            </div>
            <div class="flex-1">
              <h2 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($user['name'] ?? '') ?></h2>
              <p class="text-gray-600 mb-2"><?= htmlspecialchars($user['email'] ?? '') ?></p>
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                  <i class="ri-building-line"></i>
                  <?= htmlspecialchars($user['department'] ?? 'undefined') ?>
                </span>
                <span class="flex items-center gap-1">
                  <i class="ri-calendar-line"></i>
                  Join <?= date('Y/m/d', strtotime($user['created_at'] ?? 'now')) ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Edit form -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6">Personal information</h3>
          
          <form id="profileForm" method="post" action="<?= $basePath ?>/profile/update">
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">full name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">e-mail</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">user name</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                  <option value="">Choose the section</option>
                  <option value="IT" <?= ($user['department'] ?? '') === 'IT' ? 'selected' : '' ?>>Information technology</option>
                  <option value="HR" <?= ($user['department'] ?? '') === 'HR' ? 'selected' : '' ?>>Human resources</option>
                  <option value="Finance" <?= ($user['department'] ?? '') === 'Finance' ? 'selected' : '' ?>>Finance</option>
                  <option value="Marketing" <?= ($user['department'] ?? '') === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                  <option value="Operations" <?= ($user['department'] ?? '') === 'Operations' ? 'selected' : '' ?>>Operations</option>
                </select>
              </div>
            </div>
            
            <div class="flex justify-end mt-6">
              <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                <i class="ri-save-line mr-2"></i>
                Save changes
              </button>
            </div>
          </form>
        </div>

        <!-- change password -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6">change password</h3>
          
          <form id="passwordForm" method="post" action="<?= $basePath ?>/profile/change-password">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" name="current_password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" name="new_password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm the new password</label>
                <input type="password" name="confirm_password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
              </div>
            </div>
            
            <div class="flex justify-end mt-6">
              <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                <i class="ri-lock-line mr-2"></i>
                change password
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Side tape -->
      <div class="space-y-6">
        <!-- Quick Statistics -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">My statistics</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                  <i class="ri-trophy-line"></i>
                </div>
                <span class="text-gray-700">Total points</span>
              </div>
              <span class="font-bold text-gray-900"><?= $userStats['total_points'] ?? 0 ?></span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                  <i class="ri-check-line"></i>
                </div>
                <span class="text-gray-700">Complete assessments</span>
              </div>
              <span class="font-bold text-gray-900"><?= $userStats['completed_exams'] ?? 0 ?></span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                  <i class="ri-star-line"></i>
                </div>
                <span class="text-gray-700">Slogans</span>
              </div>
              <span class="font-bold text-gray-900"><?= $userStats['badges_count'] ?? 0 ?></span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                  <i class="ri-eye-line"></i>
                </div>
                <span class="text-gray-700">Scene content</span>
              </div>
              <span class="font-bold text-gray-900"><?= $userStats['content_viewed'] ?? 0 ?></span>
            </div>
          </div>
        </div>

        <!-- The last badges -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">The last badges</h3>
          <?php if (!empty($recentBadges)): ?>
          <div class="space-y-3">
            <?php foreach ($recentBadges as $badge): ?>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center">
                <i class="ri-medal-line text-white"></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-gray-900"><?= htmlspecialchars($badge['name'] ?? '') ?></div>
                <div class="text-sm text-gray-500"><?= date('Y/m/d', strtotime($badge['earned_at'] ?? 'now')) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="text-center py-4">
            <i class="ri-medal-line text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 text-sm">You didn't get badges yet</p>
          </div>
          <?php endif; ?>
          
          <a href="<?= $basePath ?>/badges" class="block text-center mt-4 text-primary hover:underline text-sm">
            View all badges
          </a>
        </div>

        <!-- Last activity -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">The last activity</h3>
          <?php if (!empty($recentActivity)): ?>
          <div class="space-y-3">
            <?php foreach ($recentActivity as $activity): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <i class="ri-time-line text-sm"></i>
              </div>
              <div class="flex-1">
                <p class="text-sm text-gray-900"><?= htmlspecialchars($activity['description'] ?? '') ?></p>
                <p class="text-xs text-gray-500"><?= date('Y/m/d H:i', strtotime($activity['created_at'] ?? 'now')) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="text-center py-4">
            <i class="ri-history-line text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 text-sm">There is no modern activity</p>
          </div>
          <?php endif; ?>
        </div>

        <!-- Faculty settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Faculty settings</h3>
          <form method="post" action="<?= $basePath ?>/profile/notification-settings">
            <div class="space-y-4">
              <label class="flex items-center justify-between">
                <span class="text-gray-700">New assessment notifications</span>
                <input type="checkbox" name="exam_notifications" <?= ($user['exam_notifications'] ?? true) ? 'checked' : '' ?>
                       class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
              </label>
              
              <label class="flex items-center justify-between">
                <span class="text-gray-700">New content notifications</span>
                <input type="checkbox" name="content_notifications" <?= ($user['content_notifications'] ?? true) ? 'checked' : '' ?>
                       class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
              </label>
              
              <label class="flex items-center justify-between">
                <span class="text-gray-700">Steeling notifications</span>
                <input type="checkbox" name="survey_notifications" <?= ($user['survey_notifications'] ?? true) ? 'checked' : '' ?>
                       class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
              </label>
              
              <label class="flex items-center justify-between">
                <span class="text-gray-700">Achievements notifications</span>
                <input type="checkbox" name="achievement_notifications" <?= ($user['achievement_notifications'] ?? true) ? 'checked' : '' ?>
                       class="w-4 h-4 text-primary focus:ring-primary border-gray-300 rounded">
              </label>
            </div>
            
            <button type="submit" class="w-full mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
              Save settings
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile update form
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('The changes were successfully saved');
                location.reload();
            } else {
                alert('Error event: ' + (data.message || 'Failure'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A mistake occurred in the contact');
        });
    });

    // Password change form
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = this.querySelector('[name="new_password"]').value;
        const confirmPassword = this.querySelector('[name="confirm_password"]').value;
        
        if (newPassword !== confirmPassword) {
            alert('The new password and its confirmation are not identical');
            return;
        }
        
        if (newPassword.length < 6) {
            alert('The password should be at least 6 letters');
            return;
        }
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('The password has been successfully changed');
                this.reset();
            } else {
                alert('Error event: ' + (data.message || 'Failure to change the password'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A mistake occurred in the contact');
        });
    });

    // Save notifications settings automatically
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const form = this.closest('form');
            if (form && form.action.includes('notification-settings')) {
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                }).catch(console.error);
            }
        });
    });
});
</script>

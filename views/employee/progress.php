<?php
// app/views/employee/progress.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-white min-h-screen">
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-4">
        <a href="<?= $basePath ?>/dashboard" class="text-gray-500 hover:text-primary">
          <i class="ri-arrow-right-line text-xl"></i>
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">تقدمي الشخصي</h1>
      </div>
      <p class="text-gray-600">تتبع إنجازاتك ومستوى تقدمك في التدريب</p>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
            <i class="ri-coin-line text-xl"></i>
          </div>
          <span class="text-xs text-yellow-700 bg-yellow-200 px-2 py-1 rounded-full">+15 اليوم</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">1,247</div>
        <div class="text-sm text-gray-600">إجمالي النقاط</div>
      </div>
      
      <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-lg bg-green-500 text-white flex items-center justify-center">
            <i class="ri-trophy-line text-xl"></i>
          </div>
          <span class="text-xs text-green-700 bg-green-200 px-2 py-1 rounded-full">المستوى 3</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">12</div>
        <div class="text-sm text-gray-600">شارات مكتسبة</div>
      </div>
      
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
            <i class="ri-check-line text-xl"></i>
          </div>
          <span class="text-xs text-blue-700 bg-blue-200 px-2 py-1 rounded-full">85%</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">17</div>
        <div class="text-sm text-gray-600">اختبارات مكتملة</div>
      </div>
      
      <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-lg bg-orange-500 text-white flex items-center justify-center">
            <i class="ri-fire-line text-xl"></i>
          </div>
          <span class="text-xs text-orange-700 bg-orange-200 px-2 py-1 rounded-full">نشط</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 mb-1">7</div>
        <div class="text-sm text-gray-600">أيام متتالية</div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- التقدم العام -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6">التقدم العام</h3>
          
          <!-- شريط التقدم الرئيسي -->
          <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-700">مستوى التدريب</span>
              <span class="text-sm text-gray-500">68% مكتمل</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full" style="width: 68%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
              <span>المستوى 2</span>
              <span>المستوى 3</span>
            </div>
          </div>

          <!-- تقدم المجالات -->
          <div class="space-y-4">
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-700">التصيد الإلكتروني</span>
                <span class="text-sm text-green-600 font-medium">مكتمل</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
              </div>
            </div>
            
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-700">كلمات المرور</span>
                <span class="text-sm text-blue-600 font-medium">85%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
              </div>
            </div>
            
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-700">الهندسة الاجتماعية</span>
                <span class="text-sm text-yellow-600 font-medium">42%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-yellow-500 h-2 rounded-full" style="width: 42%"></div>
              </div>
            </div>
            
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-700">أمان الشبكات</span>
                <span class="text-sm text-gray-500 font-medium">15%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gray-400 h-2 rounded-full" style="width: 15%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- النشاط الأخير -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">النشاط الأخير</h3>
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-check-line"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">أكملت اختبار "التصيد الإلكتروني المتقدم"</div>
                <div class="text-xs text-gray-500">حصلت على 95% - منذ ساعتين</div>
              </div>
            </div>
            
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-star-line"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">حصلت على شارة "خبير كلمات المرور"</div>
                <div class="text-xs text-gray-500">أمس</div>
              </div>
            </div>
            
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-play-line"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-900">شاهدت فيديو "حماية البريد الإلكتروني"</div>
                <div class="text-xs text-gray-500">منذ 3 أيام</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- الشارات والإنجازات -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">الشارات المكتسبة</h3>
          <div class="grid grid-cols-3 gap-3">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center">
                <i class="ri-shield-check-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-600">مبتدئ الأمان</div>
            </div>
            
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                <i class="ri-mail-check-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-600">خبير البريد</div>
            </div>
            
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="ri-key-2-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-600">حارس كلمات المرور</div>
            </div>
            
            <div class="text-center opacity-50">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <i class="ri-lock-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-400">قريباً</div>
            </div>
            
            <div class="text-center opacity-50">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <i class="ri-wifi-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-400">قريباً</div>
            </div>
            
            <div class="text-center opacity-50">
              <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <i class="ri-bug-line text-xl"></i>
              </div>
              <div class="text-xs text-gray-400">قريباً</div>
            </div>
          </div>
        </div>

        <!-- الأهداف -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">أهدافي</h3>
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                <i class="ri-check-line text-sm"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm text-gray-900">أكمل 5 اختبارات</div>
                <div class="text-xs text-gray-500">5/5 مكتمل</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="ri-time-line text-sm"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm text-gray-900">احصل على 1000 نقطة</div>
                <div class="text-xs text-gray-500">847/1000</div>
                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                  <div class="bg-blue-500 h-1 rounded-full" style="width: 84.7%"></div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <i class="ri-target-line text-sm"></i>
              </div>
              <div class="flex-1">
                <div class="text-sm text-gray-900">أكمل جميع المجالات</div>
                <div class="text-xs text-gray-500">2/4 مكتمل</div>
                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                  <div class="bg-gray-400 h-1 rounded-full" style="width: 50%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

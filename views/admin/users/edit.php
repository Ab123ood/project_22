<?php
// app/views/admin/users/edit.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="<?= $basePath ?>/admin/users" class="text-gray-600 hover:text-gray-900">
            <i class="ri-arrow-right-line text-xl"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-900">تعديل المستخدم</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <?php if (!empty($errors ?? [])): ?>
            <div class="mb-4 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
                <ul class="list-disc mr-6">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= $basePath ?>/admin/users/update" class="space-y-6">
            <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الدور</label>
                    <select name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">اختر الدور</option>
                        <?php foreach (($roles ?? []) as $r): ?>
                            <option value="<?= (int)$r['id'] ?>" <?= isset($user['role_id']) && (int)$user['role_id'] === (int)$r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $st = $user['status'] ?? 'active'; ?>
                        <option value="active" <?= $st==='active'?'selected':''; ?>>نشط</option>
                        <option value="inactive" <?= $st==='inactive'?'selected':''; ?>>غير نشط</option>
                        <option value="pending" <?= $st==='pending'?'selected':''; ?>>في الانتظار</option>
                        <option value="banned" <?= $st==='banned'?'selected':''; ?>>محظور</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور">
                    <p class="text-xs text-gray-500 mt-1">الحد الأدنى 6 أحرف إذا كنت تريد تغيير كلمة المرور</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="force_reset" class="ml-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($user['force_password_reset']) && (int)$user['force_password_reset'] === 1 ? 'checked' : '' ?>>
                    فرض تغيير كلمة المرور عند أول دخول
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="<?= $basePath ?>/admin/users" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">إلغاء</a>
                    <a href="<?= $basePath ?>/admin/users/view?id=<?= (int)($user['id'] ?? 0) ?>" class="px-6 py-3 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50">عرض</a>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">حفظ التغييرات</button>
                </div>
            </div>
        </form>
    </div>
</div>

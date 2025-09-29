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
        <h2 class="text-xl font-bold text-gray-900">User modification</h2>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select a role</option>
                        <?php foreach (($roles ?? []) as $r): ?>
                            <option value="<?= (int)$r['id'] ?>" <?= isset($user['role_id']) && (int)$user['role_id'] === (int)$r['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $st = $user['status'] ?? 'active'; ?>
                        <option value="active" <?= $st==='active'?'selected':''; ?>>Active</option>
                        <option value="inactive" <?= $st==='inactive'?'selected':''; ?>>InActive</option>
                        <option value="pending" <?= $st==='pending'?'selected':''; ?>>Pending</option>
                        <option value="banned" <?= $st==='banned'?'selected':''; ?>>Banned</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="Password" name="Password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Leave it empty if you don't want to change the Password.>
                    <p class="text-xs text-gray-500 mt-1">Minimum 6 charactersif you want to change the Password</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="force_reset" class="mr-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($user['force_Password_reset']) && (int)$user['force_Password_reset'] === 1 ? 'checked' : '' ?>>
                    Imposing a Password change at the first entry
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="<?= $basePath ?>/admin/users" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</a>
                    <a href="<?= $basePath ?>/admin/users/view?id=<?= (int)($user['id'] ?? 0) ?>" class="px-6 py-3 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50">an offer</a>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// app/views/admin/users/list.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<!-- Page header with primary action -->
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900">User management</h2>
    <a href="<?= $basePath ?>/admin/users/add" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm ring-1 ring-blue-700/10">
        <i class="ri-user-add-line"></i>
        Add a user
    </a>
</div>

<!-- Stats cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <i class="ri-user-line text-white text-xl"></i>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">Total users</p>
                <p class="text-2xl font-bold text-gray-900"><?= isset($counts['total']) ? (int)$counts['total'] : 0 ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                <i class="ri-user-check-line text-white text-xl"></i>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">Active users</p>
                <p class="text-2xl font-bold text-gray-900"><?= isset($counts['active']) ? (int)$counts['active'] : 0 ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                <i class="ri-user-add-line text-white text-xl"></i>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">New users (30 days)</p>
                <p class="text-2xl font-bold text-gray-900"><?= isset($counts['new30']) ? (int)$counts['new30'] : 0 ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                <i class="ri-user-forbid-line text-white text-xl"></i>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">Prohibited</p>
                <p class="text-2xl font-bold text-gray-900"><?= isset($counts['banned']) ? (int)$counts['banned'] : 0 ?></p>
            </div>
        </div>
    </div>
</div>



<!-- Users table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">User menu</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">user</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">The date of joining</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php $initial = function_exists('mb_substr') ? mb_substr($u['name'] ?? '?', 0, 1, 'UTF-8') : substr($u['name'] ?? '?', 0, 1); ?>
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3"><?= htmlspecialchars($initial) ?></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($u['name'] ?? '') ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($u['email'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($u['role'] ?? '—') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(substr((string)($u['created_at'] ?? ''), 0, 10)) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $status = $u['status'] ?? 'inactive';
                                    $map = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-yellow-100 text-yellow-800',
                                        'banned' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $cls = $map[$status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $cls ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="<?= $basePath ?>/admin/users/view?id=<?= $u['id'] ?>" class="text-primary-600 hover:text-primary-900 p-1" title="an offer">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="<?= $basePath ?>/admin/users/edit?id=<?= $u['id'] ?>" class="text-gray-600 hover:text-gray-900 p-1" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <button onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['name'] ?? '', ENT_QUOTES) ?>')" class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-6 py-6 text-center text-sm text-gray-500" colspan="6">
                            There is no data to be displayed now.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                an offer <span class="font-medium">1</span> to <span class="font-medium">10</span> from <span class="font-medium"><?= isset($counts['total']) ? (int)$counts['total'] : 0 ?></span> a result
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">the previous</button>
                <button class="px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-primary-600 rounded-md">1</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">the next</button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId, userName) {
    if (confirm(`Are you sure to delete the user?${userName}"?\n\nThis action cannot be undone.`)) {
        const formData = new FormData();
        formData.append('id', userId);
        
        fetch('<?= $basePath ?>/admin/users/delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('mistake: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user');
        });
    }
}
</script>

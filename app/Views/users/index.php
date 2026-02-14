<?= view('layouts/header', ['title' => 'Users']) ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">User Management</h4>
    <a href="/users/new" class="btn btn-primary">Add User</a>
</div>

<div class="table-responsive bg-white shadow-sm">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= esc($u['id']) ?></td>
                <td><?= esc($u['username']) ?></td>
                <td><?= esc($u['full_name']) ?></td>
                <td><?= esc($u['role']) ?></td>
                <td>
                    <?php if ((int) $u['is_active'] === 1): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($u['last_login_at'] ?? '-') ?></td>
                <td>
                    <a class="btn btn-sm btn-outline-warning" href="/users/edit/<?= $u['id'] ?>">Edit</a>
                    <?php if ((int) $u['id'] !== (int) session()->get('user_id')): ?>
                    <form method="post" action="/users/delete/<?= $u['id'] ?>" class="d-inline" onsubmit="return confirm('Delete this user?');">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= view('layouts/footer') ?>


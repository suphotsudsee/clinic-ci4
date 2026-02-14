<?= view('layouts/header', ['title' => $mode === 'create' ? 'Add User' : 'Edit User']) ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5><?= $mode === 'create' ? 'Create User' : 'Update User' ?></h5>
        <form method="post" action="<?= $mode === 'create' ? '/users/create' : '/users/update/' . $user['id'] ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Username *</label>
                    <input class="form-control" name="username" value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Full Name *</label>
                    <input class="form-control" name="full_name" value="<?= esc(old('full_name', $user['full_name'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role *</label>
                    <?php $role = old('role', $user['role'] ?? 'staff'); ?>
                    <select class="form-select" name="role" required>
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>admin</option>
                        <option value="doctor" <?= $role === 'doctor' ? 'selected' : '' ?>>doctor</option>
                        <option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>staff</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><?= $mode === 'create' ? 'Password *' : 'New Password (optional)' ?></label>
                    <input type="password" class="form-control" name="password" <?= $mode === 'create' ? 'required' : '' ?>>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <?php $isActive = (int) old('is_active', (int) ($user['is_active'] ?? 1)); ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $isActive === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active account</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary"><?= $mode === 'create' ? 'Create' : 'Save Changes' ?></button>
                <a class="btn btn-secondary" href="/users">Back</a>
            </div>
        </form>
    </div>
</div>
<?= view('layouts/footer') ?>


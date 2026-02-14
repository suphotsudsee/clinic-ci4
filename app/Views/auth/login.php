<?= view('layouts/header', ['title' => 'เข้าสู่ระบบ']) ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">เข้าสู่ระบบคลินิก</h5>
                <form method="post" action="/login">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-muted small mt-3">ค่าเริ่มต้น: admin / admin123</div>
            </div>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>

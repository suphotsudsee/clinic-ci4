<?= view('layouts/header', ['title' => $mode === 'create' ? 'เพิ่มผู้ป่วย' : 'แก้ไขผู้ป่วย']) ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5><?= $mode === 'create' ? 'ลงทะเบียนผู้ป่วย' : 'แก้ไขข้อมูลผู้ป่วย' ?></h5>
        <form method="post" action="<?= $mode === 'create' ? '/patients/create' : '/patients/update/' . $patient['id'] ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">HN</label><input class="form-control" name="hn" value="<?= esc($patient['hn'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">เลขบัตรประชาชน</label><input class="form-control" name="cid" value="<?= esc($patient['cid'] ?? '') ?>" required></div>
                <div class="col-md-2"><label class="form-label">คำนำหน้า</label><input class="form-control" name="title_name" value="<?= esc($patient['title_name'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">เพศ</label>
                    <select class="form-select" name="gender">
                        <option value="">เลือก</option>
                        <option value="M" <?= (($patient['gender'] ?? '') === 'M') ? 'selected' : '' ?>>ชาย</option>
                        <option value="F" <?= (($patient['gender'] ?? '') === 'F') ? 'selected' : '' ?>>หญิง</option>
                        <option value="O" <?= (($patient['gender'] ?? '') === 'O') ? 'selected' : '' ?>>อื่นๆ</option>
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">ชื่อ</label><input class="form-control" name="first_name" value="<?= esc($patient['first_name'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">นามสกุล</label><input class="form-control" name="last_name" value="<?= esc($patient['last_name'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">วันเกิด</label><input type="date" class="form-control" name="dob" value="<?= esc($patient['dob'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">โทรศัพท์</label><input class="form-control" name="phone" value="<?= esc($patient['phone'] ?? '') ?>"></div>
                <div class="col-md-8"><label class="form-label">ที่อยู่</label><input class="form-control" name="address" value="<?= esc($patient['address'] ?? '') ?>"></div>
                <div class="col-12"><label class="form-label">ประวัติแพ้ยา</label><textarea class="form-control" name="allergy_note" rows="2"><?= esc($patient['allergy_note'] ?? '') ?></textarea></div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">บันทึก</button>
                <a class="btn btn-secondary" href="/patients">ย้อนกลับ</a>
            </div>
        </form>
    </div>
</div>
<?= view('layouts/footer') ?>

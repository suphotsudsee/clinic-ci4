<?= view('layouts/header', ['title' => 'อ่านบัตรประชาชน']) ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5>Smart Card Reader</h5>
        <p class="text-muted mb-2">เสียบบัตรประชาชนและกดปุ่มอ่านข้อมูลจาก Local Service</p>
        <button id="btnReadCard" class="btn btn-primary">อ่านข้อมูลจากบัตร</button>
        <span id="cardStatus" class="ms-2 text-muted"></span>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <h5>ข้อมูลผู้ป่วย (รองรับกรอกมือ)</h5>
        <form method="post" action="/patients/card/import" id="cardForm">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">เลขบัตรประชาชน</label><input class="form-control" name="cid" required></div>
                <div class="col-md-2"><label class="form-label">คำนำหน้า</label><input class="form-control" name="title_name"></div>
                <div class="col-md-3"><label class="form-label">ชื่อ</label><input class="form-control" name="first_name" required></div>
                <div class="col-md-3"><label class="form-label">นามสกุล</label><input class="form-control" name="last_name" required></div>
                <div class="col-md-2"><label class="form-label">เพศ</label><input class="form-control" name="gender"></div>
                <div class="col-md-3"><label class="form-label">วันเกิด</label><input type="date" class="form-control" name="dob"></div>
                <div class="col-md-7"><label class="form-label">ที่อยู่</label><input class="form-control" name="address"></div>
                <div class="col-md-4"><label class="form-label">โทรศัพท์</label><input class="form-control" name="phone"></div>
                <div class="col-md-8"><label class="form-label">ประวัติแพ้ยา</label><input class="form-control" name="allergy_note"></div>
            </div>
            <button class="btn btn-success mt-3">บันทึกข้อมูลผู้ป่วย</button>
        </form>
    </div>
</div>
<script>
$('#btnReadCard').on('click', function () {
    $('#cardStatus').text('กำลังอ่านข้อมูล...');
    $.post('/api/card/read', {}, function (res) {
        if (!res.ok) {
            $('#cardStatus').text('ไม่สามารถอ่านบัตรได้ กรุณากรอกข้อมูลด้วยมือ');
            return;
        }
        const data = res.data || {};
        $('input[name="cid"]').val(data.cid || '');
        $('input[name="title_name"]').val(data.title_name || '');
        $('input[name="first_name"]').val(data.first_name || '');
        $('input[name="last_name"]').val(data.last_name || '');
        $('input[name="gender"]').val(data.gender || '');
        $('input[name="dob"]').val(data.dob || '');
        $('input[name="address"]').val(data.address || '');
        $('#cardStatus').text('อ่านข้อมูลสำเร็จ');
    }).fail(function () {
        $('#cardStatus').text('Card Reader Service ไม่พร้อมใช้งาน');
    });
});
</script>
<?= view('layouts/footer') ?>

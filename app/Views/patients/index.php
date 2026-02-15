<?= view('layouts/header', ['title' => 'ข้อมูลผู้ป่วย']) ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">ข้อมูลผู้ป่วย</h4>
    <div>
        <a href="/patients/card" class="btn btn-outline-primary">อ่านบัตรประชาชน</a>
        <a href="/patients/new" class="btn btn-primary">เพิ่มผู้ป่วย</a>
    </div>
</div>
<form class="row g-2 mb-3" method="get" action="/patients/search">
    <div class="col-md-4"><input class="form-control" type="text" name="q" value="<?= esc($q) ?>" placeholder="ค้นหา HN / CID / ชื่อ"></div>
    <div class="col-auto"><button class="btn btn-secondary">ค้นหา</button></div>
</form>
<div class="table-responsive bg-white shadow-sm">
<table class="table table-hover mb-0">
<thead><tr><th>รูป</th><th>HN</th><th>CID</th><th>ชื่อ-สกุล</th><th>โทรศัพท์</th><th>จัดการ</th></tr></thead>
<tbody>
<?php foreach ($patients as $p): ?>
<tr>
    <td><?= view('components/patient_photo', ['photo' => $p['photo'] ?? '', 'size' => 44, 'alt' => trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))]) ?></td>
    <td><?= esc($p['hn']) ?></td>
    <td><?= esc($p['cid']) ?></td>
    <td><?= esc(trim($p['title_name'] . ' ' . $p['first_name'] . ' ' . $p['last_name'])) ?></td>
    <td><?= esc($p['phone']) ?></td>
    <td>
        <a class="btn btn-sm btn-outline-info" href="/patients/show/<?= $p['id'] ?>">ดู</a>
        <a class="btn btn-sm btn-outline-warning" href="/patients/edit/<?= $p['id'] ?>">แก้ไข</a>
        <a class="btn btn-sm btn-outline-success" href="/visits/new/<?= $p['id'] ?>">ตรวจ</a>
        <?php if (session()->get('role') === 'admin'): ?>
        <form method="post" action="/patients/delete/<?= $p['id'] ?>" class="d-inline" onsubmit="return confirm('ยืนยันลบข้อมูล?');">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger">ลบ</button>
        </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?= view('layouts/footer') ?>

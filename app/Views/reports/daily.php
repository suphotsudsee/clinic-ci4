<?= view('layouts/header', ['title' => 'รายงานประจำวัน']) ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2" method="get" action="/reports/daily">
            <div class="col-md-3"><label class="form-label">ตั้งแต่วันที่</label><input type="date" class="form-control" name="start" value="<?= esc($start) ?>"></div>
            <div class="col-md-3"><label class="form-label">ถึงวันที่</label><input type="date" class="form-control" name="end" value="<?= esc($end) ?>"></div>
            <div class="col-md-6 d-flex align-items-end gap-2">
                <button class="btn btn-primary">แสดงรายงาน</button>
                <a class="btn btn-outline-success" href="/reports/excel?start=<?= esc($start) ?>&end=<?= esc($end) ?>">Export Excel</a>
                <a class="btn btn-outline-danger" href="/reports/pdf?start=<?= esc($start) ?>&end=<?= esc($end) ?>">Export PDF</a>
            </div>
        </form>
    </div>
</div>
<div class="table-responsive bg-white shadow-sm">
<table class="table table-striped mb-0">
<thead><tr><th>รูป</th><th>วันที่</th><th>HN</th><th>CID</th><th>ชื่อผู้ป่วย</th><th>อาการสำคัญ</th><th>วินิจฉัย</th></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
    <td><?= view('components/patient_photo', ['photo' => $r['photo'] ?? '', 'size' => 40, 'alt' => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))]) ?></td>
    <td><?= esc($r['visit_date']) ?></td>
    <td><?= esc($r['hn']) ?></td>
    <td><?= esc($r['cid']) ?></td>
    <td><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></td>
    <td><?= esc($r['chief_complaint']) ?></td>
    <td><?= esc($r['diagnosis']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?= view('layouts/footer') ?>

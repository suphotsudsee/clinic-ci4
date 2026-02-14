<?= view('layouts/header', ['title' => 'รายละเอียดผู้ป่วย']) ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5><?= esc($patient['title_name'] . $patient['first_name'] . ' ' . $patient['last_name']) ?></h5>
        <div class="row">
            <div class="col-md-3"><strong>HN:</strong> <?= esc($patient['hn']) ?></div>
            <div class="col-md-3"><strong>CID:</strong> <?= esc($patient['cid']) ?></div>
            <div class="col-md-3"><strong>โทรศัพท์:</strong> <?= esc($patient['phone']) ?></div>
            <div class="col-md-3"><strong>วันเกิด:</strong> <?= esc($patient['dob']) ?></div>
        </div>
        <div class="mt-2"><strong>ที่อยู่:</strong> <?= esc($patient['address']) ?></div>
        <div><strong>แพ้ยา:</strong> <?= esc($patient['allergy_note']) ?></div>
    </div>
</div>
<div class="mb-3">
    <a class="btn btn-success" href="/visits/new/<?= $patient['id'] ?>">บันทึกการตรวจ</a>
    <a class="btn btn-outline-primary" href="/visits/timeline/<?= $patient['id'] ?>">Timeline</a>
</div>
<?= view('layouts/footer') ?>

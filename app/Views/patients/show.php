<?= view('layouts/header', ['title' => 'รายละเอียดผู้ป่วย']) ?>
<?php
$dobRaw = trim((string) ($patient['dob'] ?? ''));
$dobDisplay = '-';
$ageDisplay = '-';

if ($dobRaw !== '') {
    $dobDate = DateTime::createFromFormat('Y-m-d', $dobRaw);
    $dobErrors = DateTime::getLastErrors();
    $warningCount = is_array($dobErrors) ? (int) ($dobErrors['warning_count'] ?? 0) : 0;
    $errorCount = is_array($dobErrors) ? (int) ($dobErrors['error_count'] ?? 0) : 0;
    $validDob = $dobDate instanceof DateTime
        && $warningCount === 0
        && $errorCount === 0
        && $dobDate->format('Y-m-d') === $dobRaw;

    if ($validDob) {
        $thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $monthIdx = (int) $dobDate->format('n') - 1;
        $yearBe = (int) $dobDate->format('Y') + 543;
        $dobDisplay = (int) $dobDate->format('j') . ' ' . $thaiMonths[$monthIdx] . ' ' . $yearBe;

        $today = new DateTime('today');
        if ($dobDate <= $today) {
            $ageDiff = $dobDate->diff($today);
            $ageDisplay = $ageDiff->y . ' ปี ' . $ageDiff->m . ' เดือน ' . $ageDiff->d . ' วัน';
        }
    }
}
?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-start">
            <div class="col-md-2">
                <?= view('components/patient_photo', ['photo' => $patient['photo'] ?? '', 'size' => 140, 'alt' => trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')), 'radius' => 10]) ?>
            </div>
            <div class="col-md-10">
                <h5><?= esc($patient['title_name'] . $patient['first_name'] . ' ' . $patient['last_name']) ?></h5>
                <div class="row">
                    <div class="col-md-3"><strong>HN:</strong> <?= esc($patient['hn']) ?></div>
                    <div class="col-md-3"><strong>CID:</strong> <?= esc($patient['cid']) ?></div>
                    <div class="col-md-3"><strong>โทรศัพท์:</strong> <?= esc($patient['phone']) ?></div>
                    <div class="col-md-3"><strong>วันเกิด:</strong> <?= esc($dobDisplay) ?></div>
                </div>
                <div class="mt-2"><strong>อายุ ณ ปัจจุบัน:</strong> <?= esc($ageDisplay) ?></div>
                <div class="mt-2"><strong>ที่อยู่:</strong> <?= esc($patient['address']) ?></div>
                <div><strong>แพ้ยา:</strong> <?= esc($patient['allergy_note']) ?></div>
            </div>
        </div>
    </div>
</div>
<div class="mb-3">
    <a class="btn btn-success" href="/visits/new/<?= $patient['id'] ?>">บันทึกการตรวจ</a>
    <a class="btn btn-outline-primary" href="/visits/timeline/<?= $patient['id'] ?>">Timeline</a>
</div>
<?= view('layouts/footer') ?>

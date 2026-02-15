<?= view('layouts/header', ['title' => 'Visit Timeline']) ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <?= view('components/patient_photo', ['photo' => $patient['photo'] ?? '', 'size' => 72, 'alt' => trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))]) ?>
        <h4 class="mb-0">Visit Timeline: <?= esc($patient['hn'] . ' - ' . $patient['first_name'] . ' ' . $patient['last_name']) ?></h4>
    </div>
    <a href="/visits/new/<?= $patient['id'] ?>" class="btn btn-success">Add Visit</a>
</div>
<?php foreach ($visits as $v): ?>
<div class="card shadow-sm mb-3">
    <div class="card-header d-flex justify-content-between">
        <span><?= esc($v['visit_date']) ?></span>
        <?php if (in_array(session()->get('role'), ['admin', 'doctor'], true)): ?>
            <a class="btn btn-sm btn-outline-warning" href="/visits/edit/<?= $v['id'] ?>">Edit</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div><strong>Chief Complaint:</strong> <?= esc($v['chief_complaint']) ?></div>
        <div><strong>Vital Signs:</strong> <?= esc($v['vital_signs']) ?></div>
        <div><strong>Diagnosis:</strong> <?= esc($v['diagnosis']) ?></div>
        <div><strong>Disease Code:</strong> <?= esc($v['diseasecode'] ?? '-') ?></div>
        <div><strong>Treatment:</strong> <?= esc($v['treatment']) ?></div>
        <div><strong>Medication:</strong> <?= esc($v['medication']) ?></div>
        <div><strong>Doctor Note:</strong> <?= esc($v['doctor_note']) ?></div>
    </div>
</div>
<?php endforeach; ?>
<?= view('layouts/footer') ?>

<?= view('layouts/header', ['title' => 'Visit List']) ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Visit List</h4>
    <a class="btn btn-outline-secondary" href="/patients">Select patient to create visit</a>
</div>
<div class="table-responsive bg-white shadow-sm">
<table class="table table-striped mb-0">
<thead><tr><th>Photo</th><th>Date</th><th>HN</th><th>Patient</th><th>Diagnosis</th><th>Disease Code</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($visits as $v): ?>
<tr>
    <td><?= view('components/patient_photo', ['photo' => $v['photo'] ?? '', 'size' => 40, 'alt' => trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''))]) ?></td>
    <td><?= esc($v['visit_date']) ?></td>
    <td><?= esc($v['hn']) ?></td>
    <td><?= esc($v['first_name'] . ' ' . $v['last_name']) ?></td>
    <td><?= esc($v['diagnosis']) ?></td>
    <td><?= esc($v['diseasecode'] ?? '-') ?></td>
    <td>
        <a class="btn btn-sm btn-outline-primary" href="/visits/timeline/<?= $v['patient_id'] ?>">Timeline</a>
        <?php if (in_array(session()->get('role'), ['admin', 'doctor'], true)): ?>
            <a class="btn btn-sm btn-outline-warning" href="/visits/edit/<?= $v['id'] ?>">Edit</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?= view('layouts/footer') ?>

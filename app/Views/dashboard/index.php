<?= view('layouts/header', ['title' => 'Dashboard']) ?>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted">Total Patients</h6>
            <h2><?= esc($totalPatients) ?></h2>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <h6 class="text-muted">Visits Today</h6>
            <h2><?= esc($todayVisits) ?></h2>
        </div></div>
    </div>
</div>
<div class="card mt-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Quick Actions</span>
        <div>
            <a href="/patients/card" class="btn btn-sm btn-outline-primary">Read Card</a>
            <a href="/patients/new" class="btn btn-sm btn-primary">New Patient</a>
        </div>
    </div>
    <div class="card-body">
        <a href="/patients" class="btn btn-outline-secondary me-2">Patients</a>
        <a href="/visits" class="btn btn-outline-secondary me-2">Visits</a>
        <a href="/reports/daily" class="btn btn-outline-secondary">Reports</a>
        <?php if (session()->get('role') === 'admin'): ?>
        <a href="/users" class="btn btn-outline-dark ms-2">Manage Users</a>
        <?php endif; ?>
    </div>
</div>
<div class="card mt-4 shadow-sm">
    <div class="card-header">Recent Visits</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Date</th><th>HN</th><th>Name</th><th>Chief Complaint</th></tr></thead>
            <tbody>
            <?php foreach ($recentVisits as $row): ?>
                <tr>
                    <td><?= esc($row['visit_date']) ?></td>
                    <td><?= esc($row['hn']) ?></td>
                    <td><?= esc($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= esc($row['chief_complaint']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= view('layouts/footer') ?>
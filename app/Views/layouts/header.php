<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Clinic CI4') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">Clinic CI4</a>
        <?php if (session()->get('isLoggedIn')): ?>
        <div class="d-flex align-items-center gap-2">
            <?php if (session()->get('role') === 'admin'): ?>
            <a class="btn btn-sm btn-light" href="/users">Users</a>
            <?php endif; ?>
            <span class="text-white small ms-2"><?= esc(session()->get('full_name')) ?> (<?= esc(session()->get('role')) ?>)</span>
            <a class="text-white small ms-2" href="/logout">Logout</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container py-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
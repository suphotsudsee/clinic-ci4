<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<?php
$fontRegularPath = str_replace('\\', '/', ROOTPATH . 'app/Fonts/NotoSansThai-Regular.ttf');
$fontBoldPath = str_replace('\\', '/', ROOTPATH . 'app/Fonts/NotoSansThai-Bold.ttf');
$fontRegularUri = 'file:///' . ltrim($fontRegularPath, '/');
$fontBoldUri = 'file:///' . ltrim($fontBoldPath, '/');
?>
<style>
@font-face {
    font-family: 'NotoSansThai';
    font-style: normal;
    font-weight: 400;
    src: url('<?= esc($fontRegularUri, 'attr') ?>') format('truetype');
}
@font-face {
    font-family: 'NotoSansThai';
    font-style: normal;
    font-weight: 700;
    src: url('<?= esc($fontBoldUri, 'attr') ?>') format('truetype');
}
body {
    font-family: 'garuda', 'NotoSansThai', 'DejaVu Sans', sans-serif;
    font-size: 12px;
}
h3 {
    margin: 0 0 12px 0;
    font-size: 16px;
}
table {
    border-collapse: collapse;
    width: 100%;
}
th, td {
    border: 1px solid #333;
    padding: 4px;
    text-align: left;
    vertical-align: top;
}
th {
    font-weight: 700;
}
</style>
</head>
<body>
<h3>รายงานผู้รับบริการ <?= esc($start) ?> ถึง <?= esc($end) ?></h3>
<table>
    <thead>
        <tr>
            <th>วันที่</th>
            <th>HN</th>
            <th>CID</th>
            <th>ชื่อผู้ป่วย</th>
            <th>อาการ</th>
            <th>วินิจฉัย</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <tr>
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
</body>
</html>
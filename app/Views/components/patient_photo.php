<?php
$photo = (string) ($photo ?? '');
$size = (int) ($size ?? 56);
$alt = (string) ($alt ?? 'Patient Photo');
$class = (string) ($class ?? '');
$radius = (int) ($radius ?? 8);
?>
<?php if ($photo !== ''): ?>
    <img
        src="<?= esc($photo, 'attr') ?>"
        alt="<?= esc($alt, 'attr') ?>"
        class="<?= esc($class, 'attr') ?>"
        style="width:<?= $size ?>px;height:<?= $size ?>px;object-fit:cover;border-radius:<?= $radius ?>px;"
    >
<?php else: ?>
    <div
        class="d-inline-flex align-items-center justify-content-center bg-light text-muted <?= esc($class, 'attr') ?>"
        style="width:<?= $size ?>px;height:<?= $size ?>px;border-radius:<?= $radius ?>px;border:1px solid #dee2e6;"
        title="No Photo"
    >
        N/A
    </div>
<?php endif; ?>

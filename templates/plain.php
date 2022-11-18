<?php foreach ($data as $key => $value): ?>
<?= $key ?>: <?= is_array($value) ? implode(', ', $value) : $value ?>

<?php endforeach ?>

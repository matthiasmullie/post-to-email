<?php foreach ($data as $key => $value): ?>
    <p>
        <strong><?= $key ?></strong><br />

        <?php if (is_array($value)): ?>
            <ul>
                <?php foreach ($value as $v): ?>
                    <li><?= str_replace("\n", '<br />', $v) ?></li>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <?= str_replace("\n", '<br />', $value) ?>
        <?php endif ?>
    </p>
<?php endforeach ?>

<?php
$rows =
    json_decode(
        file_get_contents(__DIR__ . '/../multiple-rows.json'),
        true
    );
?>
<div class="rows">
    <ul>
        <?php foreach ($rows as $row) {
            ?>
            <li>
                <div id="<?= $row['id'] ?>" class="element">
                    <h1><?= $row['name'] ?></h1>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>

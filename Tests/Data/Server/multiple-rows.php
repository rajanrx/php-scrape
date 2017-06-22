<?php
$rows =
    json_decode(
        file_get_contents(__DIR__ . '/../multiple-rows.json'),
        true
    );
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$pageSize = 3;
foreach ($rows as $count => $row) {
    if ($count < ($page * $pageSize) || $count >= (($page + 1) * $pageSize)) {
        unset($rows[$count]);
    }
}
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
    <div class="pager">
        <?php if ($page < 2) { ?>
            <a href="http://localhost:1349/multiple-rows.php?page=<?= ($page + 1) ?>"> Next Page</a>
        <?php } ?>
    </div>
</div>

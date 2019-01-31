<?php
    foreach($rows as $row) {
    $padding = 20*$row['level'];
?>
    <tr>

            <td>
                <div style="padding-left: <?= $padding ?>px;">
                    <?= $row['name'] ?>
                </div>
            </td>
            <td>></td>
            <td><?= $row['parentImg'] ?></td>
            <td><?= $row['parentName'] ?></td>
            <td><?= $row['rootImg'] ?></td>
            <td><?= $row['rootName'] ?></td>
            <td><?= $row['stepIn'] ?></td>
            <td><?= $row['stepOut'] ?></td>
    </tr>
<?php
        if(!empty($row['children'])) {
            echo tpl('topology/views/table-rows', ["rows" => $row['children']]);
        }

    }
?>


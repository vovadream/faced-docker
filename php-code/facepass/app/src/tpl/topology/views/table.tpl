<h2> Группа:<?= $groupName ?></h2>

<table class='akkt table' border='1' cellpadding='5'>
    <tr>
        <th rowspan='2'>Название</th>
        <?php //<th rowspan='2'>Описание</th> ?>
        <th rowspan='2'>Путь</th>
        <th colspan='2'>Отдел</th>
        <th colspan='2'>Департамент</th>
        <th rowspan='2'>Шагомер вход</th>
        <th rowspan='2'>Шагомер выход</th>
    </tr>
    <tr>
        <th>Изображение</th>
        <th>Название</th>
        <th>Изображение</th>
        <th>Название</th>
    </tr>
    <?php echo tpl('topology/views/table-rows', ["rows" => $rows]); ?>
    </table>
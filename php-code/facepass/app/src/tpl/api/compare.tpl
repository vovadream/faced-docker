<div class="compare">
    <h2>Запрос на сравнение документа с ручным вводом.</h2>
    <h4>Сравните следующие данные:</h4>
    <div class="img">
        <?php foreach ($scans as $item) { ?>
            <img src="/img/documents/<?php echo $item; ?>" alt="">
        <?php } ?>
    </div>
    <ul>
        <li><span>Фамилия:</span> <?php echo $surname;?></li>
        <li><span>Имя:</span> <?php echo $first_name;?></li>
        <li><span>Отчество:</span> <?php echo $patronymic;?></li>
        <li><span>Серия и номер:</span> <?php echo $series_number;?></li>
        <?php if(isset($date_birth)): ?>
            <li><span>Дата рождения:</span> <?php echo $date_birth;?></li>
        <?php endif; ?>
        <li><span>Пол:</span> <?php echo $gender==1? 'М': 'Ж';?></li>
        <?php if(isset($birthplace)): ?>
            <li><span>Место рождения:</span> <?php echo $birthplace;?></li>
        <?php endif; ?>
        <?php if(isset($passport_date)): ?>
            <li><span>Дата выдачи паспорта:</span> <?php echo $passport_date;?></li>
        <?php endif; ?>
        <?php if(isset($passport_code)): ?>
            <li><span>Код подразделения:</span> <?php echo $passport_code;?></li>
        <?php endif; ?>
        <?php if(isset($passport_place)): ?>
            <li><span>Место выдачи паспорта:</span> <?php echo $passport_place;?></li>
        <?php endif; ?>
        <?php if(isset($registration_place)): ?>
            <li><span>Место регистрации:</span> <?php echo $registration_place;?></li>
        <?php endif; ?>
    </ul>
</div>
<?php
/**
 * @var $user
 * @var $userPass
 */

$marks = $userMarks;
$marksModel = $allMarks;
$user_id = $user->id;
?>

<div class='buttonsControl nomargin'>
    <div class='tabButton active'
         onclick="showHideDivs(this, 'userAccount,passTable,userMarks,worker_access', 'userAccount', 'tabButton active');">
        Аккаунт
    </div>
    <div class='tabButton'
         onclick="showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'passTable', 'tabButton active');">
        Статистика
    </div>
    <div class='tabButton'
         onclick="showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'userMarks', 'tabButton active');">
        Метки
    </div>
    <?php if ($user->main_class == 1 || $user->user_type_id == 1) { ?>
    <div class='tabButton'
         onclick="showHideDivs(this,'userAccount,passTable,userMarks,worker_access', 'worker_access', 'tabButton active');">
        Категория доступа
    </div>
    <?php $date = new DateTime($user->birthday); ?>
    <div class="mini-profile">
        <img width="40" src='<?= GetImageURL($user->user_photo, 'user_photo') ?>' class='bigIcon' >
        <div class="user-name">
            <a href="/account/<?= $user->id ?>/"><?= $user->surname ?> <?= $user->first_name ?> <?= $user->patronymic ?></a>
            <?= $date->Format('d.m.Y') ?>
        </div>

    </div>
    <?php } ?>
</div>

<div class='userProfile'>
    <?php //Аккаунт ?>
    <div id='userAccount'>
        <div class='userData'>
            <div id="userInfo">
                <?php //<div class='userPhoto'><img src='base_path()."images/icons/Vhod_photo.PNG' class='bigIcon' \"></div> ?>

            <div class='userPhoto'><img src='<?= GetImageURL($user->user_photo, 'user_photo') ?>' class='bigIcon' ></div>
            <table border='1' cellpadding='7'>
                <tr>
                    <td>Фамилия</td>
                    <td class='ralewaybold'><?= $user->surname ?></td>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td class='ralewaybold'><?= $user->first_name ?></td>
                </tr>
                <tr>
                    <td>Отчество</td>
                    <td class='ralewaybold'><?= $user->patronymic ?></td>
                </tr>

                <tr>
                    <td>Дата рождения</td>
                    <?php if (!empty($user->birthday)) {
                        $date = new DateTime($user->birthday);
                        $formatedDate = $date->Format('d.m.Y');
                    } else {
                     $formatedDate = '';
                    }
                    ?>
                    <td class='robotocr'><?= $formatedDate ?></td>
                </tr>
                <tr>
                    <td>Тип документа</td>
                    <td class='robotocr'>Паспорт РФ</td>
                </tr>
                <tr>
                    <td>Серия, номер</td>
                    <td class='robotocr'>0234 123456</td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td class='robotocr'><?= $user->user_type ?></td>
                </tr>
                <tr>
                    <td>Место работы</td>
                    <td class='robotocr'><?= $user->work_place ?></td>
                </tr>
                <?php if ($worker != null && ($worker[0]->id != null) && ($user->main_class == 1 || $user->user_type_id == 1)) { ?>
                <tr>
                    <td>Кабинет</td>
                    <td class='robotocr'><?= $workerRoom[name] ?> (<a onclick="sendAjax('/topology/change-room/<?= $user_id ?>/', 'GET')">ИЗМЕНИТЬ</a>)</td>
                </tr>
                <?php } ?>
                <tr>
                    <td>Должность</td>
                    <td class='robotocr'><?= $user->work_position ?></td>
                </tr>
                <tr>
                    <td>Прописка</td>
                    <td class='robotocr'>г. Москва, ул. Ленина, 45-8</td>
                </tr>

                <tr>
                    <td>Работа в суде</td>
                    <td>Пример</td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td class='robotocr'><?= $user->phone ?></td>
                </tr>
                <tr>
                    <td>Почта</td>
                    <td class='robotocr'><?= $user->email ?></td>
                </tr>
                <tr>
                    <td>Рег. номер</td>
                    <td class='robotocr'><?= $user->id ?></td>
                </tr>
                <?php if ($marks != null && $marks[0]->id != null) { ?>
                <tr>
                    <td>Метка</td>
                    <td class='ralewaymed red'><?= $marks[0]->mark_name ?></td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td>Метка</td>
                    <td>Нет меток</td>
                </tr>
                <?php } ?>

            </table>
            </div>

        <div id='userDocuments'>
            <?php
            /**
            //$HTML .= " <div class='blockweight'><img src='".base_path()."images/icons/doc1.PNG' class='bigIcon'>
            //$HTML .= "<img src='".base_path()."images/icons/doc2.PNG' class='bigIcon'>
            //$HTML .= "<img src='".base_path()."images/icons/doc3.PNG' class='bigIcon'></div>
        //$HTML .= "
        **/
        ?>
        <div class='blockheight'><img src='<?= base_path() ?>images/icons/pasport1.PNG' class='bigIcon'>
        <?php /**
            //$HTML .= "<img src='".base_path()."images/icons/pasport2.PNG' class='bigIcon'></div>
            //$HTML .= "<img src='".base_path()."images/icons/snils.PNG' class='bigIcon' \"> */ ?>

        <?= tpl('chunks/documents', ['userPhotos' => $userPhotos]); ?>

        <?php /**
        <div class='blockheight'>
            <div id='rengakk'>

                <div class='reng ak'>
                    <img src='".base_path()."images/icons/rentgen.PNG' class='bigIcon'>
                    </br>Рентген
                </div>
                <div class='lotok ak'>
                    <img src='".base_path()."images/icons/lotok.PNG' class='bigIcon'>
                    </br>Лоток</br> металлодетектора
                </div>
            </div>
        </div>
        */
        ?>
    </div>

    <div class='userDataButtons'>

        <div class='button greenak otstupkn'>Подтвердить</div>

        <div class='button redak otstupkn'>Не подтверждено</div>
        <div class='button nocolor otstupkn'><a href='#'>Заблокировать вход/выход !</a></div>
        <div class='button blueak otstupkn' style='color: white;float:right;'
             onclick="sendAjax('/userpass/form/<?= $user_id ?>/', 'GET')">Пропуск</div>
</div>
</div>


</div>
</div>

<?php //Статистика ?>
<div id='passTable' style='display: none'>

    <?= tpl('account/statistic', [
    'user_id' => $user_id,
    'userPass' => $userPass
    ]); ?>

</div>

<?php //Метки ?>

<div id='userMarks' style='display: none'>
    <form name='addUserMarkForm'>
        <div class='metkhead'><b class='otstupkn'>Метка</b>
            <select class='otstupkn' name='mark'>
                <?php for ($i = 0; $i < count($marks); $i++) { ?>
                    <option value='<?= $marks[$i]->mark_id ?>'><?= $marks[$i]->mark_name ?></option>
                <?php } ?>
            </select>
            <div class='button otstupkn' onclick="sendAjax('/usermark/<?= $user_id ?>/', 'POST', 'addUserMarkForm')">Сохранить</div>
        <div class='button redak' onclick="sendAjax('/usermark_modify/', 'POST', 'selectedMarkForm')">Удалить</div>
    </form>
</div>



<div id='userMarksTable'>
    <?php if ($marks != null && $marks[0]->id != null) { ?>
    <form name='selectedMarkForm'>
        <table class='full' border='1' cellpadding='5'>
            <tr>
                <th colspan='2'>№</th>
                <th>Дата</th>
                <th>Время</th>
                <th>Метка</th>
                <th>Кто поставил</th>
                <th>Дата удаления</th>
                <th>Время удаления</th>
                <th>Кто удалил</th>
                <th>Выбрать</th>
            </tr>

            <?php for ($i = 0; $i < count($marks); $i++) {
            if ($marks[$i]->id == null) continue; ?>
            <tr>
                <?php if ($marks[$i]->date_close == null) { ?>
                <td class='robotocr'><input type='radio' name='selectedMark' value='<?= $marks[$i]->id ?>'></td>
                <td class='robotocr'><?= $marks[$i]->id ?></td>
                <?php } else { ?>
                <td class='robotocr' colspan='2'><?= $marks[$i]->id ?></td>
                <?php } ?>
                <td class='robotocr'><?= $marks[$i]->mdate ?></td>
                <td class='robotocr'><?= $marks[$i]->mtime ?></td>
                <td class='ralewaymed red'><?= $marks[$i]->mark_name ?></td>
                <td><?= $marks[$i]->worker_add_surname ?> <?= $marks[$i]->worker_add_first_name ?> <?= $marks[$i]->worker_add_last_name ?></td>
                <?php $date = new DateTime($marks[$i]->date_close); ?>
                <td class='robotocr'><?= $date->Format('d.m.Y') ?></td>
                <?php $date = new DateTime($marks[$i]->time_close); ?>
                <td class='robotocr'><?= $date->Format('d.m.Y') ?></td>
                <td><?= $marks[$i]->worker_close_surname ?> <?= $marks[$i]->worker_close_first_name ?> <?= $marks[$i]->worker_close_last_name ?></td>
            </tr>
            <?php } ?>
        </table>

    </form>

    <?php } else { ?>
    Нет данных
    <?php } ?>
</div>
</div>

<?php //Если сотрудник - выводим доп. пункты ?>
<?php if ($worker != null && ($worker[0]->id != null) && ($user->main_class == 1 || $user->user_type_id == 1)) {
//Контроль доступа сотрудника ?>
<div id='worker_access' style='display: none;'>

    <div class="general_worker_access">
        <div>
            <div>
                <span>Категория доступа</span>
            </div>
            <div>
                <span>Описание категории</span>
            </div>
        </div>
        <div>
            <div>
                <span>Пропуск</span>
            </div>
            <div>
                <span>Действует</span>
            </div>
        </div>
        <div>
            <div>
                <span>Доступ разрешен</span>
            </div>
            <div>
                <span>Вход</span>
            </div>
            <div>
                <span>Выход</span>
            </div>
        </div>
        <div>
            <div>
                <span>Роль пользователя</span>
            </div>
        </div>

    </div>
    <div class="topology_worker_access">
        <?= tpl('account/accessTopology', []); ?>
    </div>


</div>
<?php } ?>

</div>

</div>

<?php //Если сотрудник - выводим доп. пункты ?>

<?php if ($worker != null && ($worker[0]->id != null) && ($user->main_class == 1 || $user->user_type_id == 1)) { ?>
<?php //Контроль доступа сотрудника ?>
<div id='worker_access' style='display: none;'>
    <b>Категория доступа</b>

    <?= tpl('account/accessTopology', [
    'level' => 0,
    'topology' => $topology,
    'worker_id' => $worker[0]->id,
    'controller' => $controller
    ]); ?>

    <div id='worker_departaments'>
        <b>Доступ к отделам</b>
        <?= tpl('account/departmentTopology', [
        'worker_id' => $worker[0]->id,
        'controller' => $controller
        ]);
        ?>
    </div>
</div>
<?php } ?>


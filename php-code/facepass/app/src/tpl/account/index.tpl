<?php
/**
 * @var $users
 */

?>
<form name='accountSearchForm' id='accountSearchForm'>
<input id="searchAutoco" list="autoco" class='search' name='name' type='text' placeholder='Поиск...'>
    <datalist id="autoco">

    </datalist>
<button class='button' type="button" onclick="searchAccount($('#accountSearchForm .search').val());">Поиск</button>
</form>

<div class='overfltable'>
    <div class='button otst' onclick="sendAjax('/users/form/', 'GET')">Добавить нового пользователя</div>
    <br><br>
    <div class='fixheight'>
        <table class='akkt table table-bordered' border='1' cellpadding='5'>
            <thead>
            <tr>
                <th>№</th>
                <th>ФИО</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
                <th>Почта</th>
                <th>Фото</th>
                <th>Дата регистрации</th>
                <th>Персона</th>
                <th>Статус</th>
                <th>Филиал</th>
                <th></th>
            </tr>
            </thead>


            <?php if (isset($users['status'])) { ?>
            <tr>
                <td class='error'><?= $users['message'] ?></td>
            </tr>
            <?php } else {
                for ($i = 0; $i < count($users); $i++) { ?>
            <tr>

                <td class='robotocr'><?= $users[$i]->id ?></td>
                <td class='ralewayreg'><a href='<?= base_path() . "account/{$users[$i]->id}/" ?>'>
                        <?= $users[$i]->surname ?> <?= $users[$i]->first_name ?> <?= $users[$i]->patronymic ?></a>
                </td>
                <td class='robotocr'><?= (new DateTime($users[$i]->birthday))->Format('d.m.Y') ?></td>
                <td class='robotocr'><?= $users[$i]->phone ?></td>
                <td class='robotocr'><?= $users[$i]->email ?></td>

                <td class='robotocr'><img src='<?= GetImageURL($users[$i]->user_photo, ' user_photo') ?>'
                    width='37'/>
                </td>

                <td class='robotocr'><?= (new DateTime($users[$i]->reg_date))->Format('d.m.Y') ?></td>
                <td class='robotocr'><?= $users[$i]->ff_person_id ?></td>
                <td class='robotocr'><?= $users[$i]->user_type ?></td>
                <td class='ralewayreg'><?= $users[$i]->filial_name ?></td>
                <td>
                    <button class='button blueak otst' type="button"
                            onclick="sendAjax('/users/form/<?= $users[$i]->id ?>/', 'GET')">Изменить
                    </button>
                    <?php if ($users[$i]->user_type_id == 2 || $users[$i]->main_class == 2) { ?>
                    <button class='button greenak otst' type="button"
                            onclick="sendAjax('/workers/form/<?= $users[$i]->id ?>/', 'GET')">Сделать сотрудником
                    </button>
                    <?php } ?>
                    <a href='<?= base_path() ?>account/<?= $users[$i]->id ?>/'
                       style='color: white;'>
                        <button class='button grayak otst'>Профиль</button>
                    </a>

                </td>

            </tr>
            <?php }
            } ?>

        </table>
    </div>
</div>
<script>
    //запуск функции при прокрутке
    var str = '';

    $(window).scroll(function () {
        scrolling(str)
    });

    var count = 10;
    var begin = 0;

    function scrolling(str) {
        var currentHeight = $(".table").height();

        if ($(this).scrollTop() >= (currentHeight - $(this).height() - 100)) {

            $(this).unbind("scroll");

            load(str, 0);
        }
    }

    $('#searchAutoco').keyup(function (k) {
        var $this = $(this),
        val = $this.val();

            $(window).unbind("scroll");
            str = val;
            begin = 0;
            load(str, 1);
            $(window).scroll(function () {
                scrolling(str);
            });
    });

    var load = function (str, clear = 0) {
        $.ajax({
            type: "POST",
            url: "/account/load",
            data: {
                count: count,
                begin: begin * count,
                str: str
            },
            success: onAjaxSuccess
        });

        function onAjaxSuccess(data) {

            if(!clear) {
                $(".table").append(data.html);
            } else {
                $(".table").html(data.html);
            }

            $(window).on("scroll", function () {
                scrolling(str);
            });
        }

        begin++;
    }


</script>

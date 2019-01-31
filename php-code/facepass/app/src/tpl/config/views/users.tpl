<div id='settings_user' class='overfltable'>
    <div id='UsersTable'>
        <div class='overfltable'>
            <div class='button otst' onclick="sendAjax('/users/form/', 'GET')">Добавить нового пользователя</div><br><br>
        <div class='fixheight'>
            <table class='akkt' border='1' cellpadding='5'>
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
                    </tr>

                <?php if (isset($users['status'])) { ?>
                <tr>
                    <td class='error'><?= $users['message'] ?></td>
                    </tr>
                <?php } else { for ($i = 0; $i < count($users); $i++) { ?>
                <tr>

                    <td class='robotocr'><?= $users[$i]->id ?></td>
                    <td class='ralewayreg'><a href='" . base_path() . "account/<?= $users[$i]->id ?>/'><?= $users[$i]->surname.' '.$users[$i]->first_name.' '.$users[$i]->patronymic ?></a></td>
                <?php $date = new DateTime($users[$i]->birthday); ?>
                    <td class='robotocr'><?= $date->Format('d.m.Y') ?></td>
                    <td class='robotocr'><?= $users[$i]->phone ?></td>
                    <td class='robotocr'><?= $users[$i]->email ?></td>

                    <td class='robotocr'><img src='<?= GetImageURL($users[$i]->user_photo, 'user_photo')  ?>' width='37'/></td>

                <?php $date = new DateTime($users[$i]->reg_date); ?>
                    <td class='robotocr'><?= $date->Format('d.m.Y') ?></td>
                    <td class='robotocr'><img src='<?= base_path() ?>images/icons/chelovek2.PNG' class='bigIcon'></td>

                    <?php /** <td class='robotocr'><?= $users[$i]->video_identify_id ?></td> **/ ?>
                    <td class='robotocr'><?= $users[$i]->user_type ?></td>
                    <td class='ralewayreg'><?= $users[$i]->filial_name ?></td>

                    <td class='button blueak otst' onclick="sendAjax('/users/form/<?= $users[$i]->id ?>/', 'GET')">Изменить</td>
                <?php if ($users[$i]->user_type_id == 2 || $users[$i]->main_class == 2)  ?> <td class='button greenak otst' onclick="sendAjax('/workers/form/<?= $users[$i]->id ?>/', 'GET')">Сделать сотрудником</td>
                    <td class='button grayak otst'><a href='<?= base_path() ?>account/<?= $users[$i]->id ?>/' style='color: white;'>Профиль</a></td>
                    </tr>
                <?php
                }
                }
                ?>
                </table>

            </div>
        </div>
    </div>
</div>
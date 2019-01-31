
<div id='settings_worker' class='overfltable'>
    <div id='WorkersTable'>
        <table class='akkt' border='1' cellpadding='5'>
            <tr>
                <th>№</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Фамилия</th>
                <th>Логин</th>
                <th>Пароль</th>
                <th>Код</th>
                <th>№ доступа</th>
                <th>№ филиала</th>
                <th>Идентификатор пользователя</th>
                <th></th>
                </tr>

            <?php if (isset($workers['status'])) { ?>
            <tr>
                <td class='error'><?= $workers['message'] ?></td>
                </tr>
            <?php } else { for ($i = 0; $i < count($workers); $i++) { ?>
            <tr>
                <td><?= $workers[$i]->id ?></td>
                <td><?= $workers[$i]->first_name ?></td>
                <td><?= $workers[$i]->patronymic ?></td>
                <td><?= $workers[$i]->surname ?></td>
                <td><?= $workers[$i]->login ?></td>
                <td><?= $workers[$i]->password ?></td>
                <td><?= $workers[$i]->code ?></td>
                <td><?= $workers[$i]->permission_id ?></td>
                <td><?= $workers[$i]->filial_id ?></td>
                <td><?= $workers[$i]->user_id ?></td>
                <td class='button' onclick="sendAjax('/workers/form/edit/<?= $workers[$i]->id ?>/', 'GET')">Изменить</td>
                </tr>
            <?php
            }
            }
            ?>
            </table>
        </div>
    </div>
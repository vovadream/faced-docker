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

                        <td class='robotocr'><img src='<?= GetImageURL($users[$i]->user_photo, 'user_photo') ?>'
                                                  width='37'/></td>

                        <td class='robotocr'><?= (new DateTime($users[$i]->reg_date))->Format('d.m.Y') ?></td>
                        <td class='robotocr'><?= $users[$i]->ff_person_id ?></td>
                        <td class='robotocr'><?= $users[$i]->user_type ?></td>
                        <td class='ralewayreg'><?= $users[$i]->filial_name ?></td>
                        <td>
                            <button class='button blueak otst' type="button" onclick="sendAjax('/users/form/<?= $users[$i]->id ?>/', 'GET')">Изменить</button>
                            <?php if ($users[$i]->user_type_id == 2 || $users[$i]->main_class == 2) { ?>
                            <button class='button greenak otst' type="button"
                                    onclick="sendAjax('/workers/form/<?= $users[$i]->id ?>/', 'GET')">Сделать сотрудником</button>
                            <?php } ?>
                            <a href='<?= base_path() ?>account/<?= $users[$i]->id ?>/'
                               style='color: white;'><button class='button grayak otst'>Профиль</button></a>

                        </td>

                        </tr>
                <?php }
            } ?>


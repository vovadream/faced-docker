<div id='nullaccount' class='userData'>

    <div id='nullaccountin' class='userData'>
        <div class='buttonsControl'>
            <h2 class='inline'>Вход</h2>
            <div class='button white' title='Увеличить'><img class='bigIcon'
                                                             src='<?= base_path() ?>images/icons/zoom-in.jpg'></div>
            <div class='button white' title='Уменьшить'><img class='bigIcon'
                                                             src='<?= base_path() ?>images/icons/zoom-out.jpg'></div>
            <div class='button white' title='Развернуть на весь экран'
                 onclick="toggleClass('#nullaccountin','active');"><img class='bigIcon'
                                                                        src='<?= base_path() ?>images/icons/full-view.jpg'>
            </div>
        </div>


        <div id='nullAccountUserPhoto' class='userPhoto'>

            <img src='<?= base_path() ?>images/icons/Vhod_photo.PNG' class='bigIcon' \">

        </div>
        <form name='addNullAccountForm'>
            <table>
                <tr>
                    <td>Фамилия</td>
                    <td><input type='text' class='margins boryes' name='surname'></td>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td><input class='margins boryes' type='text' name='first_name'></td>
                </tr>
                <tr>
                    <td>Отчество</td>
                    <td><input class='margins boryes' type='text' name='patronymic'></td>
                </tr>

                <tr>
                    <td>Статус</td>
                    <td><select name='user_type_id'>
                            <?php if(isset($user_types['status'])) { ?>
                            <option value='0'>Нет данных</option>
                            <?php } else { ?>
                            <option value='0' disabled>Не выбран статус</option>
                            <?php for($i=0;$i <count($user_types); $i++) { ?>
                            if (($user_types[$i]->id==3)||($user_types[$i]->main_class==3)) { ?>
                            <option value='<?= $user_types[$i]->id?>'><?= $user_types[$i]->name?></option>
                            <?php
                        }
                        }
                        ?>
                        </select></td>
                </tr>
            </table>
        </form>
        <div class='button greenak margins'>Сфотографировать</div>
        <br>
        <div class='button greenak margins '>Добавить копированием</div>
        <br>
        <div class='button greenak margins '>Громкая связь</div>
        <br>
        <div class='button margins ' onclick="sendAjax('/nullaccount/add/', 'POST', 'addNullAccountForm')">Пропустить
        </div>

    </div>


    <div id='nullaccountout' class='userData'>
        <div class='buttonsControl' style='inline-block'>
            <h2 class='inline'>Вход</h2>
            <div class='button white' title='Увеличить'><img class='bigIcon'
                                                             src='<?= base_path() ?>images/icons/zoom-in.jpg'>
            </div>
            <div class='button white' title='Уменьшить'><img class='bigIcon'
                                                             src='<?= base_path() ?>images/icons/zoom-out.jpg'>
            </div>
            <div class='button white' title='Развернуть на весь экран'
                 onclick="toggleClass('#nullaccountin', 'active');"><img class='bigIcon'
                                                                         src='<?= base_path() ?>images/icons/full-view.jpg'>
            </div>
        </div>

        <select name='nullaccountid' onchange="sendAjax('/nullaccount/updateform/'+this.value+'/', 'GET')">
            <?php if(isset($nullaccount['status'])) { ?>
            <option value='0'>Нет данных</option>
            <?php } else { ?>
            <option value='0'>Не выбран нулевой аккаунт</option>
            <?php  for($i=0;$i<count($nullaccount);$i++) {
            if (($nullaccount[$i]->first_name == null)&&($nullaccount[$i]->patronymic == null)&&($nullaccount[$i]->surname == null)) { ?>
            <option value='{$nullaccount[$i]->user_id}'>Не были указаны данные при входе</option>
            <?php } else { ?>
            <option value='{$nullaccount[$i]->user_id}'>
                <?php if ($nullaccount[$i]->surname==null) { ?> [Нет фамилии]
                <?php  } else { ?> {$nullaccount[$i]->surname}

                <?php } if ($nullaccount[$i]->first_name==null) { ?> [Нет имени]
                <?php } else { ?> {$nullaccount[$i]->first_name}

                <?php } if ($nullaccount[$i]->patronymic==null) { ?> [Нет отчества]
                <?php } else { ?> {$nullaccount[$i]->patronymic} <?php } ?>
            </option>
            <?php
    }
    }
    }
    ?>
        </select>
        <div id='nullaccountoutform' class='userData'>
        </div>
    </div>
</div>
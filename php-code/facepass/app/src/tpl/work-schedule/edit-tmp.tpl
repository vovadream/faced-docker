<form id='DayTypesOnWeekForm'>
    <div id='topologyweekteplate'><br>
        <h2>Изменение шаблона для услуги <?= $hearing[0]->name ?></h2>
        <table style='display: block;'>
            <tbody>
            <tr>
                <td>ПН</td>
                <td><select name='daytype_monday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?= $i ?>'
                        <?php if ($hearing[0]->monday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>

            <tr>
                <td>ВТ</td>
                <td><select name='daytype_tuesday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->tuesday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>
            <tr>
                <td>СР</td>
                <td><select name='daytype_wednesday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->wednesday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>

            <tr>
                <td>ЧТ</td>
                <td><select name='daytype_thursday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->thursday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>

            <tr>
                <td>ПТ</td>
                <td><select name='daytype_friday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->friday_day_type == $i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>

            <tr>
                <td>СБ</td>
                <td><select name='daytype_saturday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->saturday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>

            <tr>
                <td>ВС</td>
                <td><select name='daytype_sunday'>
                        <?php for ($i=1;$i<count($day_types)+1;$i++) { ?>
                        <option value='<?=$i?>'
                        <?php if ($hearing[0]->sunday_day_type==$i) { echo 'selected';} ?>><?=$day_types[$i]?></option>
                        <?php } ?>
                        </select></td></tr>
            </tbody>
            </table>
        </div>
    <div class='polov'>
        <br><b>Рабочие дни</b>
        <table style='display: block;'>
            <tbody>
            <tr>
                <td>Начало приема</td>
                <td><input name='pass_in_fullday' type='time' value='<?= $hearing[0]->pass_in_work_day?>'></td>
                </tr>

            <tr>
                <td>Окончание приема</td>
                <td><input name='pass_out_fullday' type='time' value='<?= $hearing[0]->pass_out_work_day?>'></td>
                </tr>

            <tr>
                <td>Обед</td>
                <td>С <input name='dinner_start_fullday' type='time' value='<?= $hearing[0]->dinner_start_work_day?>'>
                    До <input name='dinner_end_fullday' type='time' value='<?= $hearing[0]->dinner_end_work_day?>'></td>
                </tr>

            <tr>
                <td>Вход сотрудника за</td>
                <td><input name='pass_before_fullday' type='number' value='<?= $hearing[0]->pass_before_work_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Выход сотрудника после</td>
                <td><input name='pass_after_fullday' type='number' value='<?= $hearing[0]->pass_after_work_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Блокировать печать пропусков за</td>
                <td><input name='stop_print_fullday' type='number' value='<?= $hearing[0]->stop_print_work_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Свободный вход/выход сотрудников</td>
                <td><input name='freepass_fullday' type='checkbox'
                    <?php if ($hearing[0]->free_pass_work_day) ?> checked></td>
                </tr>
            </tbody>
            </table>

        </div>

    <div class='polov'><b>Сокращенные дни</b>
        <table style='display: block;'>
            <tbody>
            <tr>
                <td>Начало приема</td>
                <td><input name='pass_in_limitedday' type='time' value='<?= $hearing[0]->pass_in_short_day?>'></td>
                </tr>

            <tr>
                <td>Окончание приема</td>
                <td><input name='pass_out_limitedday' type='time' value='<?= $hearing[0]->pass_out_short_day?>'></td>
                </tr>

            <tr>
                <td>Обед</td>
                <td>С <input name='dinner_start_limitedday' type='time' value='<?= $hearing[0]->dinner_start_short_day?>'>
                    До <input name='dinner_end_limitedday' type='time' value='<?= $hearing[0]->dinner_end_short_day?>'></td>
                </tr>

            <tr>
                <td>Вход сотрудника за </td>
                <td><input name='pass_before_limitedday' type='number' value='<?= $hearing[0]->pass_before_short_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Выход сотрудника после </td>
                <td><input name='pass_after_limitedday' type='number' value='<?= $hearing[0]->pass_after_short_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Блокировать печать пропусков за</td>
                <td><input name='stop_print_limitedday' type='number' value='<?= $hearing[0]->stop_print_short_day?>'> мин.</td>
                </tr>

            <tr>
                <td>Свободный вход/выход сотрудников</td>
                <td><input name='freepass_fullday_limitedday' type='checkbox'
                    <?php if ($hearing[0]->free_pass_short_day) ?> checked></td></tr>
            </tbody>
            </table>
        </div>
    </form>
<div class='button' onclick="sendAjax('/topology/service/update/<?= $id ?>/', 'POST','DayTypesOnWeekForm')">Сохранить изменения</div>
        
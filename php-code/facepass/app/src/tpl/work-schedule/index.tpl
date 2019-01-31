
<div id='workscheduletopology' class='userData'>
    <div class='buttonsControl'>
        <b class='marginright'>Формирование графиков</b>
        <form name='topologySearchForm' id='topologySearchForm' onsubmit="sendAjax('/topology/1/search/', 'POST', 'topologySearchForm'); return false;">
        <input class='poisktopology' type='text' name='name' placeholder='департамент/отдел/кабинет/график'>
        <button class='button'>Поиск</button>
        </form>


        </div>

    <div class='topology' id='leftopology'>
        <?php echo widget('TopologyMenuWidget', [
        'view' => 'schedule'
        ]); ?>
    </div>
    <div id='workschedulemake' class='userData'>
<?php
        /*
        <div id='workschedulecalendar'>
            $year=date('Y');
            for ($i=1;$i<13;$i++)
            {
            $HTML .="<input type='month' value='{$year}-
        if ($i<10) $HTML .="0
        $HTML .="{$i}'>
            }
            </div>
        */
?>
        <div id='workschedulebutton'>
            <?php if($id==null && $type==null && $category==null) { ?>
                <b style='color: red;'>Выберите в топологии нужную сущность.</b>
            <?php } else { ?>
                <div class='button' onclick="sendAjax('/workschedule/create/<?= $id ?>/', 'POST','workScheduleCreateForm')">Заполнить график</div>
            <?php } ?>
            </div>

        <form id='workScheduleCreateForm'>

            <b>Тип графика: </b>
            <select name='workscheduletypeselect' class='marginright' onchange="divSlide(null, 'body', '#allDates'); divSlide(null, 'body', '#calendarDates');">
            <option value='1' selected>Недельный</option>
            <option value='2'>Скользящий</option>
            </select>
            <div id='workschedulestartenddate'>
                <div id='allDates'>
                    Заполнить график С
                   <input type='date' name='start' value='<?= $currentYear?>-01-01' onchange="sendAjax('/workschedule/calendar/'+start.value+'/'+end.value+'/', 'GET')">
                    До
                    <input type='date' name='end' value='<?= $currentYear?>-12-30' onchange="sendAjax('/workschedule/calendar/'+start.value+'/'+end.value+'/', 'GET')">
                </div>
                <?php $monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']; ?>
                <div id='calendarDates' style='display: none;'>
                    <div>
                        <select name='dayType' id='dayType'>
                            <option value='0' data-color='transparent'>Убрать выделение</option>
                            <option value='1' data-color='red'>Рабочий день</option>
                            <option value='2' data-color='yellow'>Сокращенный день</option>
                            <option value='3' data-color='green'>Выходной день</option>
                        </select>
                    </div>

                    <?php while($date->format('Y')==$currentYear) {
                    $month = $date->format('m');
                    if($date->format('d')=="01") { ?>
                    <table class='calendar'>
                        <thead><tr><th colspan='7'><?= $monthNames[$date->format('m')-1] ?></th></tr></thead>
                        <thead>
                        <tr>
                            <th>Пн</th>
                            <th>Вт</th>
                            <th>Ср</th>
                            <th>Чт</th>
                            <th>Пт</th>
                            <th>Сб</th>
                            <th>Вс</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php }
                        $dayNum = date("N", mktime(0, 0, 0, $date->format('m'), $date->format('d'), $date->format('Y')));
                        if($date->format('d')=="01" || $dayNum==1) { ?>
                        <tr>
                            <?php for($i=1; $i<$dayNum; $i++) ?>
                            <td class='changedCell disabled'>-</td>
                            <?php } ?>
                            <td
                            <?php if($currentDate < $date) { ?>
                            data-dayid='<?= $date->format('dmY') ?>' class='changedCell enabled' onclick="selectCalendarDay(this)"
                            <?php } else { ?>
                            class='changedCell disabled'
                            <?php } ?>
                            >
                            <?= $date->format('j'); ?>
                            <input id='formDay<?= $date->format('dmY') ?>' type='hidden' value='0' name='<?= $date->format('dmY') ?>'>
                            </td>

                            <?php $date->modify('+1 day');
                            if($month!=$date->format('m') || $dayNum==7) { ?>
                        </tr>
                        <?php if($month!=$date->format('m')) { ?>
                        </tbody>
                    </table>
                    <?php }
                    }
                    } ?>

                </div>
                </div>

            <input type='radio' name='template_type' value='automatic' checked onchange="divSlide(this, '#manualTime', '.manualTime'); event.stopPropagation();">Автоматический
            <input type='radio' name='template_type' value='manual' onchange="divSlide(this, '#manualTime', '.manualTime'); event.stopPropagation();">Ручной<br>


            <div id='manualTime'>
                <div class='polov manualTime' style='display: none;'>
                    <br><b>Рабочие дни</b>
                    <table style='display: block;'>
                        <tbody>
                        <tr>
                            <td>Начало приема</td>
                            <td><input name='pass_in_fullday' type='time' value='08:00'></td>
                            </tr>

                        <tr>
                            <td>Окончание приема</td>
                            <td><input name='pass_out_fullday' type='time' value='16:00'></td>
                            </tr>

                        <tr>
                            <td>Обед</td>
                            <td>С <input name='dinner_start_fullday' type='time' value='12:00'> До <input name='dinner_end_fullday' type='time' value='13:00'></td>
                            </tr>

                        <tr>
                            <td>Вход сотрудника за</td>
                            <td><input name='pass_before_fullday' type='number' value='60'> мин.</td>
                            </tr>

                        <tr>
                            <td>Выход сотрудника после</td>
                            <td><input name='pass_after_fullday' type='number' value='90'> мин.</td>
                            </tr>

                        <tr>
                            <td>Блокировать печать пропусков за</td>
                            <td><input name='stop_print_fullday' type='number' value='15'> мин.</td>
                            </tr>

                        <tr>
                            <td>Свободный вход/выход сотрудников</td>
                            <td><input name='freepass_fullday' type='checkbox' checked></td>
                            </tr>
                        </tbody>
                        </table>
                    <?php
                        /*$HTML .="<br>Перерыв начиная с <input name='pause_from_fullday' type='time' value='10:00'>
                        $HTML .="<br>длительность <input name='pause_duration_fullday' type='time' value='00:15'>
                        $HTML .="<br>интервал <input name='pause_interval_fullday' type='time' value='02:00'>
                        */
                    ?>

                    </div>

                <div class='polov manualTime' style='display: none;'><b>Сокращенные дни</b>
                    <table style='display: block;'>
                        <tbody>
                        <tr>
                            <td>Начало приема</td>
                            <td><input name='pass_in_limitedday' type='time' value='09:00'></td>
                            </tr>

                        <tr>
                            <td>Окончание приема</td>
                            <td><input name='pass_out_limitedday' type='time' value='16:00'></td>
                            </tr>

                        <tr>
                            <td>Обед</td>
                            <td>С <input name='dinner_start_limitedday' type='time' value='12:00'> До <input name='dinner_end_limitedday' type='time' value='13:00'></td>
                            </tr>

                        <tr>
                            <td>Вход сотрудника за </td>
                            <td><input name='pass_before_limitedday' type='number' value='60'> мин.</td>
                            </tr>

                        <tr>
                            <td>Выход сотрудника после </td>
                            <td><input name='pass_after_limitedday' type='number' value='90'> мин.</td>
                            </tr>

                        <tr>
                            <td>Блокировать печать пропусков за</td>
                            <td><input name='stop_print_limitedday' type='number' value='15'> мин.</td>
                            </tr>

                        <tr>
                            <td>Свободный вход/выход сотрудников</td>
                            <td><input name='freepass_fullday_limitedday' type='checkbox' checked></td>
                            </tr>
                        <?php
                            /* $HTML .="<br>Перерыв начиная с <input name='pause_from_limitedday' type='time' value='10:00'>
                            $HTML .="<br>длительность <input name='pause_duration_limitedday' type='time' value='00:15'>
                            $HTML .="<br>интервал <input name='pause_interval_limitedday' type='time' value='02:00'>
                            */
                        ?>
                        </tbody>
                        </table>
                    </div>

                <div class='manualTime' style='display: none;'>
                    <table>
                        <tbody>
                        <tr>
                            <td>ПН</td>
                            <td><select name='daytype_monday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>
                            </tr>

                        <tr>
                            <td>ВТ</td>
                            <td><select name='daytype_tuesday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>
                            </tr>
                        <tr>
                            <td>СР</td>
                            <td><select name='daytype_wednesday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>
                            </tr>

                        <tr>
                            <td>ЧТ</td>
                            <td><select name='daytype_thursday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3'>Выходной день</option></td>
                            </tr>

                        <tr>
                            <td>ПТ</td>
                            <td><select name='daytype_friday'><option value='1'>Рабочий день</option>
                                    <option value='2' selected>Сокращенный день</option><option value='3'>Выходной день</option></td>
                            </tr>

                        <tr>
                            <td>СБ</td>
                            <td><select name='daytype_saturday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3' selected>Выходной день</option></td>
                            </tr>

                        <tr>
                            <td>ВС</td>
                            <td><select name='daytype_sunday'><option value='1'>Рабочий день</option>
                                    <option value='2'>Сокращенный день</option><option value='3' selected>Выходной день</option></td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>

           </div>
<?php
    /*
    <br><br><b>Неприемные дни</b>
    <br><br><b>Выходные дни</b>
    */
?>
    </form>
    </div>
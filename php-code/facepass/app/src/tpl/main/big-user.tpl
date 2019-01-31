<?php
/**
 * @var $type
 * @var $userInfo
 */

$viewInfoClass = (!empty($userInfo[0]->{'x-ray'}) || !empty($userInfo[0]->metal_detector)) ? "with-other" : "";
?>

<div class="main-user-view__container">
    <div class="main-user-view__photo">
        <div class="main-user-view__photo__content">
            <img src='<?= GetImageURL($userInfo[0]->user_photo, 'user_photo') ?>' class='main-user-view__photo__image'/>
            <div class='main-user-view__button-container'>
                <?php if ($type == "in") { ?>
                    <a class="button button__personal-card--main"
                       href='<?= base_path() . "account/{$userInfo[0]->user_id}/" ?>'>Личная карточка</a>
                    <a class='button gray button__silent-alarm--main'>Тихая тревога</a>
                <?php } else { ?>
                    <a class="button button__personal-card--main"
                       href='<?= base_path() . "account/{$userInfo[0]->user_id}/" ?>' style='width: 100%'>Личная карточка</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (!empty($userInfo[0]->{'x-ray'}) || !empty($userInfo[0]->metal_detector)) { ?>
        <div class="main-user-view__other">
            <div class="main-user-view__other__content">
                <div>
                    <?php if (!empty($userInfo[0]->{'x-ray'})) { ?>
                        <div class='reng'>
                            <img src='<?= base_path() . "images/icons/rentgen.PNG" ?>' class='bigIcon'>
                            <br>Рентген
                        </div>
                    <?php }

                    if (!empty($userInfo[0]->metal_detector)) { ?>
                        <div class='lotok'>
                            <img src='<?= base_path() . "images/icons/lotok.PNG" ?>' class='bigIcon'>
                            <br>Лоток<br> металлодетектора
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="main-user-view__info <?= $viewInfoClass ?>">
        <div class="main-user-view__info__content">
            <div>
                <?php
                $userInfo[0]->time_in = explode(".", $userInfo[0]->time_in);
                $userInfo[0]->time_in = $userInfo[0]->time_in[0];
                ?>

                <table>
                    <tbody>
                    <tr>
                        <td class='ralewaymed bordertd'>Дата/время прохода</td>
                        <td class='robotocr bordertd'>
                            <?php $date = new \DateTime($userInfo[0]->date_in);
                            echo $date->Format('d.m.Y') . "/" . $userInfo[0]->time_in ?>
                        </td>
                    </tr>
                    <tr class='ralewayreg bordertd'>
                        <td class='ralewaymed bordertd'>Фамилия</td>
                        <td class='ralewaybold bordertd'><?= $userInfo[0]->surname ?></td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Имя</td>
                        <td class='ralewaybold bordertd'><?= $userInfo[0]->first_name ?></td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Отчество</td>
                        <td class='ralewaybold bordertd'><?= $userInfo[0]->patronymic ?></td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Дата рождения</td>
                        <td class='robotocr bordertd'><?php $date = new \DateTime($userInfo[0]->birthday);
                            echo $date->Format('d.m.Y') ?></td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Напр-е</td>
                        <td class='ralewaymed bordertd'><?= $userInfo[0]->user_room_name ?></td>
                    </tr>
                    <?php
                    /*$HTML .= "
                    <tr>";
                        $HTML .= "
                        <td class='ralewaymed bordertd'>Категория доступа</td>
                        ";
                        $HTML .= "
                        <td class='ralewaymed bordertd'>А</td>
                        ";
                        $HTML .= "
                    </tr>
                    ";*/ ?>

                    <tr>
                        <td class='ralewaymed bordertd'>Доступно</td>
                        <td class='ralewaymed bordertd'>Доступно</td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Статус</td>
                        <td class='robotocr bordertd'><?= $userInfo[0]->user_type_name ?></td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Доступное время для входа</td>
                        <td class='robotocr bordertd'>c 8:00 по 18:00</td>
                    </tr>
                    <tr>
                        <td class='ralewaymed bordertd'>Доступное время для выхода</td>
                        <td class='robotocr bordertd'>c 8:00 по 18:00</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

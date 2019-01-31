<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FacePass</title>

    <link rel='stylesheet' href='<?php echo base_path();?>css/bootstrap/bootstrap.min.css'>
    <link rel='stylesheet' href='<?php echo base_path();?>css/bootstrap/datetimepicker.min.css'>
    <link rel='stylesheet' href='<?php echo base_path();?>css/style.css'>
    <link rel='stylesheet' href='<?php echo base_path();?>css/main.css'>

    <script>
        var domain = '<?php echo base_path(false);?>';
    </script>

    <script src='<?php echo base_path();?>js/jquery.js'></script>
    <script src="<?php echo base_path();?>js/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo base_path();?>js/bootstrap/moment.min.js"></script>
    <script src="<?php echo base_path();?>js/bootstrap/datetimepicker.min.js"></script>

    <script src='<?php echo base_path();?>js/script.js'></script>
    <script src='<?php echo base_path();?>js/tableheaders.min.js'></script>
    <script src='<?php echo base_path();?>js/notify.js'></script>
    <script src='<?php echo base_path();?>js/build.js'></script>


</head>
<body>
    <div id='popup'></div>
    <div id='header'>
        <?php if (isset($_SESSION['id'])) { ?>
            <?php echo $container->InterfaceView->getMenuView(); ?>
            <div class='logosk'><img id="logoimg" src="<?php echo base_path();?>images/logo.jpg"></div>

            <div class='advancedMenu'>
                <div class='menuItem noborder'>
                    <a class='menuItem noborder' href="<?php echo base_path();?>notifications/">
                        <img title='Оповещения' src='<?php echo base_path();?>images/icons/notifications.jpg' class='bigIcon'>
                        <span id="notify-count"></span>
                    </a>
                </div>

                <!--<div class='menuItem noborder' onclick="sendAjax('/messages/', 'GET')">
                    <img title='Сообщения' src='<?php echo base_path();?>images/icons/messages.jpg' class='bigIcon'>
                </div>-->

                <div class='menuItem'>
				 <a href='<?php echo base_path();?>nullaccount/'>
                    <img src='<?php echo base_path();?>images/icons/zero-acc.jpg' class='bigIcon'>
                    <br>Нулевой аккаунт
					</a>
                </div>

                <?php if(base_path().'topology/topologyadv/'==$_SERVER['REQUEST_URI']) { ?>
                    <div class='menuItem green active'>
                        <a href='<?php echo base_path();?>topology/topologyadv/'>
                            <img src='<?php echo base_path();?>images/icons/topology_active.jpg' class='bigIcon'>
                            <br>Топология
                        </a>
                    </div>
                <?php } else { ?>
                    <div class='menuItem'>
                        <a href='<?php echo base_path();?>topology/topologyadv/'>
                            <img src='<?php echo base_path();?>images/icons/topology.jpg' class='bigIcon'>
                            <br>Топология
                        </a>
                    </div>
                <?php } ?>

                <div id="showPeopleInBuilding"  class='menuItem'>
                    <img src='<?php echo base_path();?>images/icons/man-in.jpg' class='bigIcon'>
                    <br>Людей в здании
                </div>
                <div class='button white logout' onclick="sendAjax('/logout/');">
                    Выход <img class='icon' src='<?php echo base_path();?>images/icons/exit.jpg'>
                </div>
             </div>
        <?php } ?>
    </div>

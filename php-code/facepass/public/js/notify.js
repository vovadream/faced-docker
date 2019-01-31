function NotifyModule() {

    var $block = $("#notify-count"),
        $sound = $("#sound-notify")[0],
        $timer = 5000;

    if (!$block.length) {
        return false;
    }

    //Проверка на новые уведомления
    function NewNotify() {
        $.get(domain + '/notifications/new/', function (data) {
            if (data.count > 0) {
                $block.addClass('active');
                $block.text(data.count);
                $sound.play();
            } else {
                $block.removeClass('active');
            }
        });
        //Отправляем каждые 5 сек.
        setTimeout(NewNotify, $timer);
    }
    NewNotify();

    $("tr.notify.new").on('click', function () {
        $(this).removeClass('new');
    });
}


$(function() {
    NotifyModule();
});
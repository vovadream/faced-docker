<?php
//глобальные функции


/**
 * Небольшой шаблонизатор
 * @param $tpl
 * @param $data array передаваемые переменные
 * @param $dir string путь до папки с шаблонами
 * @return string|false
 */
function tpl($tpl, $data = [], $dir = '/src/tpl/')
{
    global $container;
    if (!is_array($data))
        return false;

    $name_tpl_file = __DIR__ . $dir . $tpl . '.tpl';
    if (!is_readable($name_tpl_file))
        return false;

    if (!empty($data))
        extract($data, EXTR_SKIP);

    ob_start();
    require $name_tpl_file;
    return ob_get_clean();
}

/**
 * Обёртка формирующая html страницу
 * TODO Лучше создать и вывести в класс шаблонизатора
 * @param $tpl_content
 * @param array $data
 * @return string
 */
function layout($tpl_content, $data = [])
{
    $tpl =  tpl('header');
    $tpl .= tpl($tpl_content, $data);
    $tpl .= tpl('footer');
    return $tpl;
}

/**
 * Вывод виджета
 * @param $widget
 * @param array $settings
 * @return string
 */
function widget($widget, $settings = [])
{
    global $container;
    $widget = 'App\Widgets\\'.$widget;
    $widget = new $widget($settings, $container);

    // Выполняем и выводим результат
    return $widget->run();
}

/**
 * Сохранение пришедшей картинки
 * @param $tmp_name
 * @param string $type
 * @return bool|string
 */
function SaveImage($tmp_name, $type = '/')
{
    global $container;

    if (substr($type, -1) != '/')
        $type .= '/';

    $settings = $container->get('settings');
    $storage = $settings['path_to_core_uploads'];
    $ext = '.jpg';

    //если нет такой папки то создаём
    if (!file_exists($storage . $type))
        mkdir($storage . $type);

    //проверка на существование имени файла
    gen_name:
    $filename = RandomString() . $ext;
    if (file_exists($storage . $type . $filename))
        goto gen_name;

    if (move_uploaded_file($tmp_name, $storage . $type . $filename)) {
        return $filename;
    }

    return false;
}

/**
 * Получение полного пути к картинке из закрытого upload
 * @param $name
 * @param $type
 * @param $local bool
 * @return bool|string
 */
function GetImageURL($name, $type, $local = false)
{
    global $container;

    $settings = $container->get('settings');
    $storage = $settings['path_to_core_uploads'];

    if (substr($type, -1) != '/')
        $type .= '/';

    if (!file_exists($storage . $type . $name) || is_null($name)) {
        //если не нашло то дефолтное изображение-заглушку
        if ($type == 'user_photo/' && !$local)
            return base_path() . 'images/icons/Vhod_photo.PNG';
        elseif ($type == 'documents/' && !$local)
            return base_path() . 'images/icons/doc1.PNG';
        else
            return false;
    }

    if($local) {
        return $storage . $type . $name;
    } else {
        return base_path() . 'img/' . $type . $name;
    }
}




/**
 * Случайная строка
 * @param int $length количество символов
 * @return string
 */
function RandomString($length = 32)
{
    return bin2hex(random_bytes($length));
}

/**
 * Псевдоним для получения абсолютного web пути сайта
 * @param $end_slash bool нужен ли последний слэш?
 * @return string
 */
function base_path($end_slash = true)
{
    global $container;
    $web_path = $container->get('settings')['web_path'];

    if ($end_slash)
        return $web_path;
    else
        return mb_substr($web_path, 0, -1);
}

/**
 * Псевдоним для получения абсолютного web пути папки загрузок
 * @return string
 */
function upload_path()
{
    global $container;
    return $container->get('settings')['web_path_to_uploads'];
}

/**
 * Псевдоним для получения абсолютного local пути папки загрузок
 * @return string
 */
function upload_core_path()
{
    global $container;
    return $container->get('settings')['path_to_core_uploads'];
}

/**
 * Псевдоним для получения абсолютного пути папки с модулями
 * @return string
 */
function module_path()
{
    global $container;
    return $container->get('settings')['path_to_module'];
}

/**
 * Псевдоним для получения url для проигрывателя стрима камеры
 * @return string
 */
function camplay_url()
{
    global $container;
    return $container->get('settings')['camplay_url'];
}

/**
 * Псевдоним для получения url для WebRTC APP
 * @return string
 */
function webrtc_url()
{
    global $container;
    return $container->get('settings')['webrtc_url'];
}

/**
 * Вернёт кадр с камеры
 * @param $stream
 * @return bool|string
 */
function saveImageFromRtsp($stream)
{
    $type = 'snapshots/';
    $storage = upload_core_path();
    $ext = '.jpg';

    //если нет такой папки то создаём
    if (!file_exists($storage . $type))
        mkdir($storage . $type);

    //проверка на существование имени файла
    gen_name_s:
    $filename = RandomString() . $ext;
    if (file_exists($storage . $type . $filename))
        goto gen_name_s;

    $savePath = $storage . $type . $filename;
    //$imageSize = "1920x1080";
    $imageSize = "640x420";

    $commandToSaveImage = "ffmpeg -i \"{$stream}\" -vsync cfr -r 1 -f image2 -s {$imageSize} -vframes 1 -qscale:v 2 \"{$savePath}\"";
    shell_exec($commandToSaveImage);

    if(file_exists($savePath)) {
        return $filename;
    } else {
        return false;
    }
}

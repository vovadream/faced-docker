<?php


namespace App\module\ffserver;

use \Slim\Container;

/**
 * Модуль для работы доп. сервера стримов видео для веба
 * Class ffserver
 * @package App\module\ffserver
 */
class ffserver
{
    /**
     * @var mixed настройки
     */
    private $settings;

    /**
     * @var string путь до шаблонов
     */
    private $tpl_path = '/src/module/ffserver/templates/';

    /**
     * @var string путь до папки логов
     */
    private $log_path;

    /**
     * @var int порт на котором разворачиваем сервер
     */
    private $port = 8888;

    /**
     * @var string путь до выходного конфига
     */
    private $config_path;

    public function __construct(Container $c)
    {
        ignore_user_abort();
        set_time_limit(0);

        $this->settings = $c->get('settings');
        $this->log_path = $this->settings['path_to_module'] . 'ffserver/logs/';
        $this->config_path = $this->settings['path_to_module'] . 'ffserver/config/';
    }

    /**
     * @param array $cams
     * @return bool
     */
    public function GenerateConfig($cams = [])
    {
        //проверяем папки на существование
        if (!$this->CheckPath($this->log_path)
            || !$this->CheckPath($this->config_path))
            return false;

        //подключаем шапку конфига
        $data['port'] = $this->port;
        $data['path_log'] = $this->log_path;
        $config_content = tpl('header', $data, $this->tpl_path);

        //подключаем все стримы
        foreach ($cams as $cam) {
            $data = [];
            $data['id'] = $cam->id;
            $data['rtsp'] = $cam->stream_url;
            $config_content .= tpl('stream', $data, $this->tpl_path);
        }

        file_put_contents($this->config_path . 'ffserver.conf', $config_content);

        $this->command_exec();

        return true;
    }

    /**
     * проверяем директории на существование
     * @param $path
     * @return bool
     */
    private function CheckPath($path)
    {
        if (!file_exists($path)) {
            if (!mkdir($path))
                return false;
        }

        return true;
    }

    /**
     * Команды для старта ffserver
     * @return bool
     */
    private function command_exec()
    {
        $conf = $this->config_path . 'ffserver.conf';
        $log = "> {$this->settings['path_to_core']}logs/";

        //если запущен сервер, то грохаем
        exec("killall ffserver");

        //запускаем сервер
        exec("nohup ffserver -f {$conf} {$log}ffserver.log 2>&1 &");

        return true;
    }

}
<?php

namespace App\Integration\Sigur;

use \Slim\Container;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;
use \Monolog\Logger;
use App\Models\WorkersModel;
use App\Models\UsersModel;
use App\Models\EquipmentModel;
use App\Controllers\HttpClientController;

/**
 * Для интеграции с СКУД Sigur - http://sigursys.com/
 * Class Controller
 * @package App\Integration\Sigur
 */
class Controller
{
    /**
     * Параметры для подключения
     */
    private $port = 3312;
    private $ip;
    private $login;
    private $password;
    private $eq_id;
    private $eq;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var WorkersModel
     */
    private $workers;

    /**
     * @var UsersModel
     */
    private $users;

    /**
     * @var EquipmentModel
     */
    private $equipment;

    /**
     * @var HttpClientController
     */
    private $client;

    /**
     * текущий socket resource
     *
     * @var resource
     */
    private $resource;

    /**
     * Controller constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $settings = $c->get('settings')['sigur'];
        $this->port = $settings['port'];
        $this->ip = $settings['ip'];
        $this->login = $settings['login'];
        $this->password = $settings['password'];
        $this->eq_id = $settings['eq_id'];

        $this->logger = $c->get('logger')->withName('Sigur');
        $this->workers = $c->get('WorkersModel');
        $this->users = $c->get('UsersModel');
        $this->equipment = $c->get('EquipmentModel');
        $this->client = $c->get('HttpClientController');
    }

    /**
     * Функция делегирования проходов
     * @param Request $request
     * @param Response $response
     * @return int
     */
    public function Delegation(Request $request, Response $response)
    {
        $delegation_start = "DELEGATION_START";
        $delegation_stop = "DELEGATION_STOP";

        $this->eq = $this->equipment->FindOne($this->eq_id, 'id');

        if ($this->connect() && $this->login()) {

            $this->write($delegation_start);

            if($this->read() == 'OK') {
                $this->logger->info("Делегирование началось.");
                $this->EndlessProcessed();
            } else {
                $this->logger->error("Делегирование не включилось, проверьте настройки сервера Sigur.");
            }

            $this->write($delegation_stop);
            if($this->read() == 'OK') {
                $this->logger->info("Делегирование закончено.");
            }
        }

        $this->close();

        return $response->getBody()->write('End processed.');
    }

    /**
     * Обработка запросов на делегирование прохода
     */
    private function EndlessProcessed()
    {
        $request = $this->read();
        $request = explode(' ', $request);

        if ($request[0] == 'DELEGATION_REQUEST') {
            $this->logger->info("Получен запрос на делегирование прохода.");

            $card_number = $request[3].' '.$request[4].' '.$request[5];
            $direction = strtolower($request[6]);
            $code = 255; //дефолтный код на разрешение открытия

            $verify = $this->workers->VerifyCode($card_number);
            //если невалидный код
            if (!$verify) {
                $code = 3;
                $this->client->SendTurnstile($this->eq, 0, 6);
            }

            //если всё ок - регистрируем проход
            if ($code == 255) {
                $access_id = null;

                $pass = $this->users->GetPassInFilial($verify->user_id, $access_id);
                if(!$pass)
                    $pass = $this->users->AddPass($verify->user_id, $access_id);
                else
                    $pass = $pass->id;
                $this->users->AddDirectionPass($pass, $direction);

                $this->client->SendTurnstile($this->eq, 1, 5);
            }

            $reply_command = "DELEGATION_REPLY {$request[1]} {$request[2]} {$code} 0 0 0 0";

            $this->write($reply_command);
            if($this->read() == 'OK') {
                $this->logger->info("Решение принято.");
                $this->EndlessProcessed();
            }

        } else {
            $this->logger->error("Не распознан формат запроса на делегирование.");
        }
    }

    /**
     * Создаём  TCP/IP сокет
     * @return bool
     */
    private function connect()
    {
        $this->resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!is_resource($this->resource)) {
            $this->logger->error("Не удалось выполнить socket_create(). Причина: " . socket_strerror(socket_last_error()) );
            return false;
        } else {
            $this->logger->info("socket_create(): OK");
        }

        //опции таймаута на чтение(не нужно) и отправку
        //socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 3, 'usec' => 0));
        socket_set_option($this->resource, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 3, 'usec' => 0));

        $this->logger->info("Пытаемся соединиться с '$this->ip' на порту '$this->port'...");

        $result = socket_connect($this->resource, $this->ip, $this->port);
        if ($result === false) {
            $this->logger->error("Не удалось выполнить socket_connect(). Причина: " . socket_strerror(socket_last_error($this->resource)) );
            return false;
        } else {
            $this->logger->info("socket_connect(): OK");
        }

        return true;
    }

    /**
     * Закрытие сокета
     */
    private function close()
    {
        $this->logger->info("Закрываем сокет...");
        socket_close($this->resource);
    }

    /**
     * Отправка команды на сокет
     * @param $msg
     * @return bool
     */
    private function write($msg)
    {
        $this->logger->info("Отправляем: ".$msg);

        $msg = $msg . PHP_EOL;
        socket_write($this->resource, $msg, strlen($msg));

        return true;
    }

    /**
     * Читаем из сокета
     * @return string
     */
    private function read()
    {
        while($buf = @socket_read($this->resource, 1024, PHP_NORMAL_READ))
            if($buf = trim($buf))
                break;

        $this->logger->info("Получено: ", [ $buf ]);

        return $buf;
    }

    /**
     * Команда авторизации на сокете
     * @return bool
     */
    private function login()
    {
        $login_command = "LOGIN 1.8 $this->login $this->password";

        $this->write($login_command);

        if($this->read() == 'OK') {
            $this->logger->info("Залогинились.");
            return true;
        } else {
            $this->logger->error("Неверный пароль или логин.");
            return false;
        }
    }
}
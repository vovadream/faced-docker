<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7;

use App\Models\WorkersModel;
use App\Models\EquipmentModel;
use App\Models\UsersModel;
use App\Models\LoggerModel;

/**
 * Для тестирования связи с устройствами и терминалом
 * Class HttpClientController
 * @package App\Controllers
 */
class HttpClientController
{
    /**
     * @var string Настройки сайта
     */
    private $settings;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EquipmentModel
     */
    private $equipment;

    /**
     * @var WorkersModel
     */
    private $workers;

    /**
     * @var UsersModel
     */
    private $users;

    /**
     * @var LoggerModel
     */
    private $logger;

    /**
     * HttpClientController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->settings = $c->get('settings');
        $this->client = $c->get('httpClient');
        $this->equipment = $c->get('EquipmentModel');
        $this->workers = $c->get('WorkersModel');
        $this->users = $c->get('UsersModel');
        $this->logger = $c->get('LoggerModel');
    }


    /**
     * Получение base64 картинки
     * @param $name
     * @param $type
     * @return string
     */
    private function ImageToBase64($name, $type)
    {
        $path = GetImageURL($name, $type, true);
        if (!$path)
            return null;

        $image = file_get_contents($path);
        $image = base64_encode($image);
        return $image;
    }

    /**
     * Человек подошёл к терминалу, отправка информации
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function GoToTheTerminal(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        $test_persona_id = $args['persona_id'];

        if(!$eq)
            $errors = 'Not found';

        if (!$errors) {
            $user_info = $this->users->GetOne($test_persona_id, 'ff_person_id');
            $data['personId'] = $test_persona_id;
            if ($user_info) {
                $data['userId'] = $user_info->id;
            } else {
                $data['userId'] = false;
            }

            if ($this->SendTerminal($eq, 'visitor', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }
        return $response->getBody()->write($result);
    }

    /**
     * Человек отошёл от терминала
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function OutTerminal(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        //TODO: Игорь - need to create service, to check

        if (!$errors) {

            if ($this->SendTerminal($eq, 'visitorleft'))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }
        return $response->getBody()->write($result);
    }

    /**
     * Отправка атоматического фото
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function AutoPhoto(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if (!$errors) {
            $data['personId'] = 1111;
            //test photo - bad path
            //$data['photo'] = $this->ImageToBase64('../../public/images/icons/akk-photo.PNG', 'user_photo');
            $photo_test = $this->settings['path_to_core'].'public/images/icons/akk-photo.PNG';
            $image = file_get_contents($photo_test);
            $data['photo'] = base64_encode($image);

            if ($this->SendTerminal($eq, 'gettingphoto', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }
        return $response->getBody()->write($result);
    }

    /**
     * Ответ на запрос сравнения документа от охранника
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function ResponseCompare(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $bool = (int)$args['bool'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if (!$errors) {
            if ($bool)
                $data['result'] = true;
            else
                $data['result'] = false;

            if ($this->SendTerminal($eq, 'compareresult', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }
        return $response->getBody()->write($result);
    }

    /**
     * Ответ на запрос сравнения документа от охранника
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function ResponseCompareFaces(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $bool = (int)$args['bool'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if (!$errors) {
            if ($bool)
                $data['result'] = true;
            else
                $data['result'] = false;

            if ($this->SendTerminal($eq, 'comparefaceresult', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }
        return $response->getBody()->write($result);
    }

    /**
     * Запрос на остановку терминала - ЧП
     * @param Request $request
     * @param Response $response
     * @return int
     */
    public function HaltTerminal(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $block = (int)$args['block'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if(!$errors) {
            if ($block)
                $data['blocking'] = true;
            else
                $data['blocking'] = false;

            if ($this->SendTerminal($eq, 'emergencyblocking', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }

        return $response->getBody()->write($result);
    }

    /**
     * Отправка комнаты для подключения охраннику
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function ConnectTerminal(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if(!$errors) {
            $room = RandomString(8);
            $data['roomUrl'] = "{$this->settings['webrtc_url']}r/{$room}?type=screen";

            if ($this->SendTerminal($eq, 'assistantconnected', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }

        return $response->getBody()->write($result);
    }

    /**
     * Установка курсора охранника на терминале
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     */
    public function SetPointer(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if(!$errors) {
            $data['x'] = rand(1, 100);
            $data['y'] = rand(1, 100);

            if ($this->SendTerminal($eq, 'helperpointer', $data))
                $result = 'success';
            else
                $result = 'Bad';
        } else {
            $result = $errors;
        }

        return $response->getBody()->write($result);
    }


    /**
     * Запрос к терминалу на открытие турникета
     * @param Request $request
     * @param Response $response
     */
    public function OpenTurnstile(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $eq = $this->equipment->FindOne($id, 'id');
        $errors = false;

        if(!$eq)
            $errors = 'Not found';

        if (!$errors) {

            //Первое посещение, попытка пройти без пропуска
            $this->SendTurnstile($eq, 0, 1);

            //Повторное  посещение, попытка пройти без пропуска
            $this->SendTurnstile($eq, 0, 2);

            //Ошибка идентификации по камере: камера не смогла распознать человека, приложившего зарегистрированную карту
            //вернём в ответе на запрос верификации кода
            //$this->SendTurnstile($eq, 0, 3);

            //Пристав не подтвердил соответствие проходящего человека и лица с аккаунта
            //вернём в ответе на запрос верификации кода
            //$this->SendTurnstile($eq, 0, 4);

            //Пристав подтвердил соответствие проходящего человека и лица с аккаунта
            $this->SendTurnstile($eq, 1, 5);

            //Использование устаревшего пропуска
            //вернём в ответе на запрос верификации кода
            //$this->SendTurnstile($eq, 0, 6);

            //Время действия пропуска не наступило
            //вернём в ответе на запрос верификации кода
            //$this->SendTurnstile($eq, 0, 7);

            //нулевой аккаунт если в интерфейсе нажали открыть.
            $this->SendTurnstile($eq, 1, 0);

        }

    }

    /**
     * Отправка запроса на контроллер турникета
     * @param $eq object equipment
     * @param int $open
     * @param int $audio
     * @param int $direction block=0|in=1|out=2
     * @return bool
     */
    public function SendTurnstile($eq, $open = 1, $audio = 0, $direction = 1)
    {
        try {
            $this->client
                ->request('POST', 'http://'.$eq->ip_adress.':84/', [
                    'connect_timeout' => 3,
                    'form_params' => [
                        'open' => $open,
                        'audio' => $audio,
                        'direction' => $direction,
                    ]]);
            return true;
        } catch (RequestException $e) {
            //поймали ошибку, решаем что с ней делать
            $this->equipment->Update($eq->id, 'FALSE', 'active');
            $this->workers->AddNotification(null, 'HttpClient. Связь с устройством пропала.', 2, $eq->id);
            $this->logger->Add([
                'equipment_id' => $eq->id,
                'message' => "HttpClient. Связь с {$eq->name} пропала.",
            ]);
            return false;
        }
    }

    /**
     * Отправка запроса к терминалу
     * @param $eq object equipment
     * @param $operation string
     * @param $data array
     * @return bool|object
     */
    public function SendTerminal($eq, $operation, $data = [])
    {
        try {
            $rq = $this->client
                ->request('POST', 'http://'.$eq->ip_adress.':3030/'.$operation, [
                    'connect_timeout' => 3,
                    'json' => $data
                ]);

            $obj = json_decode($rq->getBody());
            if ($obj && $obj->status == 'success') {
                return true;
            } else {
                $this->logger->Add([
                    'equipment_id' => $eq->id,
                    'message' => "HttpClient. Некорректный ответ от устройства {$eq->name}.",
                    'debug' => var_export($obj, true)
                ]);
                return false;
            }
        } catch (RequestException $e) {
            //поймали ошибку, решаем что с ней делать
            $this->equipment->Update($eq->id, 'FALSE', 'active');
            $this->workers->AddNotification(null, 'HttpClient. Связь с терминалом пропала.', 2, $eq->id);
            $this->logger->Add([
                'equipment_id' => $eq->id,
                'message' => "HttpClient. Связь с {$eq->name} пропала.",
            ]);
            return false;
        }
    }

    /**
     * Метод для проверки связи с устройствами
     * @param Request $request
     * @param Response $response
     * @return int
     */
    public function Pinging(Request $request, Response $response)
    {
        $log = 'Start: '.date_create()->format('Y-m-d H:i:s').PHP_EOL;

        $all = $this->equipment->getAll();
        $promises = (function () use ($all) {
            foreach ($all as $item) {
                $port = '';
                if($item->type_id == 3)
                    $port = ':84';
                if($item->type_id == 1)
                    $port = ':3030';

                yield $this->client->requestAsync(
                    'GET',
                    'http://'.$item->ip_adress.$port.'/',
                    ['connect_timeout' => 3]
                )->then(
                    //если успешно отработало
                    function (Response $res) use ($item) {
                        if($item->active && $res->getStatusCode() != 200) {
                            $this->equipment->Update($item->id, 'FALSE', 'active');
                            $this->workers->AddNotification(null, "Связь с устройством {$item->name} пропала.(CLI)(Неверный статус)", 2, $item->id);
                        }
                        if(!$item->active && $res->getStatusCode() == 200) {
                            $this->equipment->Update($item->id, 'TRUE', 'active');
                            $this->workers->AddNotification(null, "Связь с устройством {$item->name} восстановлена.(CLI)", 2, $item->id);
                        }
                    },
                    //если недостучалось
                    function (RequestException $e) use ($item) {
                        if($item->active) {
                            $this->equipment->Update($item->id, 'FALSE', 'active');
                            $this->workers->AddNotification(null, "Связь с устройством {$item->name} пропала.(CLI)({$e->getMessage()})", 2, $item->id);
                        }
                    }
                );
            }
        })();

        \GuzzleHttp\Promise\settle($promises)->wait();

        $log .= 'End: '.date_create()->format('Y-m-d H:i:s').PHP_EOL;
        return $response->getBody()->write($log);
    }
}

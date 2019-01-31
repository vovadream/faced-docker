<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

use App\Models\EquipmentModel;
use App\Models\WorkersModel;
use App\Models\UsersModel;
use App\Models\LoggerModel;
use App\Models\DocumentsModel;
use App\Models\FilialModel;

/**
 * Specification http://labs.omniti.com/labs/jsend
 * Class ApiController
 * @package App\Controllers
 */
class ApiController
{

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
     * @var DocumentsModel
     */
    private $documents;

    /**
     * @var FilialModel
     */
    private $filial;

    /**
     * @var string Настройки сайта
     */
    private $settings;

    /**
     * @var HttpClientController
     */
    private $client;

    /**
     * @var array Errors
     * code => message
     */
    private $errors = [
        111 => 'Bad mark',
        112 => 'Bad direction',
        113 => 'Mark is expired',
        114 => 'Unknown type',
        115 => 'Not valid',
        116 => 'Not found',
        117 => 'Empty value',
        118 => 'Not found equipment',
        119 => 'Image not uploaded',
        120 => 'Not allowed parameter',
        121 => 'User not found',
        122 => 'Array only',
        123 => 'Connection is failed',
        124 => 'Cant start service',
        125 => 'Video stream not identify person',
        126 => 'Wrong person',
        127 => 'Camera not found',
    ];

    /**
     * ApiController constructor.
     * @param \Slim\Container $c
     */
    public function __construct(Container $c)
    {
        $this->settings = $c->get('settings');
        $this->equipment = $c->get('EquipmentModel');
        $this->workers = $c->get('WorkersModel');
        $this->users = $c->get('UsersModel');
        $this->logger = $c->get('LoggerModel');
        $this->documents = $c->get('DocumentsModel');
        $this->filial = $c->get('FilialModel');
        $this->client = $c->get('HttpClientController');
    }

    /**
     * Обёртка для ответа
     * @param null|array $data
     * @param string $status = success|fail
     * @return array
     */
    private function response($data = null, $status = 'success')
    {
        $array = [
            'status' => $status,
            'data' => $data,
        ];

        return $array;
    }

    /**
     * Проверка на пустоту массива
     * @param $data
     * @param $not_empty - массив названий обязательных полей
     * @return array|bool
     */
    private function IsEmpty($data, $not_empty)
    {
        $errors = false;
        foreach ($not_empty as $item) {
            if (empty($data[$item]))
                $errors[$item] = $this->errors[117];
        }
        return $errors;
    }

    /**
     * Сохранение картинки из base64
     * @param $data
     * @param string $folder
     * @return bool|string
     */
    private function SaveImageFromBase64($data, $folder = '/')
    {
        $ext = '.jpg';
        $storage = $this->settings['path_to_core_uploads'];
        
        //убираем лишнее и раскодируем
        $data = str_replace("data:image/jpeg;base64,", "", $data);
        $image = base64_decode($data);

        //если нет такой папки то создаём
        if (!file_exists($storage.$folder))
            mkdir($storage.$folder);

        //проверка на существование имени файла
        gen_name:
        $filename = RandomString() . $ext;
        if(file_exists($storage.$folder.$filename))
            goto gen_name;

        if(file_put_contents($storage . $folder . $filename, $image) !== FALSE) {
            return $filename;
        }

        return false;
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
        $image = 'data:image/jpeg;base64,'.$image;
        return $image;
    }

    /**
     * Запрос на открытие турникета
     * @var $direction = in|out|block
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function OpenTurnstile(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $input = $request->getParsedBody();

        $mark = $input['mark']; //код
        $direction = $input['direction']; //Направление
        $worker = (int)$input['worker']; //Работник ли это

        $errors = false;
        $direction_default = ['in', 'out', 'block'];
        $mark_remote_controller = 'console'; //стандартная метка пульта управления

        //проверка на валидность направления
        if (!in_array($direction, $direction_default)) {
            $errors['direction'] = $this->errors[112];
        }

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        // исключительная ситуация - открытие турникета с пульта
        if ($mark == $mark_remote_controller) {

            $this->logger->Add([
                'message' => "API. Турникет открыт пультом.({$direction})",
                'equipment_id' => $eq->id
            ]);

            $result = $this->response([
                'audio' => 0,
            ]);
        } else {

            if($worker)
                $verify = $this->workers->VerifyCode($mark);
            else
                $verify = $this->users->VerifyCode($mark);

            //если невалидный код
            if (!$verify) {
                $errors['audio'] = 6;
                $errors['error'] = $this->errors[111];
            }

            //если лицо в камере не совпадает с тем кто даёт пропуск
            if ($verify && $eq) {
                $turnstile = $this->equipment->getTurnstile($eq->id);

                if($turnstile['status'] == 'success' &&
                    $turnstile['data']->camera_in
                ) {
                    //если смогли идентифицировать и чужой пропуск
                    if (($direction == 'in' &&
                        $verify->ff_person_id != $turnstile['data']->camera_in->ff_person_id) ||
                        ($direction == 'out' &&
                        $verify->ff_person_id != $turnstile['data']->camera_out->ff_person_id)
                    ) {
                        $errors['audio'] = 4;
                        $errors['error'] = $this->errors[126];
                    }

                    //если не смогли идентифицировать человека перед камерой
                    if (($direction == 'in' &&
                            is_null($turnstile['data']->camera_in->ff_person_id)) ||
                        ($direction == 'out' &&
                            is_null($turnstile['data']->camera_out->ff_person_id))
                    ) {
                        $errors['audio'] = 3;
                        $errors['error'] = $this->errors[125];
                    }
                }
            }

            //если дата события истекла у посетителя
            $date = date_create()->format('Y-m-d');
            if ($verify && !$worker && !is_null($verify->hdate) && $verify->hdate != $date) {
                $errors['audio'] = 6;
                $errors['error'] = $this->errors[113];
            }

            if ($errors) {
                $result = $this->response($errors, 'fail');
            } else {
                if($worker)
                    $access_id = null;
                else
                    $access_id = $verify->id;

                $pass = $this->users->GetPassInFilial($verify->user_id, $access_id);
                if(!$pass)
                    $pass = $this->users->AddPass($verify->user_id, $access_id);
                else
                    $pass = $pass->id;
                $this->users->AddDirectionPass($pass, $direction);

                $result = $this->response([
                    'audio' => 5,
                    'id' => $pass,
                ]);
            }

        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Получение конфигурации от устройства
     * @var $type = terminal|doorcontroller|checkpoint
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function AddDeviceConfig(Request $request, Response $response)
    {
        $input = $request->getParsedBody();

        $ip = $request->getAttribute('ip_address');
        $mac = $input['mac'];
        $type = $input['type']; //тип устройства

        $errors = false;
        $type_id = 0;

        //Валидация ip
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $errors['ip'] = $this->errors[115];
        }

        //Валидация mac
        if (!filter_var($mac, FILTER_VALIDATE_MAC)) {
            $errors['mac'] = $this->errors[115];
        }

        //проверка на тип
        switch ($type) {
            case 'terminal':
                $type_id = 1;
                break;
            case 'doorcontroller':
                $type_id = 4;
                break;
            case 'checkpoint':
                $type_id = 3;
                break;
            default:
                $errors['type'] = $this->errors[114];
        }

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $FindByMac = $this->equipment->FindOne($mac);
            if ($FindByMac) {
                $id = $FindByMac->id;
                $this->equipment->Update($id, $ip);

                $this->logger->Add([
                    'message' => 'API. Автоматически обновлена конфигурация оборудования.',
                    'equipment_id' => $id,
                ]);
            } else {
                $id = $this->equipment->Add($ip, $mac, $type_id);
                //При первом добавление назначается имя устройства
                $find_type = $this->equipment->GetType($type_id);
                $type_name = $find_type->name . ' ' . $id;
                $this->equipment->Update($id, $type_name, 'name');

                $this->logger->Add([
                    'message' => 'API. Автоматически добавлено оборудование.',
                    'equipment_id' => $id,
                ]);
            }
            $input['id'] = $id;
            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Отправка серверного времени
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function GetTime(Request $request, Response $response)
    {
        $request->getParsedBody();
        $date['time'] = date_create()->format('d-m-Y H:i:s');
        $response = $response->withJson($this->response($date));
        return $response;
    }

    /**
     * Добавление события
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function Log(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $input = $request->getParsedBody();

        $msg = $input['message'];
        $type = $input['type']; //тип ошибки/события

        $errors = false;
        $type_id = 0;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if (empty($msg))
            $errors['message'] = $this->errors[117];

        switch ($type) {
            case 'notification':
                $type_id = 1;
                break;
            case 'error':
                $type_id = 2;
                break;
            case 'alert':
                $type_id = 6;
                break;
            default:
                $errors['type'] = $this->errors[114];
        }

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $input['id'] = $this->workers->AddNotification(null, $msg, $type_id, $eq->id);
            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Получение иерархического списка департаментов и услуг
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function Departments(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $filial_id = 1;
        $errors = false;

        $FindByIP = $this->equipment->FindOne($ip, 'ip_adress');
        if ($FindByIP) {
            $filial_id = $FindByIP->filial_id;
        } else {
            $errors['ip'] = $this->errors[118];
        }

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $departments = $this->filial->GetDepartments($filial_id);
            $structure = $this->FormatThree($departments);
            $arr['structure'] = $structure;
            //$arr['structure'] = $this->filial->GetTerminalTopology($filial_id);
            $result = $this->response($arr);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Рекурсивное формирование дерева отделов
     * @param $tree
     * @param null $parent
     * @return array
     */
    private function FormatThree($tree, $parent = null)
    {
        $tree_new = [];$i=0;
        foreach ($tree as $item) {
            if ($item['parent_id'] == $parent) {
                $tree_new[$i] = $item;
                //$tree_new[$i]['children'] = $this->FormatThree($tree, $item['id']);
                $tree_new[$i]['children'] = array_merge(
                    $this->FormatThree($tree, $item['id']),
                    $this->filial->GetHearing($item['id'])
                );
                $i++;
            }
        }

        return $tree_new;
    }

    /**
     * Запрос на помощь
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function RequestHelp(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $msg = $eq->name." запрашивает помощь.";
            $type_id = 3; //help
            $input['id'] = $this->workers->AddNotification(null, $msg, $type_id, $eq->id);
            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Запрос на сравнение ручного ввода и скана документа
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function RequestCompare(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');

        $not_empty = ['type', 'surname', 'first_name', 'patronymic', 'series_number', 'gender', 'scans'];
        $errors = $this->IsEmpty($input, $not_empty);

        if ((int)$input['type'] != 1)
            $errors['type'] = $this->errors[114];

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if (!is_array($input['scans']))
            $errors['scans'] = $this->errors[122];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            //сохранение сканов
            $images = [];
            foreach ($input['scans'] as $scan) {
                $images[] = $this->SaveImageFromBase64($scan, 'documents/');
            }
            $input['scans'] = $images;

            $type_id = 4; //compare
            $msg = tpl('api/compare', $input);
            $input['id'] = $this->workers->AddNotification(null, $msg, $type_id, $eq->id);
            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Запрос на сравнение лица
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function CompareFaces(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');

        $not_empty = ['id_person', 'scan'];
        $errors = $this->IsEmpty($input, $not_empty);

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        $camera = $this->equipment->GetTerminalCamera($eq->id);
        if(!$camera)
            $errors['camera'] = $this->errors[118];

        if ($errors) {
                $result = $this->response($errors, 'fail');
        } else {
            //сохранение скана
            $input['scan'] = $this->SaveImageFromBase64($input['scan'], 'documents/');
            $input['scan'] = GetImageURL($input['scan'], 'documents');

            //Image
            $img = saveImageFromRtsp($camera->stream_url);
            $input['photo'] = GetImageURL($img, 'snapshots');

            $type_id = 5; //compare
            $msg = tpl('api/compare-face', $input);
            $input['id'] = $this->workers->AddNotification(null, $msg, $type_id, $eq->id);
            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Запрос на получение стрима камеры
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function GetStreamCam(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        $camera = $this->equipment->GetTerminalCamera($eq->id);
        if(!$camera)
            $errors['stream'] = $this->errors[127];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $a['stream'] = camplay_url() . "s{$camera->id}.mjpeg";
            $a['id'] = $this->logger->Add([
                'message' => 'API. Запрос на получение потока терминала.',
                'equipment_id' => $eq->id,
            ]);
            $result = $this->response($a);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Новый метод для отправки фото
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function MakePhoto(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $errors = false;
        $img = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        $camera = $this->equipment->GetTerminalCamera($eq->id);
        if (!$camera)
            $errors['camera'] = $this->errors[127];

        if ($camera) {
            $img = saveImageFromRtsp($camera->stream_url);
            if (!$img)
                $errors['rtsp'] = $this->errors[119];
        }


        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {

            $data = [
                'personId' => $camera->ff_person_id,
                'photo' => $this->ImageToBase64($img, 'snapshots')
            ];
            $this->client->SendTerminal($eq, 'gettingphoto', $data);

            $this->logger->Add([
                'message' => 'API. Запрос на ручное фото.',
                'equipment_id' => $eq->id,
            ]);

            $result = $this->response([]);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Отдаёт данные идентификации по фото(гадает по фото:))
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function Verify(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $photo = $input['photo'];
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        //сохраняем изображение
        $photo = $this->SaveImageFromBase64($photo, 'documents/');
        $photo = GetImageURL($photo, 'documents/', true);

        if (!$photo)
            $errors['image'] = $this->errors[119];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            //здесь к сдк обращения для того чтоб узнать персону
            $id_person = 179;

            $user = $this->users->GetOne($id_person, "ff_person_id");

            $data = [
                'id_person' => $id_person,
                'id_user' => isset($user->id) ? $user->id : false
            ];

            $result = $this->response($data);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Добавление посетителя
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function AddUser(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');

        $not_empty = ['id_person', 'surname', 'first_name', 'patronymic', 'user_photo'];
        $errors = $this->IsEmpty($input, $not_empty);

        //определяем устройство по ипу и добавляем в дату филиал
        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if ($eq) {
            $input['filial_id'] = $eq->filial_id;
        } else {
            $errors['ip'] = $this->errors[118];
        }

        if($this->users->GetOne($input['id_person'], 'ff_person_id'))
            $errors['id_person'] = $this->errors[115];

        //сохраняем изображение
        $input['user_photo'] = $this->SaveImageFromBase64($input['user_photo'], 'user_photo/');
        if (!$input['user_photo'])
            $errors['user_photo'] = $this->errors[119];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $input['user_type_id'] = 2;
            $input['id'] = (int)$this->users->Add($input);
            $result = $this->response($input);

            //добавляем приглашения если есть
            $searchInv = $this->users->SearchGuests($input);
            if ($searchInv) {
                foreach ($searchInv as $item){
                    $this->users->InviteGuestAttach($input['id'], $item->id);
                    $this->users->AddInvitee($input['id'], $item->hearing_id);
                }
            }

            $this->logger->Add([
                'message' => 'API. Зарегистрирован посетитель.',
                'equipment_id' => $eq->id,
                'user_id' => $input['id'],
            ]);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Обновленние данных пользователя
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function UpdateUser(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');
        $id = (int)$args['id'];

        $errors = false;
        $allowed_params = ['surname', 'first_name', 'patronymic',
            'email', 'phone', 'birthday',
            'user_photo', 'work_place', 'work_position'];

        foreach ($input as $key => $v) {
            if (!in_array($key, $allowed_params))
                $errors[$key] = $this->errors[120];
        }

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if(!$this->users->GetOne($id))
            $errors['user_id'] = $this->errors[121];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $this->users->Update($input, $id);
            $result = $this->response($input);

            $this->logger->Add([
                'message' => 'API. Обновлена информация посетителя.',
                'equipment_id' => $eq->id,
                'user_id' => $id,
            ]);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Получение информации о посетителе
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function GetUser(Request $request, Response $response, $args)
    {
        $ip = $request->getAttribute('ip_address');
        $user_id = (int)$args['id'];
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        $user = $this->users->GetOne($user_id);
        if (!$user)
            $errors['user_id'] = $this->errors[121];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            //формируем все данные юзера
            $info = new \stdClass();
            $info->id = $user->id;
            $info->email = $user->email;
            $info->phone = $user->phone;
            $info->surname = $user->surname;
            $info->first_name = $user->first_name;
            $info->patronymic = $user->patronymic;
            $info->work_place = $user->work_place;
            $info->work_position = $user->work_position;
            $info->photo = $this->ImageToBase64($user->user_photo, 'user_photo');

            $info->documents[0] = $this->documents->GetPassportRF($user_id);
            if($info->documents[0]!=null) {
                $info->documents[0]->page_one = $this->ImageToBase64($info->documents[0]->page_one, 'documents');
                $info->documents[0]->page_two = $this->ImageToBase64($info->documents[0]->page_two, 'documents');
                $info->documents[0]->page_three = $this->ImageToBase64($info->documents[0]->page_three, 'documents');
                $info->documents[0]->type = 1;
            }
            $result = $this->response($info);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Добавление документа к пользователю
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function AddDocument(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');
        $input['user_id'] = $args['id'];

        $not_empty = ['type', 'surname', 'first_name', 'patronymic', 'series_number', 'gender', 'scans'];
        $errors = $this->IsEmpty($input, $not_empty);

        if(!$this->users->GetOne($input['user_id']))
            $errors['user_id'] = $this->errors[121];

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ((int)$input['type'] != 1)
            $errors['type'] = $this->errors[114];

        if (is_array($input['scans'])) {
            //сохранение сканов
            $images = [];
            foreach ($input['scans'] as $scan) {
                $images[] = $this->SaveImageFromBase64($scan, 'documents/');
            }
            $input['scans'] = $images;

            if (empty($input['scans']))
                $errors['scans'] = $this->errors[119];
        } else
            $errors['scans'] = $this->errors[122];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $input['id'] = (int)$this->documents->AddPassportRF($input);
            $result = $this->response($input);

            $this->logger->Add([
                'message' => 'API. Добавлен паспорт посетителя.',
                'equipment_id' => $eq->id,
                'user_id' => $input['user_id'],
            ]);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Персональные приглашения пользователя на слушанья
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function GetInvitees(Request $request, Response $response, $args)
    {
        $user_id = $args['id'];
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        if(!$this->users->GetOne($user_id))
            $errors['id'] = $this->errors[121];

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $invitees = $this->users->GetInviteeByUser($user_id);
            $i = 0;
            $departments = $this->filial->GetDepartments($eq->filial_id);
            foreach ($invitees as $item) {
                $d = $this->GetHeadParent($departments, $item['departament_id']);
                $invitees[$i]['department_name'] = $d['name'];
                $invitees[$i]['division_name'] = $this->filial->GetOneDepartment($item['departament_id'])->name;
                $invitees[$i]['number'] = 1;
                unset($invitees[$i]['departament_id']);
                $i++;
            }
            $arr['invitees'] = $invitees;
            $result = $this->response($arr);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Рекурсивный поиск главного родителя
     * @param $arr
     * @param $id
     * @return array|bool
     */
    private function GetHeadParent($arr, $id)
    {
        foreach ($arr as $item) {
            if ($item['id'] == $id) {
                if ($item['parent_id'] == null || $item['parent_id'] == 0) {
                    return $item;
                }
                return $this->GetHeadParent($arr, $item['parent_id']);
            }
        }

        return false;
    }

    /**
     * Отправка данных для печати пропуска
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function GetPass(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $user_id = $args['id'];
        $invitee_id = isset($input['invitee_id'])?(int)$input['invitee_id']:null;
        $service_id = isset($input['service_id'])?(int)$input['service_id']:null;
        $errors = false;

        if(empty($invitee_id) && empty($service_id)) {
            if(empty($invitee_id))
                $errors['invitee_id'] = $this->errors[117];
            if(empty($service_id))
                $errors['service_id'] = $this->errors[117];
        }

        if(!empty($service_id)) {
            $exs = $this->users->GetHearing((int)$service_id);
            if(!$exs)
                $errors['service_id'] = $this->errors[116];
        }

        if(!empty($invitee_id)) {
            $exi = $this->users->GetInvitee((int)$invitee_id);
            if(!$exi)
                $errors['invitee_id'] = $this->errors[116];
            else
                $service_id = $exi->hearing_id;
        }

        if(!$this->users->GetOne($user_id))
            $errors['id'] = $this->errors[121];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            if(!empty($invitee_id)) {
                //меняем статус приглашения на завершённый
                $this->users->UpdateInvitee($invitee_id, 'FALSE');
            }

            $service =  $this->users->GetHearing((int)$service_id);
            $room = $this->filial->GetRoom($service->room_id);
            $code = rand(1000000, 9999999);
            $arr['time'] = date_create()->format('d-m-Y H:i:s');
            if(isset($room->step_in))
                $arr['steps'] = $room->step_in;
            else
                $arr['steps'] = 0;
            $arr['code'] = $code;
            $arr['id'] = $this->users->AddAccess($user_id, $service_id, $code);
            $result = $this->response($arr);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Добавление сотрудника
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function AddWorker(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');

        $not_empty = ['id_person', 'surname', 'first_name', 'patronymic', 'code', 'user_photo'];
        $errors = $this->IsEmpty($input, $not_empty);

        //определяем устройство по ипу и добавляем в дату филиал
        $FindByIP = $this->equipment->FindOne($ip, 'ip_adress');
        if ($FindByIP) {
            $input['filial_id'] = $FindByIP->filial_id;
        } else {
            $errors['ip'] = $this->errors[118];
        }

        //сохраняем изображение
        $input['user_photo'] = $this->SaveImageFromBase64($input['user_photo'], 'user_photo/');
        if (!$input['user_photo'])
            $errors['user_photo'] = $this->errors[119];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            //Необработанный сотрудник
            $input['user_type_id'] = 4;
            //создание юзера
            $input['id'] = (int)$this->users->Add($input);
            //создание работника
            $worker['user_id'] = $input['id'];
            $worker['code'] = $input['code'];
            $worker['filial_id'] = $input['filial_id'];
            $worker['permission_id'] = 5;
            $this->workers->Add($worker);

            $result = $this->response($input);

            $this->logger->Add([
                'message' => 'API. Зарегистрирован сотрудник.',
                'equipment_id' => $FindByIP->id,
                'user_id' => $input['id'],
            ]);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Закрытие сесси пользователем
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function CloseSession(Request $request, Response $response)
    {
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            $this->equipment->terminalCloseSession($eq->id);

            $result = $this->response();
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Добавление картинки с металлодетектора и ренгена
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function AddDetectorImg(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');
        $pass_id =  $args['id'];

        $not_empty = ['image', 'type'];
        $errors = $this->IsEmpty($input, $not_empty);

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ((int)$input['type'] != 1 || (int)$input['type'] != 2)
            $errors['type'] = $this->errors[114];

        if(!$this->users->GetPass($pass_id))
            $errors['id_pass'] = $this->errors[115];

        //сохраняем изображение
        $input['image'] = $this->SaveImageFromBase64($input['image'], 'other/');
        if (!$input['image'])
            $errors['image'] = $this->errors[119];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            if ($input['type'] == 1)
                $this->users->UpdatePass($pass_id, $input['image'], 'metal_detector');
            else
                $this->users->UpdatePass($pass_id, $input['image'], 'x-ray');

            $result = $this->response($input);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function GetAllMarks(Request $request, Response $response, $args)
    {
        $ip = $request->getAttribute('ip_address');
        $who =  $args['who'];
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {
            if ($who == 'users')
                $marks = $this->users->AllCodes($eq->filial_id);
            else
                $marks = $this->workers->AllCodes($eq->filial_id);

            $data['marks'] = [];
            foreach ($marks as $mark) {
                if ($who == 'users') {
                    $date = date('Y-m-d');
                    if (!empty($mark['code']) &&
                        ($mark['hdate'] == $date || is_null($mark['hdate']))) {
                        unset($mark['hdate']);
                        array_push($data['marks'], $mark);
                    }
                } else {
                    if (!empty($mark['code']))
                        array_push($data['marks'], $mark);
                }
            }

            $result = $this->response($data);
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Сохранение проходов которые контроллер пропустил в офлайн режиме
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function SavePass(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $ip = $request->getAttribute('ip_address');
        $errors = false;

        $eq = $this->equipment->FindOne($ip, 'ip_adress');
        if (!$eq)
            $errors['ip'] = $this->errors[118];

        if (!is_array($input))
            $errors['array'] = $this->errors[115];

        if ($errors) {
            $result = $this->response($errors, 'fail');
        } else {

            foreach ($input as $item) {
                if ($item['who'] == 'user') {
                    $access_id = (int)$item['id'];
                    $user_id = $this->users->GetAccess($access_id)->user_id;
                } else {
                    $access_id = null;
                    $user_id = $this->workers->GetOne((int)$item['id'])->user_id;
                }

                //щепотка валидации
                if (!$user_id)
                    continue;
                $default_directions = ['in', 'out'];
                if (!in_array($item['direction'], $default_directions))
                    continue;

                $pass = $this->users->GetPassInFilial($user_id, $access_id);
                if(!$pass)
                    $pass = $this->users->AddPass($user_id, $access_id);
                else
                    $pass = $pass->id;
                $this->users->AddDirectionPass($pass, $item['direction'], $item['date']);
            }

            $result = $this->response();
        }

        $response = $response->withJson($result);
        return $response;
    }

    /**
     * Временная заглушка для функций
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function Dummy(Request $request, Response $response)
    {
        $request->getParsedBody();
        $result = $this->response([
            'message' => 'dummy response',
        ]);
        $response = $response->withJson($result);
        return $response;
    }

    /*
     * Установка курсора терминала в указанную позицию
     */
    public function sendCursorCoordinates(Request $request, Response $response) {
        $data = [];
        $data['id'] = $request->getAttribute('id');
        $eq = $this->equipment->FindOne($data['id'], 'id');
        $errors = false;
        if(!$eq)
            $errors = 'Not found';
        if(!$errors) {
            $data['x'] = $request->getAttribute('x');
            $data['y'] = $request->getAttribute('y');
            if($this->client->SendTerminal($eq, 'helperpointer', $data)) {
                $data['status'] = 'success';
            } else {
                $data['status'] = 'error';
                $data['message'] = 'Ошибка отправки запроса.';
            }
            $response = $response->withJson($data);
        } else {
            $data['status'] = 'error';
            $data['message'] = $errors;
        }
        return $response;
    }
}


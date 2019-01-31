<?php

namespace App\Controllers;

use \Slim\Container;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;
use App\Models\WorkersModel;

/**
 * Для отображения страниц
 * Class PageController
 * @package App\Controllers
 */
class PageController
{
    /**
     * @var string Настройки сайта
     */
    private $settings;

    /**
     * @var WorkersModel
     */
    private $worker;

    /**
     * PagesController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->settings = $c->get('settings');
        $this->worker = $c->get('WorkersModel');
    }

    /**
     * Авторизация
     * @param $login
     * @param $pass
     * @return bool|object
     */
    public function Auth($login, $pass)
    {
        $user = $this->worker->GetOne($login, 'login');
        if ($user) {
            if ($user->password == md5($pass)) {
                //TODO delete all sessions var in code
                $_SESSION['id'] = $user->id;
                $_SESSION['first_name'] = $user->first_name;
                $_SESSION['patronymic'] = $user->patronymic;
                $_SESSION['surname'] = $user->surname;
                $_SESSION['filial_id'] = $user->filial_id;

                return $user;
            }
        }

        return false;
    }

    /**
     * Главная страница
     * @param Request $request
     * @param Response $response
     * @return int html
     */
    public function Main(Request $request, Response $response)
    {
        return $response->getBody()->write('test');
    }

    /**
     * Форма логина
     * @param Request $request
     * @param Response $response
     * @return int|object
     */
    public function Login(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $errors['e'] = false;

        //Авторизуем
        if ($request->isPost() && isset($input['auth'])) {
            $auth = $this->Auth($input['login'], $input['password']);
            if ($auth) {
                return $response->withRedirect('/');
            } else {
                $errors['e'] = 'Неверный логин или пароль';
            }
        }

        $data = layout('login', $errors);
        return $response->getBody()->write($data);
    }
}
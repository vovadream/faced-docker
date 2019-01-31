<?php


namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

/**
 * Класс для деплоя проекта
 * Class Deploy
 * @package App\Controllers
 */
class Deploy
{
    /**
     * @var string токен для вебхука
     */
    private $token;

    /**
     * @var string путь к корневой папке скрипта
     */
    private $path;

    /**
     * @var string Ветка откуда заливаем
     */
    private $branch;

    /**
     * Deploy constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        //разрешаем выполняться хоть сколько времени
        set_time_limit(0);
        $this->token = $c->get('settings')['web-hook_token'];
        $this->path = $c->get('settings')['path_to_core'];
        $this->branch = $c->get('settings')['git-branch'];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return int
     */
    public function run(Request $request, Response $response)
    {

        $this->command_exec();

        return $response->getBody()->write("Success." . PHP_EOL);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return int
     * @throws \Slim\Exception\NotFoundException
     */
    public function runWeb(Request $request, Response $response, $args)
    {
        $input_token = $args['token'];
        if ($input_token != $this->token)
            throw new \Slim\Exception\NotFoundException($request, $response);

        $this->command_exec();

        return $response->getBody()->write("Success.");
    }

    /**
     * Комманды для деплоя
     */
    private function command_exec()
    {
        $path = "cd {$this->path} && ";
        $log = "> {$this->path}logs/";

        //Стаскиваем с гита изменения
        shell_exec($path . "git pull origin {$this->branch} {$log}deploy-git.log 2>&1");

        //обновляем зависимости composer
        shell_exec($path . "php composer.phar update {$log}deploy-composer.log 2>&1");

        //Выполняем миграцию БД
        shell_exec($path . "php cli.php phinx migrate {$log}deploy-migrate.log 2>&1");

        //фронт css
        shell_exec($path . "gulp sass {$log}deploy-sass.log 2>&1");

        //фронт js
        shell_exec($path . "gulp js {$log}deploy-js.log 2>&1");

        return true;
    }
}
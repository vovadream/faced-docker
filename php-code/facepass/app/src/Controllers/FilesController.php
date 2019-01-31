<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

/**
 * Class FilesController
 * @package App\Controllers
 */
class FilesController
{
    /**
     * @var string Настройки сайта
     */
    private $settings;

    /**
     * @var object Ошибка не найдено
     */
    private $notFound;

    /**
     * FilesController constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->settings = $c->get('settings');
        $this->notFound = $c->get('notFoundHandler');
    }

    /**
     * Получение картинки
     * @param Request $request
     * @param Response $response
     * @param $arg
     * @return string binary image if exists
     */
    public function GetImage(Request $request, Response $response, $arg)
    {
        $type = $arg['type'];
        $name = $arg['name'];
        $storage = $this->settings['path_to_core_uploads'];
        $allow_ext = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];

        $file_name = $storage.$type.'/'.$name;
        $ext = pathinfo($file_name)['extension'];

        if (!file_exists($file_name) || !in_array($ext, $allow_ext)) {
            $handler = $this->notFound;
            return $handler($request, $response);
        }

        $image = @file_get_contents($file_name);

        $response->write($image);
        return $response->withHeader('Content-Type', 'image/'.$ext);
    }
}
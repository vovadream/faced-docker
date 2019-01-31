<?php

namespace App\Middleware;

use \Slim\Container;

/**
 * Class ApiErrorMiddleware
 * @package App\Middleware
 */
class ApiErrorMiddleware
{
    /**
     * @var $container Container
     */
    private $container;

    /**
     * ApiErrorMiddleware constructor.
     * @param $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;

        //@TODO Найти как в инвоке запускать notfound и notallowed
        $uri = $container['request']->getUri();
        $path = $uri->getPath();
        if (strripos($path, 'api/v1') !== false)
        {
            $this->container['notFoundHandler'] = function ($c) {
                return function ($request, $response) use ($c) {
                    $data404 = [
                        'status' => 'error',
                        'code' =>  404,
                        'message' => 'Not found'
                    ];
                    return $c['response']->withStatus(404)
                        ->withJson($data404);
                };
            };
            $this->container['notAllowedHandler'] = function ($c) {
                return function ($request, $response, $methods) use ($c) {
                    $data405 = [
                        'status' => 'error',
                        'code' =>  405,
                        'message' => 'Method not allowed'
                    ];
                    return $c['response']
                        ->withStatus(405)
                        ->withHeader('Allow', implode(', ', $methods))
                        ->withJson($data405);
                };
            };
        }
    }


    /**
     * Error middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        //Критичные ошибки сервера
        $this->container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                $data500 = [
                    'status' => 'error',
                    'code' =>  500,
                    'message' => $exception->getMessage()
                ];
                return $c['response']->withStatus(500)
                    ->withJson($data500);
            };
        };
        //ошибки php(только в 7 php)
        $this->container['phpErrorHandler'] = function ($c) {
            return $c['errorHandler'];
        };


        $response = $next($request, $response);

        return $response;
    }
}
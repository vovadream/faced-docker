<?php

namespace App\Middleware;

use App\Models\Model;
use App\Models\WorkersModel;
use \Slim\Container;

/**
 * Для авторизации пользователей в интерфейсе
 * Class AuthMiddleware
 * @package App\Middleware
 */
class AuthMiddleware
{
    /**
     * @var Container
     */
    private $container;

    /**
     * AuthMiddleware constructor.
     * @param $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Auth middleware invokable class
     *
     * @param  \Slim\Http\Request   $request  PSR7 request
     * @param  \Slim\Http\Response  $response PSR7 response
     * @param  callable             $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $worker = $this->container->get('WorkersModel');

        //Если не авторизованы, перенаправляем на авторизацию
        if (!isset($_SESSION['id'])) {
            return $response->withRedirect(base_path().'login/');
        } else {
            $user = $worker->GetOne($_SESSION['id']);
            if (!$user) {
                return $response->withRedirect(base_path().'login/');
            }

            $request = $request->withAttribute('current_user', $user);
        }

        $response = $next($request, $response);

        return $response;
    }

}
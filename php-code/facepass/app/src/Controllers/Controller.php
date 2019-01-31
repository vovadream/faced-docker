<?php
namespace App\Controllers;

use Slim\Container;
use App\Models\Model;

class Controller
{
    var $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($var)
    {
        return $this->container->{$var};
    }
}
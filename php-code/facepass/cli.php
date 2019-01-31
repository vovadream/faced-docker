#!/usr/bin/env php
<?php
/**
 * Интерфейс для командной строки
 * Вызовы:
 * php /path/to/cli.php pinging - Пингануть все устройства
 * php /path/to/cli.php sigur - Демон делегирования принятий решений для интеграции с Sigur
 * php /path/to/cli.php deploy - запустить процесс деплоя
 * php /path/to/cli.php phinx - Библиотека phinx для работы с миграциями (https://book.cakephp.org/3.0/en/phinx.html)
 * php /path/to/cli.php init-db - Накатить базу данных при первом запуске приложения
 */
if (PHP_SAPI != 'cli')
    die('Only command line execute');

use Slim\Container;
use App\Controllers\HttpClientController;

//разрешаем выполняться хоть сколько времени
set_time_limit(0);

require __DIR__ . '/vendor/autoload.php';

//внедряем в запрос фреймворка строку из аргумента
$env = \Slim\Http\Environment::mock(
        ['REQUEST_URI' => '/' . $argv[1]]
);
$c = require __DIR__ . '/app/settings.php';
$c['environment'] = $env;
$app = new \Slim\App($c);

require __DIR__ . '/app/functions.php';
require __DIR__ . '/app/dependencies.php';

//Обработка ошибок в командном интефейсе
$container['errorHandler'] = function (Container $c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'text/text')
            ->write('Something went wrong!(500)');
    };
};
$container['notFoundHandler'] = function (Container $c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/text')
            ->write('Not Found');
    };
};

//Проверка доступности всех устройств
$app->get('/pinging', HttpClientController::class.':Pinging');

//интеграции
$app->get('/sigur', App\Integration\Sigur\Controller::class.':Delegation');

//деплой
$app->get('/deploy', \App\Controllers\Deploy::class.':run');

//миграции
$app->get('/phinx', function () use ($argv) {
    $argStr = '';
    foreach ($argv as $key => $arg) {
        if($key > 1) {
            $argStr .= $arg.' ';
        }
    }
    exec('php vendor/bin/phinx '. $argStr , $a);
        echo implode(PHP_EOL, $a). PHP_EOL;
});

//Накатить базу данных postgress (Init) во время первого запуска (База лежит /db/db.sql)
$app->get('/init-db', function () use ($argv, $c) {

    $dbConfig = $c['settings']['db'];

    exec('psql -U postgres '.$dbConfig['dbname'].' < db/structure.sql ' , $a);

    if(array_search('--with-data', $argv) > 0) {
        exec('psql -U postgres '.$dbConfig['dbname'].' < db/data.sql ' , $a);
    }

    echo implode(PHP_EOL, $a). PHP_EOL;
});

$app->run();

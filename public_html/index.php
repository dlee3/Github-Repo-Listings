<?php



date_default_timezone_set('UTC');

define('APPLICATION_PATH', __DIR__ . '/..');



require sprintf('%s/environment.inc', APPLICATION_PATH);
require sprintf('%s/vendor/autoload.php', APPLICATION_PATH);



Model\Config::init(sprintf('%s/config.ini', APPLICATION_PATH));



// setup the DB
Model\Db::setup();



$app = new \Slim\Slim([
	'debug' => Model\Config::$instance->app->debug,
    'templates.path' => APPLICATION_PATH . '/Templates'
]);




require sprintf('%s/routes.inc', APPLICATION_PATH);



$app->run();


<?php

require_once __DIR__ . '/../vendor/autoload.php';


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails'=> true,
    ]
]);

$container = $app->getContainer();

$container['db'] = function(){
    return new PDO('mysql:host=localhost;dbname=todo', 'root', '');
};


$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
       
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()),'/');
    $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

require_once __DIR__ . '/../routes/web.php';

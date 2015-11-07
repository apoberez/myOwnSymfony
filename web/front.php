<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$map = [
    '/hello' => __DIR__.'/../src/pages/hello.php',
    '/bye' => __DIR__.'/../src/pages/bye.php'
];

$request = Request::createFromGlobals();
$response = new Response();

$path = $request->getPathInfo();
if (array_key_exists($path, $map)) {
    ob_start();
    require $map[$path];
    $response->setContent(ob_get_clean());
} else {
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
    $response->setContent(Response::$statusTexts[Response::HTTP_NOT_FOUND]);
};

$response->send();
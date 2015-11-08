<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

$request = Request::createFromGlobals();

/** @var RouteCollection $routes */
$routes = include __DIR__ . '/../src/app.php';

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$path = $request->getPathInfo();

try {
    extract($matcher->match($path));
    ob_start();
    include sprintf(__DIR__ . '/../src/pages/%s.php', $_route);

    $response = new Response(ob_get_clean());
} catch (ResourceNotFoundException $e) {
    $response = new Response(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
} catch (Exception $e) {
    $response = new Response(
        Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
        Response::HTTP_INTERNAL_SERVER_ERROR
    );
};

$response->send();
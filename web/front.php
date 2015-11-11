<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

$request = Request::createFromGlobals();

/** @var RouteCollection $routes */
$routes = include __DIR__ . '/../src/Calendar/app.php';
$matcher = new UrlMatcher($routes, new RequestContext());
$resolver = new ControllerResolver();
$framework = new Framework($matcher, $resolver);

$response = $framework->handle($request);

$response->send();
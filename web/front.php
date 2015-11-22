<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Simplex\Framework;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

$request = Request::createFromGlobals();

/** @var RouteCollection $routes */
$routes = include __DIR__ . '/../src/Calendar/app.php';
$matcher = new UrlMatcher($routes, new RequestContext());
$resolver = new ControllerResolver();

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher));
$dispatcher->addSubscriber(new ExceptionListener('Calendar\\Controller\\ErrorController::exceptionAction'));
$dispatcher->addSubscriber(new ResponseListener('UTF-8'));

$framework = new Framework($dispatcher, $resolver);
$framework = new HttpCache($framework, new Store(__DIR__.'/../cache'));

$response = $framework->handle($request);
$response->send();

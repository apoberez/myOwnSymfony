<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Calendar\EventListener\FrameworkResponseListener;
use Simplex\Events\FrameworkEvents;
use Simplex\Framework;

use Symfony\Component\EventDispatcher\EventDispatcher;
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

$dispatcher = new EventDispatcher();
$dispatcher->addListener(FrameworkEvents::ON_RESPONSE, [new FrameworkResponseListener(), 'onResponse']);

$framework = new Framework($matcher, $resolver, $dispatcher);

$response = $framework->handle($request);

$response->send();
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/Calendar/app.php';

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $sc */
$sc = include __DIR__.'/../src/container.php';

/** @var \Symfony\Component\HttpKernel\KernelInterface $framework */
$framework = $sc->get('framework');

$response = $framework->handle($request);
$response->send();

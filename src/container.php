<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\HttpCache\Store;

$sc = new ContainerBuilder(new ParameterBag());
$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments([$routes, new Reference('context')])
;
$sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');

$sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments([new Reference('matcher')])
;
$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(['UTF-8'])
;
$sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments(['Calendar\\Controller\\ErrorController::exceptionAction'])
;
$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')])
;
$sc->register('framework', 'Simplex\Framework')
    ->setArguments([new Reference('dispatcher'), new Reference('resolver')])
;
$sc->register('framework_cache', '\Symfony\Component\HttpKernel\HttpCache\HttpCache')
    ->addArgument(new Reference('framework_cache.inner'))
    ->addArgument(new Store(__DIR__.'/../cache'))
    ->setDecoratedService('framework')
;

return $sc;

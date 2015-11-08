<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('leap_year', new Route('/is_leap_year/{year}', [
    'year' => 1995,
    '_controller' => 'Calendar\Controller\LeapController::indexAction'
]));

return $routes;
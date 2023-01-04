<?php


use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator) {

    $routingConfigurator
        ->import('../src/Controller/', 'annotation')
        ->prefix('/');

    $routingConfigurator
        ->import('../src/Controller/Security');
};
<?php

use App\Product\Infrastructure\Controller\AddProductController;
use App\Product\Infrastructure\Controller\ListProductController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('product_list', '/list')
        ->controller(ListProductController::class)
        ->methods(['GET']);

    $routes->add('product_add', '/add/{name}/{price}')
        ->controller(AddProductController::class)
        ->defaults(['price' => 0])
        ->methods(['GET']);
};

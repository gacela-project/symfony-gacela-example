<?php

use App\Kernel;
use Gacela\Framework\Gacela;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
##############################
# OPTION A: Using gacela.php #
##############################
Gacela::bootstrap($kernel->getProjectDir(), ['symfony/kernel' => $kernel]);
/*
############################################################
# OPTION B: Directly here. Without the need for gacela.php #
############################################################

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Gacela\Framework\Config\ConfigReader\EnvConfigReader;
use Doctrine\ORM\EntityManagerInterface;

Gacela::bootstrap($kernel->getProjectDir(), [
    'config' => [
        'path' => '.env*',
        'path_local' => '.env',
    ],
    'mapping-interfaces' => [
        ProductRepositoryInterface::class => ProductRepository::class,
        EntityManagerInterface::class => static fn() => $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager'),
    ],
    'config-readers' => [
        'env' => new EnvConfigReader(),
    ],
]);
*/
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

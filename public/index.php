<?php

use App\Kernel;
use Gacela\Framework\Config\GacelaConfigBuilder\ConfigBuilder;
use Gacela\Framework\Config\GacelaConfigBuilder\MappingInterfacesBuilder;
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

######################################################################
# OPTION B: Using Gacela::bootstrap. Without the need for gacela.php #
######################################################################

//use App\Product\Domain\ProductRepositoryInterface;
//use App\Product\Infrastructure\Persistence\ProductRepository;
//use Doctrine\ORM\EntityManagerInterface;
//use Gacela\Framework\Config\ConfigReader\EnvConfigReader;
//
//Gacela::bootstrap($kernel->getProjectDir(), [
//    'symfony/kernel' => $kernel,
//    'config' => function (ConfigBuilder $configBuilder): void {
//        $configBuilder->add('.env*', '.env.local', EnvConfigReader::class);
//    },
//    'mapping-interfaces' => function (
//        MappingInterfacesBuilder $mappingInterfacesBuilder,
//        array $globalServices
//    ): void {
//        $mappingInterfacesBuilder->bind(ProductRepositoryInterface::class, ProductRepository::class);
//
//        /** @var Kernel $kernel */
//        $kernel = $globalServices['symfony/kernel'];
//
//        $mappingInterfacesBuilder->bind(
//            EntityManagerInterface::class,
//            static fn() => $kernel->getContainer()->get('doctrine.orm.entity_manager')
//        );
//    },
//]);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

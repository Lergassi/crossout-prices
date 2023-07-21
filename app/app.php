#!/usr/bin/env php
<?php
//todo: Только для dev. Для prod оставить ~E_DEPRECATED.
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

//--------------------------------
// init app
//--------------------------------
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new \App\Services\Dump\InitCustomDumper();

//--------------------------------
//region init container
//--------------------------------
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    PDO::class => function (ContainerInterface $container) {
        return new \PDO(
            sprintf('mysql:host=%s;dbname=%s', $_ENV['APP_DB_HOST'] ?? '', $_ENV['APP_DB_NAME'] ?? ''),
            $_ENV['APP_DB_USER'] ?? '',
            $_ENV['APP_DB_PASSWORD'] ?? '',
            [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
    },
    \App\Services\ProjectPath::class => function (ContainerInterface $container) {
        return new \App\Services\ProjectPath($_ENV['APP_PROJECT_ROOT'] ?? '');
    },
]);

$container = $containerBuilder->build();
//endregion init container

$application = new Application(
        $_ENV['APP_NAME'] ?? 'crossout',
        $_ENV['APP_VERSION'] ?? '',
);

//--------------------------------
// commands
//--------------------------------
$application->add($container->get(\App\Commands\MainCommand::class));
$application->add($container->get(\App\Commands\OptimalRouteCommand::class));

$application->add($container->get(\App\Commands\DownloadItemsCommand::class));
$application->add($container->get(\App\Commands\DownloadRecipesCommand::class));
$application->add($container->get(\App\Commands\DownloadPricesCommand::class));

$application->add($container->get(\App\Commands\ManualLoadItemsToDatabaseCommand::class));
$application->add($container->get(\App\Commands\LoadItemsToDatabaseCommand::class));
$application->add($container->get(\App\Commands\LoadRecipesToDatabaseCommand::class));
$application->add($container->get(\App\Commands\LoadPricesToDatabaseCommand::class));

$application->add($container->get(\App\Commands\DetailItemCommand::class));

//todo: Сделать чтобы sandbox/test были не доступны на prod.
//--------------------------------
// sandbox commands
//--------------------------------
$application->add(new \App\Commands\SandboxCommands\MainSandboxCommand($container));
$application->add($container->get(\App\Commands\SandboxCommands\PriceSandboxCommand::class));

//--------------------------------
// test commands
//--------------------------------
$application->add(new \App\Commands\TestCommands\TestCommand());
$application->add(new \App\Commands\TestCommands\TestVarDumperCommand());
$application->add(new \App\Commands\TestCommands\TestDotenvCommand());
$application->add($container->get(\App\Commands\TestCommands\TestInjectContainerCommand::class));
$application->add($container->get(\App\Commands\TestCommands\TestPDOInjectCommand::class));

$application->add($container->get(\App\Commands\TestCommands\TestOptimalRouteCommand::class));

//--------------------------------
// run app
//--------------------------------
$application->run();
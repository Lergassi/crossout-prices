#!/usr/bin/env php
<?php
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

new \App\Service\Dump\InitCustomDumper();

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
    \App\Service\ProjectPath::class => function (ContainerInterface $container) {
        return new \App\Service\ProjectPath($_ENV['APP_PROJECT_ROOT'] ?? '');
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
//$application->add($container->get(\App\Commands\MainCommand::class));
$application->add($container->get(\App\Command\OptimalRouteCommand::class));
$application->add($container->get(\App\Command\ProfitsCommand::class));

$application->add($container->get(\App\Command\DownloadItemsCommand::class));
$application->add($container->get(\App\Command\DownloadRecipesCommand::class));
$application->add($container->get(\App\Command\DownloadPricesCommand::class));

//$application->add($container->get(\App\Commands\ManualLoadItemsToDatabaseCommand::class));
$application->add($container->get(\App\Command\LoadItemsToDatabaseCommand::class));
$application->add($container->get(\App\Command\LoadRecipesToDatabaseCommand::class));
$application->add($container->get(\App\Command\UpdatePricesInDatabaseCommand::class));

$application->add($container->get(\App\Command\InitCommand::class));
$application->add($container->get(\App\Command\UpdateCommand::class));

$application->add($container->get(\App\Command\CalculateProfitsCommand::class));
$application->add($container->get(\App\Command\CalculateProfitCommand::class));

$application->add($container->get(\App\Command\WipeCommand::class));

//--------------------------------
// sandbox commands
//--------------------------------
$application->add(new \App\Command\SandboxCommands\MainSandboxCommand($container));
$application->add($container->get(\App\Command\SandboxCommands\PriceSandboxCommand::class));

//--------------------------------
// test commands
//--------------------------------
$application->add(new \App\Command\TestCommands\TestCommand());
$application->add(new \App\Command\TestCommands\TestVarDumperCommand());
$application->add(new \App\Command\TestCommands\TestDotenvCommand());
$application->add($container->get(\App\Command\TestCommands\TestInjectContainerCommand::class));
$application->add($container->get(\App\Command\TestCommands\TestPDOInjectCommand::class));
//$application->add($container->get(\App\Commands\TestCommands\TestNameInjectCommand::class));

$application->add($container->get(\App\Command\TestCommands\TestOptimalRouteCommand::class));
$application->add($container->get(\App\Command\TestCommands\TestOptimalRoutesCommand::class));

//--------------------------------
// run app
//--------------------------------
$application->run();
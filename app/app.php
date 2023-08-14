#!/usr/bin/env php
<?php
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use App\Command\CalculateProfitCommand;
use App\Command\CalculateProfitsCommand;
use App\Command\DownloadItemsCommand;
use App\Command\DownloadPricesCommand;
use App\Command\DownloadRecipesCommand;
use App\Command\InitCommand;
use App\Command\LoadCommand;
use App\Command\LoadItemsToDatabaseCommand;
use App\Command\LoadRecipesToDatabaseCommand;
use App\Command\OptimalRouteCommand;
use App\Command\ProfitsCommand;
use App\Command\SandboxCommands\MainSandboxCommand;
use App\Command\SandboxCommands\PriceSandboxCommand;
use App\Command\TestCommands\TestCommand;
use App\Command\TestCommands\TestDotenvCommand;
use App\Command\TestCommands\TestInjectContainerCommand;
use App\Command\TestCommands\TestOptimalRouteCommand;
use App\Command\TestCommands\TestOptimalRoutesCommand;
use App\Command\TestCommands\TestPDOInjectCommand;
use App\Command\TestCommands\TestVarDumperCommand;
use App\Command\UpdateCommand;
use App\Command\UpdatePricesInDatabaseCommand;
use App\Command\WipeCommand;
use App\Service\Dump\InitCustomDumper;
use App\Service\ProjectPath;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

//--------------------------------
// init app
//--------------------------------
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new InitCustomDumper();

//--------------------------------
//region init container
//--------------------------------
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    PDO::class => function (ContainerInterface $container) {
        return new PDO(
            sprintf('mysql:host=%s;dbname=%s', $_ENV['APP_DB_HOST'] ?? '', $_ENV['APP_DB_NAME'] ?? ''),
            $_ENV['APP_DB_USER'] ?? '',
            $_ENV['APP_DB_PASSWORD'] ?? '',
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
    },
    ProjectPath::class => function (ContainerInterface $container) {
        return new ProjectPath($_ENV['APP_PROJECT_ROOT'] ?? '');
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
$application->add($container->get(OptimalRouteCommand::class));
$application->add($container->get(ProfitsCommand::class));

$application->add($container->get(DownloadItemsCommand::class));
$application->add($container->get(DownloadRecipesCommand::class));
$application->add($container->get(DownloadPricesCommand::class));

$application->add($container->get(LoadItemsToDatabaseCommand::class));
$application->add($container->get(LoadRecipesToDatabaseCommand::class));
$application->add($container->get(UpdatePricesInDatabaseCommand::class));
$application->add($container->get(LoadCommand::class));

$application->add($container->get(InitCommand::class));
$application->add($container->get(UpdateCommand::class));

$application->add($container->get(CalculateProfitsCommand::class));
$application->add($container->get(CalculateProfitCommand::class));

$application->add($container->get(WipeCommand::class));

//--------------------------------
// sandbox commands
//--------------------------------
$application->add(new MainSandboxCommand($container));
$application->add($container->get(PriceSandboxCommand::class));

//--------------------------------
// test commands
//--------------------------------
$application->add(new TestCommand());
$application->add(new TestVarDumperCommand());
$application->add(new TestDotenvCommand());
$application->add($container->get(TestInjectContainerCommand::class));
$application->add($container->get(TestPDOInjectCommand::class));
//$application->add($container->get(\App\Commands\TestCommands\TestNameInjectCommand::class));

$application->add($container->get(TestOptimalRouteCommand::class));
$application->add($container->get(TestOptimalRoutesCommand::class));

//--------------------------------
// run app
//--------------------------------
$application->run();
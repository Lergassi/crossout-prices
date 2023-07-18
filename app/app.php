#!/usr/bin/env php
<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use App\Services\Database;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

//--------------------------------
// init app
//--------------------------------
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new \App\Services\InitCustomDumper();

//--------------------------------
//region init container
//--------------------------------
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    Database::class => function (ContainerInterface $container) {
        return new Database(
            $_ENV['APP_DB_HOST'],
            $_ENV['APP_DB_NAME'],
            $_ENV['APP_DB_USER'],
            $_ENV['APP_DB_PASSWORD'],
        );
    },
]);

$container = $containerBuilder->build();
//endregion init container

$application = new Application();

//--------------------------------
// commands
//--------------------------------
$application->add($container->get(\App\Commands\ListCommand::class));

//--------------------------------
// sandbox commands
//--------------------------------
$application->add(new \App\Commands\SandboxCommands\MainSandboxCommand($container));

//--------------------------------
// test commands
//--------------------------------
$application->add(new \App\Commands\TestCommands\TestCommand());
$application->add(new \App\Commands\TestCommands\TestVarDumperCommand());
$application->add(new \App\Commands\TestCommands\TestDotenvCommand());
$application->add($container->get(\App\Commands\TestCommands\TestInjectContainerCommand::class));

$application->run();
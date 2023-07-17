#!/usr/bin/env php
<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

// init app
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new \App\InitCustomDumper();
$application = new Application();

// commands

// tests commands
$application->add(new \App\Test\TestCommand());
$application->add(new \App\Test\TestVarDumperCommand());
$application->add(new \App\Test\TestDotenvCommand());

$application->run();
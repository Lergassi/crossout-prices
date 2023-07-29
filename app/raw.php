<?php
require __DIR__ . '/../vendor/autoload.php';

//echo 'this is /app/raw.php' . PHP_EOL;
var_dump('this is /app/raw.php');

$foo = new \App\Command\TestCommands\Foo('this is Foo');
var_dump($foo->msg);
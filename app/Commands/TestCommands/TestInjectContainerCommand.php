<?php

namespace App\Commands\TestCommands;

use App\Services\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestInjectContainerCommand extends Command
{
    protected static $defaultName = 'test.inject_container';

    private \App\Test\Foo $foo;
    private Database $database;

    public function __construct(\App\Test\Foo $foo, Database $database)
    {
        $this->foo = $foo;
        parent::__construct(static::$defaultName);
        $this->database = $database;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dump($this->foo);
        dump($this->database);

        return 0;
    }
}
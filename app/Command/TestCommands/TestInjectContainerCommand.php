<?php

namespace App\Command\TestCommands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestInjectContainerCommand extends Command
{
    protected static $defaultName = 'test.inject_container';

    private \App\Test\Foo $foo;

    public function __construct(\App\Test\Foo $foo)
    {
        $this->foo = $foo;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dump($this->foo);

        return 0;
    }
}
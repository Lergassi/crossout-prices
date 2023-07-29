<?php

namespace App\Command\TestCommands;

use App\Test\Foo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestNameInjectCommand extends Command
{
//    protected static $defaultName = 'test.name_inject';

//    public function __construct(string $name = null)
    private Foo $foo;

    public function __construct(Foo $foo, string $name = null)
    {
        parent::__construct($name);
        $this->foo = $foo;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dump($this->foo);

        return 0;
    }
}
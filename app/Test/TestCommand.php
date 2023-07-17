<?php

namespace App\Test;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump('this is TestCommand');

        dump('this is dump');
        dd('this is dd');

        return 0;
    }
}
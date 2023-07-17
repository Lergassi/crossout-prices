<?php

namespace App\Test;

use Dotenv\Dotenv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestDotenvCommand extends Command
{
    protected static $defaultName = 'test.dotenv';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($_ENV);

        return 0;
    }
}
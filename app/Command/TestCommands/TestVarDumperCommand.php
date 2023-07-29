<?php

namespace App\Command\TestCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestVarDumperCommand extends Command
{
    protected static $defaultName = 'test.var_dumper';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        new \App\InitCustomDumper();

        dump('this is dump. File: "/app/CliCustomDumper.php", line 17');
        dd('this is dd. File: "/app/CliCustomDumper.php", line 18');

        return 0;
    }
}
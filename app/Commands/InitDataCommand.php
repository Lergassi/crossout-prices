<?php

namespace App\Commands;

use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDataCommand extends Command
{
    protected static $defaultName = 'db.init_data';

    public function __construct()
    {
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //todo:
        //load resources
        //load items
        //load recipes + requireItems

        return 0;
    }
}
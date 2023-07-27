<?php

namespace App\Commands;

use App\Services\PriceController;
use stringEncode\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MainCommand extends Command
{
    protected static $defaultName = 'main';

    public function __construct()
    {
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new Exception('indev');

        return 0;
    }
}
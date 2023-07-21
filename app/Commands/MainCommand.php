<?php

namespace App\Commands;

use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MainCommand extends Command
{
    protected static $defaultName = 'main';
    private PriceController $_priceController;

    public function __construct(PriceController $priceController)
    {
        $this->_priceController = $priceController;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_priceController->optimalRoute(497);

        return 0;
    }
}
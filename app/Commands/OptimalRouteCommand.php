<?php

namespace App\Commands;

use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OptimalRouteCommand extends Command
{
    protected static $defaultName = 'optimal_route';

    private PriceController $_priceController;

    public function __construct(PriceController $priceController)
    {
        $this->_priceController = $priceController;
        parent::__construct(static::$defaultName);
    }

    protected function configure()
    {
        $this->addArgument('ID', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ID = intval($input->getArgument('ID'));

        $this->_priceController->detailItem($ID);

        return 0;
    }
}
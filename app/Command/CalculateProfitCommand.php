<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\PriceController;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateProfitCommand extends Command
{
    protected static $defaultName = 'db.calc_item';

    private PriceController $_priceController;

    public function __construct(PriceController $priceController)
    {
        parent::__construct(static::$defaultName);
        $this->_priceController = $priceController;
    }

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ID = intval($input->getArgument('id'));

        echo 'Рассчет оптимальных цен для предмета запушен...' . PHP_EOL;

        $this->_priceController->calculateOptimalRoute($ID, new \DateTime());

        echo 'Рассчет оптимальных цен для предмета завершен.' . PHP_EOL;

        return 0;
    }
}
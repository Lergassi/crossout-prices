<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\ProfitCalculator;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateProfitCommand extends Command
{
    protected static $defaultName = 'db.calc_profit';

    private ProfitCalculator $_profitCalculator;

    public function __construct(ProfitCalculator $priceController)
    {
        parent::__construct(static::$defaultName);
        $this->_profitCalculator = $priceController;
    }

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ID = intval($input->getArgument('id'));

        echo 'Рассчет оптимальных цен для предмета запушен...' . PHP_EOL;

        $this->_profitCalculator->calculateOptimalRoute($ID, new \DateTime());

        echo 'Рассчет оптимальных цен для предмета завершен.' . PHP_EOL;

        return 0;
    }
}
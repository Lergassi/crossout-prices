<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\ProfitCalculator;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateProfitsCommand extends Command
{
    protected static $defaultName = 'db.calc_profits';

    private DataManager $_dataManager;
    private ProfitCalculator $_profitCalculator;

    public function __construct(DataManager $dataManager, ProfitCalculator $priceController)
    {
        parent::__construct(static::$defaultName);
        $this->_dataManager = $dataManager;
        $this->_profitCalculator = $priceController;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $start = microtime(true);
        echo 'Рассчет оптимальных цен запушен...' . PHP_EOL;

        $date = new \DateTime();
        $items = $this->_dataManager->findCraftableItems();
        foreach ($items as $item) {
            $this->_profitCalculator->calculateOptimalRoute($item['id'], $date);
        }

        echo 'Рассчет оптимальных цен завершен.' . PHP_EOL;
//        $end = microtime(true);
//        dump(round($end - $start, 3));

        return 0;
    }
}
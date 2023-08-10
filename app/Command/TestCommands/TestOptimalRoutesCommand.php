<?php

namespace App\Command\TestCommands;

use App\Service\DataManager;
use App\Service\ProfitCalculator;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Быстрая проверка работоспособности всех предметов с echo в консоли на каждый предмет.
 */
class TestOptimalRoutesCommand extends Command
{
    protected static $defaultName = 'test.optimal_routes_fast';
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
//        $items = $this->_dataManager->findItemsWithoutCategory(CategoryID::Resources->value);
//        foreach ($items as $item) {
//            $this->_profitCalculator->calculateOptimalRoute($item['id']);
//        }

        return 0;
    }
}
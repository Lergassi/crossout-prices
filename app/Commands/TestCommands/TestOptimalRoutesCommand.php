<?php

namespace App\Commands\TestCommands;

use App\Services\DataManager;
use App\Services\PriceController;
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
    private PriceController $_priceController;

    public function __construct(DataManager $dataManager, PriceController $priceController)
    {
        parent::__construct(static::$defaultName);
        $this->_dataManager = $dataManager;
        $this->_priceController = $priceController;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $items = $this->_dataManager->findItemsWithoutCategory(CategoryID::Resources->value);
//        foreach ($items as $item) {
//            $this->_priceController->calculateOptimalRoute($item['id']);
//        }

        return 0;
    }
}
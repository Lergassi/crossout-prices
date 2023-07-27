<?php

namespace App\Commands;

use App\Services\DataManager;
use App\Services\PriceController;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateProfitsCommand extends Command
{
    protected static $defaultName = 'db.calc';

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
        echo 'Рассчет оптимальных цен запушен...' . PHP_EOL;

        $date = new \DateTime();
        $items = $this->_dataManager->findCraftableItems();
        foreach ($items as $item) {
            $this->_priceController->calculateOptimalRoute($item['id'], $date);
        }

        echo 'Рассчет оптимальных цен завершен.' . PHP_EOL;

        return 0;
    }
}
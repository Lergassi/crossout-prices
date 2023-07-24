<?php

namespace App\Commands;

use App\Services\DataManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProfitsCommand extends Command
{
    protected static $defaultName = 'profits';

    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        parent::__construct(static::$defaultName);
        $this->_dataManager = $dataManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $prices = $this->_dataManager->totalItemProfits();
        foreach ($prices as $index => $price) {
            echo vsprintf('| %s | %s | %s | %s | %s | %s | %s |' . PHP_EOL, [
                $index + 1,
                $price['item_id'],
                $price['i_name'],
                $price['i_category'],
                $price['c_optimal_craft_cost'],
                $price['max_sell_price'],
                $price['c_profit'],
                $price['c_type'],
            ]);
        }

        return 0;
    }
}
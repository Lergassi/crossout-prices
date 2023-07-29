<?php

namespace App\Command;

use App\CliRender\CliTableRender;
use App\Service\DataManager;
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

    protected function configure()
    {
        $this->addOption(
            'all',
            'a',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allItems = $input->getOption('all');

        $table = new CliTableRender(8, [
            'index',
            'id',
            'name',
            'category',
            'optimal craft cost',
            'max sell price',
            'profit',
            'type',
        ]);

        $prices = $this->_dataManager->totalItemProfits(!$allItems);
        foreach ($prices as $index => $price) {
            $table->add([
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

        echo $table->render();

        return 0;
    }
}
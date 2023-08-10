<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\ProfitCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @indev Заготовка для информации без роутов.
 */
class DetailItemCommand extends Command
{
    protected static $defaultName = 'detail_item';
    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->_dataManager = $dataManager;
        parent::__construct(static::$defaultName);
    }

    protected function configure()
    {
        $this->addArgument('ID', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ID = intval($input->getArgument('ID'));

        $item = $this->_dataManager->findOneItem($ID);
        $recipe = $this->_dataManager->findOneRecipe($ID);
        $requireItems = $this->_dataManager->findRequireItemsWithJoin($ID);

        $separator = str_repeat('-', 64) . PHP_EOL;
        echo $separator;
        echo sprintf('| Item: %s (%s)', $item['name'], $item['id']) . PHP_EOL;
        echo $separator;
        echo sprintf('| Craft cost: %s, result count: %s', $recipe['craft_cost'], $recipe['result_count']) . PHP_EOL;
        echo $separator;
        echo sprintf('| Require items:') . PHP_EOL;
        echo $separator;
        foreach ($requireItems as $requireItem) {
            echo sprintf('| %s | %s | %s', $requireItem['item_id'], $requireItem['i_name'], $requireItem['item_count']) . PHP_EOL;
        }
        echo $separator;

        return 0;
    }
}
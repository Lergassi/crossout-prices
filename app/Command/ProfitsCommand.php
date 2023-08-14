<?php

namespace App\Command;

use App\CliRender\CliTableRender;
use App\Service\DataManager;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProfitsCommand extends Command
{
    protected static $defaultName = 'profits';
    protected static $defaultDescription = 'Выводит выгоду для предметов. По умолчанию только доступных для крафта.';

    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        parent::__construct(static::$defaultName);
        $this->_dataManager = $dataManager;
    }

    protected function configure()
    {
        $this->addOption('all', 'a',null,'Выводит все предметы, в том числе не доступные для крафта.',);
        $this->addOption('categories', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allItems = $input->getOption('all');

        $head = [
            'index',
            'id',
            'name',
            'category',
            'quality',
            'optimal craft cost',
            'min buy price',
            'max sell price',
            'profit',
            'type',
        ];
        $table = new CliTableRender(count($head), $head);

        $defaultCategories = [
            CategoryID::Cabins->value,
            CategoryID::Weapons->value,
            CategoryID::Hardware->value,
            CategoryID::Movement->value,
        ];

        $categoriesOption = $input->getOption('categories');    //todo: isSetOption?
        if ($categoriesOption) {
            $categories = explode(',', $categoriesOption);  //todo: А почему explode с пустой строкой массив возвращает c исходным значением?
            $categories = array_filter($categories, function ($category) {
                return $category ?: false;
            });
        } else {
            $categories = $defaultCategories;
        }

        $prices = $this->_dataManager->totalItemProfits(!$allItems, $categories);
        foreach ($prices as $index => $price) {
            $table->add([
                $index + 1,
                $price['item_id'],
                $price['name'],
                $price['category'],
                $price['quality'],
                $price['c_optimal_craft_cost'],
                $price['min_buy_price'],
                $price['max_sell_price'],
                $price['c_profit'],
                $price['c_type'],
            ]);
        }

        echo $table->render();

        return 0;
    }
}
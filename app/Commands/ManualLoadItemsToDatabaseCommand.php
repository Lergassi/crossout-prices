<?php

namespace App\Commands;

use App\Services\DataManager;
use App\Services\PriceController;
use App\Types\CategoryID;
use App\Types\QualityID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ManualLoadItemsToDatabaseCommand extends Command
{
    protected static $defaultName = 'db.manual_load_items';
    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->_dataManager = $dataManager;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resources = [
            ['ID' => 53, 'name' => 'Scrap Metal', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            ['ID' => 85, 'name' => 'Wires', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            ['ID' => 43, 'name' => 'Copper', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            ['ID' => 785, 'name' => 'Plastic', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            ['ID' => 201, 'name' => 'Electronics', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            ['ID' => 168, 'name' => 'Electronics x100', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            //x100
            ['ID' => 784, 'name' => 'Batteries', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            //x100
            ['ID' => 337, 'name' => 'Uranium ore', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
            //x100
            ['ID' => 919, 'name' => 'Engraved casings', 'category' => CategoryID::Resources->value, 'quality' => QualityID::Common->value, 'faction' => null],
        ];

        foreach ($resources as $resource) {
            $this->_dataManager->addItem(
                $resource['ID'],
                $resource['name'],
                $resource['category'],
                $resource['quality'],
                $resource['faction'],
            );
        }

        return 0;
    }
}
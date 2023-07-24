<?php

namespace App\Commands\TestCommands;

use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestOptimalRouteCommand extends Command
{
    protected static $defaultName = 'test.optimal_route';
    private PriceController $_priceController;

    public function __construct(PriceController $priceController)
    {
        $this->_priceController = $priceController;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $IDs = [
            497,

            482,
            483,
            112,

            379,
            389,

            383,
            395,

            108,

            163,
            126,

            186,
            176,

            109,
            172,
        ];

        $endBlockMessage = 'END BLOCK';
        $endBlockMessageLength = strlen($endBlockMessage);
        $sideSeparatorLength = intval(round(64 - $endBlockMessageLength) / 2);
        $leftSideSeparator = str_repeat('-', $sideSeparatorLength);
        $rightSideSeparator = str_repeat('-', $sideSeparatorLength + $endBlockMessageLength % 2);
        foreach ($IDs as $ID) {
            $this->_priceController->calculateOptimalRoute($ID);
            echo $leftSideSeparator . $endBlockMessage . $rightSideSeparator . PHP_EOL;
        }

        return 0;
    }
}
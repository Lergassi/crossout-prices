<?php

namespace App\Command\TestCommands;

use App\Service\ProfitCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestOptimalRouteCommand extends Command
{
    protected static $defaultName = 'test.optimal_route';
    private ProfitCalculator $_profitCalculator;

    public function __construct(ProfitCalculator $priceController)
    {
        $this->_profitCalculator = $priceController;
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
            $this->_profitCalculator->calculateOptimalRoute($ID);
            echo $leftSideSeparator . $endBlockMessage . $rightSideSeparator . PHP_EOL;
        }

        return 0;
    }
}
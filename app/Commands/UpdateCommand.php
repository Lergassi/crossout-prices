<?php

namespace App\Commands;

use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected static $defaultName = 'update';
    private DownloadPricesCommand $_downloadPricesCommand;
    private LoadPricesToDatabaseCommand $_loadPricesToDatabaseCommand;

    public function __construct(
        DownloadPricesCommand $downloadPricesCommand,
        LoadPricesToDatabaseCommand $loadPricesToDatabaseCommand,
    )
    {
        parent::__construct(static::$defaultName);
        $this->_downloadPricesCommand = $downloadPricesCommand;
        $this->_loadPricesToDatabaseCommand = $loadPricesToDatabaseCommand;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo '### update start ###' . PHP_EOL;

        $this->_downloadPricesCommand->execute($input, $output);
        $this->_loadPricesToDatabaseCommand->execute($input, $output);

        echo '### update end ###' . PHP_EOL;

        return 0;
    }
}
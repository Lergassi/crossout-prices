<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends Command
{
    protected static $defaultName = 'db.load';
    protected static $defaultDescription = 'Запись в бд из заранее загруженных данных.';

    private LoadItemsToDatabaseCommand $loadItemsToDatabaseCommand;
    private LoadRecipesToDatabaseCommand $loadRecipesToDatabaseCommand;
    private UpdatePricesInDatabaseCommand $updatePricesInDatabaseCommand;
    private CalculateProfitsCommand $calculateProfitsCommand;

    /**
     * @param LoadItemsToDatabaseCommand $loadItemsToDatabaseCommand
     * @param LoadRecipesToDatabaseCommand $loadRecipesToDatabaseCommand
     * @param UpdatePricesInDatabaseCommand $updatePricesInDatabaseCommand
     * @param CalculateProfitsCommand $calculateProfitsCommand
     */
    public function __construct(
        LoadItemsToDatabaseCommand $loadItemsToDatabaseCommand,
        LoadRecipesToDatabaseCommand $loadRecipesToDatabaseCommand,
        UpdatePricesInDatabaseCommand $updatePricesInDatabaseCommand,
        CalculateProfitsCommand $calculateProfitsCommand,
    )
    {
        parent::__construct(static::$defaultName);
        $this->loadItemsToDatabaseCommand = $loadItemsToDatabaseCommand;
        $this->loadRecipesToDatabaseCommand = $loadRecipesToDatabaseCommand;
        $this->updatePricesInDatabaseCommand = $updatePricesInDatabaseCommand;
        $this->calculateProfitsCommand = $calculateProfitsCommand;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadItemsToDatabaseCommand->execute($input, $output);
        $this->loadRecipesToDatabaseCommand->execute($input, $output);
        $this->updatePricesInDatabaseCommand->execute($input, $output);
        $this->calculateProfitsCommand->execute($input, $output);

        return 0;
    }
}
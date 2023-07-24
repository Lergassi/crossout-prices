<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Учитывать порядок вызова команд с предметами:
 *  - download items
 *  - load items
 *  - остальные команды работаю на основе загруженных предметов уже из бд
 */
class InitCommand extends Command
{
    protected static $defaultName = 'init';

    private DownloadItemsCommand $_downloadItemsCommand;
    private DownloadRecipesCommand $_downloadRecipesCommand;

    private ManualLoadItemsToDatabaseCommand $_manualLoadItemsToDatabaseCommand;
    private LoadItemsToDatabaseCommand $_loadItemsToDatabaseCommand;
    private LoadRecipesToDatabaseCommand $_loadRecipesToDatabaseCommand;

    private UpdateCommand $_updateCommand;

    public function __construct(
        DownloadItemsCommand             $downloadItemsCommand,
        DownloadRecipesCommand           $downloadRecipesCommand,
        DownloadPricesCommand            $downloadPricesCommand,
        ManualLoadItemsToDatabaseCommand $manualLoadItemsToDatabaseCommand,
        LoadItemsToDatabaseCommand       $loadItemsToDatabaseCommand,
        LoadRecipesToDatabaseCommand     $loadRecipesToDatabaseCommand,
        UpdateCommand $updateCommand,
    )
    {
        parent::__construct(static::$defaultName);

        $this->_downloadItemsCommand = $downloadItemsCommand;
        $this->_downloadRecipesCommand = $downloadRecipesCommand;

        $this->_manualLoadItemsToDatabaseCommand = $manualLoadItemsToDatabaseCommand;
        $this->_loadItemsToDatabaseCommand = $loadItemsToDatabaseCommand;
        $this->_loadRecipesToDatabaseCommand = $loadRecipesToDatabaseCommand;
        $this->_updateCommand = $updateCommand;
    }

    /*
     * todo: Сделать проверку была ли уже init команда.
     * todo: Варианты оптимизации.
     * - Загрузка рецептов происходит долго из-за интервала чтобы не попасть под возможную блокировку сервера. Возможно стоит полностью разделить download и load.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_downloadItemsCommand->execute($input, $output);

        $this->_manualLoadItemsToDatabaseCommand->execute($input, $output);
        $this->_loadItemsToDatabaseCommand->execute($input, $output);

        $this->_downloadRecipesCommand->execute($input, $output);

        $this->_loadRecipesToDatabaseCommand->execute($input, $output);

        //Цены должны загружаться в конце, чтобы быть оптимальными из-за долгой загрузки рецептов.
        $this->_updateCommand->execute($input, $output);

        return 0;
    }
}
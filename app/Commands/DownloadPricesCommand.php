<?php

namespace App\Commands;

use App\Services\Downloader;
use App\Services\PriceController;
use App\Services\ProjectPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadPricesCommand extends Command
{
    protected static $defaultName = 'download_prices';

    private string $_url;
    private string $_path;

    private Downloader $_downloader;

    public function __construct(Downloader $downloader, ProjectPath $projectPath)
    {
        $this->_url = 'https://crossoutdb.com/export?showtable=true&sellprice=true&buyprice=true&id=true&removedItems=true';
        $this->_path = $projectPath->build('data/prices', 'prices.html');

        $this->_downloader = $downloader;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Загрузка цен начата.' . PHP_EOL;

        $filesize = $this->_downloader->download($this->_url, $this->_path);

        echo 'Загрузка цен завершена.' . PHP_EOL;
        echo sprintf('Данные загружены в файл %s (size: %s).' . PHP_EOL, $this->_path, $filesize);

        return 0;
    }
}
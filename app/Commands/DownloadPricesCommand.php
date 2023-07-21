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
    private Downloader $_downloader;
    private ProjectPath $_projectPath;

    public function __construct(Downloader $downloader, ProjectPath $projectPath)
    {
        $this->_downloader = $downloader;
        $this->_projectPath = $projectPath;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'https://crossoutdb.com/export?showtable=true&sellprice=true&buyprice=true&id=true&removedItems=true';
//        $date = (new \DateTime())->format('d.m.Y_H:i:s');
        $path = $this->_projectPath->build('data/prices', 'prices.html');

        $filesize = $this->_downloader->download($url, $path);

        echo sprintf('Данные загружены в файл %s (size: %s).', $path, $filesize) . PHP_EOL;

        return 0;
    }
}
<?php

namespace App\Command;

use App\Service\Downloader;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadItemsCommand extends Command
{
    protected static $defaultName = 'download_items';

    private Downloader $downloader;

    public function __construct(
        Downloader $downloader,
    )
    {
        parent::__construct(static::$defaultName);
        $this->downloader = $downloader;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $_ENV['APP_PROJECT_ROOT'] ?? '';
        $targetDir = $projectDir. '/data/items';

        if (!file_exists($targetDir)) throw new \Exception(sprintf('Директория %s не найдена.', $targetDir));

        $categories = [
            CategoryID::Cabins->value,
            CategoryID::Weapons->value,
            CategoryID::Hardware->value,
            CategoryID::Movement->value,
            CategoryID::Resources->value,
        ];
        $urlPattern = 'https://crossoutdb.com/api/v1/items?category=%s';
        $delay = 250 * 1000;
        foreach ($categories as $category) {
            $targetFilepath = implode('/', [
                $targetDir,
                sprintf('%s.json', $category),
            ]);

            $filesize = $this->downloader->download(sprintf($urlPattern, $category), $targetFilepath);
            usleep($delay);
            echo sprintf('Загружено: %s в %s (size: %s).', $category, $targetFilepath, $filesize) . PHP_EOL;
        }

        echo 'Данные загружены.' . PHP_EOL;

        return 0;
    }
}
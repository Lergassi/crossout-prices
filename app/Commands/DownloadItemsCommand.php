<?php

namespace App\Commands;

use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadItemsCommand extends Command
{
    protected static $defaultName = 'download_items';

    public function __construct()
    {
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $_ENV['APP_PROJECT_ROOT'] ?? '';
        $targetDir = $projectDir. '/data';

        if (!file_exists($targetDir)) throw new \Exception(sprintf('Директория %s не найдена.', $targetDir));

        $categories = [
            CategoryID::Cabins->value,
            CategoryID::Weapons->value,
            CategoryID::Hardware->value,
            CategoryID::Movement->value,
        ];
        $delay = 250 * 1000;
        foreach ($categories as $category) {
            $targetFilepath = implode('/', [
                $targetDir,
                sprintf('%s.json', $category),
            ]);

            $this->_download($category, $targetFilepath);
            usleep($delay);
            echo sprintf('Загружено: %s в %s.', $category, $targetFilepath) . PHP_EOL;
        }

        echo 'Данные загружены.' . PHP_EOL;

        return 0;
    }

    private function _download(string $category, string $filepath): void
    {
        $url = 'https://crossoutdb.com/api/v1/items?category=' . $category;

        $ch = curl_init($url);
        $fp = fopen($filepath, 'w');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        if(curl_error($ch)) {
            fwrite($fp, curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);
    }
}
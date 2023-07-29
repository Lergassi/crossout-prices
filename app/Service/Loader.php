<?php

namespace App\Service;

class Loader
{
    public function load(string $path): string
    {
        if (!file_exists($path)) throw new \Exception(sprintf('Файл %s не найден.', $path));

        $fp = fopen($path, 'r');
        $content = fread($fp, filesize($path));
        fclose($fp);

        return $content;
    }

    /**
     * @deprecated todo: Убрать. Тут только загрузка файлов.
     * @param string $path
     * @return array
     * @throws \Exception
     */
    public function loadJson(string $path): array
    {
        $json = $this->load($path);

        $content = json_decode($json, JSON_UNESCAPED_UNICODE);
        if ($content === null) throw new \Exception(sprintf('Ошибка при обработки json: %s.', json_last_error_msg()));

        return $content;
    }
}
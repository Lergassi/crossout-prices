<?php

namespace App\Services;

class Downloader
{
    /**
     * todo: Сохранение в файл вынести в отдельный класс.
     * @return int Размер данных записанных в файл.
     */
    public function download(string $url, string $path): int
    {
        //todo: validate file_exists
        $fp = fopen($path, 'w');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        if(curl_error($ch)) {
            fwrite($fp, curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);

        return filesize($path);
    }
}
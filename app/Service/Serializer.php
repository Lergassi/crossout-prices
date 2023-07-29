<?php

namespace App\Service;

class Serializer
{
    public function encode(mixed $data /*todo: options*/): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function decode(string $json, ?bool $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        $content = json_decode($json, $associative, $depth, $flags);

        if ($content === null) throw new \Exception(sprintf('Ошибка при обработки json: %s.', json_last_error_msg()));

        return $content;
    }
}
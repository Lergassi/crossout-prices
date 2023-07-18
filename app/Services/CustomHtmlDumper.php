<?php

namespace App\Services;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

class CustomHtmlDumper extends HtmlDumper
{
    public function dump(Data $data, $output = null, array $extraDisplayOptions = []): ?string
    {
        $trace = debug_backtrace(-1);

        $caller = null;
        foreach ($trace as $key => $item) {
            if (
                isset($item['function']) && $item['function'] === 'dump' &&
                isset($item['class']) && $item['class'] === VarDumper::class
            ) {
                $caller = $trace[$key + 1];
                break;
            }
        }

        $callerStringPattern = '<span style="font-size: 14px; text-decoration: underline;">%s on line %s:</span>';
        if ($caller) {
            $callerString = sprintf($callerStringPattern, basename($caller['file']), $caller['line']);
        } else {
            $callerString = '<span style="color: red;">Ошибка при определении места вызова отладочной функции.</span>';
        }
        $this->echoLine($callerString, 0, '');

        return parent::dump($data, $output, $extraDisplayOptions);
    }
}
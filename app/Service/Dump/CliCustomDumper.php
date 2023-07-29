<?php

namespace App\Service\Dump;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

//todo: Объединить с HtmlCustomDumper (убрать копирование кода) и обновить gist/git.
class CliCustomDumper extends CliDumper
{
    public function dump(Data $data, $output = null): ?string
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

        $callerStringPattern = 'Dump in file %s on line %s:';
        if ($caller) {
            $callerString = sprintf($callerStringPattern, basename($caller['file']), $caller['line']);
        } else {
            $callerString = '<span style="color: red;">Ошибка при определении места вызова отладочной функции.</span>';
        }
        $this->echoLine($callerString, 0, '');

        return parent::dump($data, $output); // TODO: Change the autogenerated stub
    }
}
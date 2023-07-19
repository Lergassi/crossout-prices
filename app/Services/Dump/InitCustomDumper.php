<?php

namespace App\Services\Dump;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

class InitCustomDumper
{
    public function __construct()
    {
        $cloner = new VarCloner();
        $fallbackDumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg']) ? new CliCustomDumper() : new HtmlCustomDumper();
        $dumper = new ServerDumper('tcp://127.0.0.1:9912', $fallbackDumper, [
            'cli' => new CliContextProvider(),
            'source' => new SourceContextProvider(),
        ]);

        VarDumper::setHandler(function ($var) use ($cloner, $dumper) {
            $dumper->dump($cloner->cloneVar($var));
        });
    }
}
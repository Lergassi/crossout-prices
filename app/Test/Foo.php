<?php

namespace App\Test;

class Foo
{
    public string $a;

    public function __construct(string $a = '')
    {
        $this->a = $a;
    }
}
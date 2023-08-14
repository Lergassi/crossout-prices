<?php

namespace App\Test\TestInterfaceDefinition;

class One implements TestInterface
{
    public function hello(): string
    {
        return 'One';
    }
}
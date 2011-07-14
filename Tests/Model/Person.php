<?php

namespace CodeMeme\RulesBundle\Tests\Model;

class Person
{
    public $name;
    public $age;

    public function __construct($name = null, $age = null)
    {
        $this->name = $name;
        $this->age  = $age;
    }
}
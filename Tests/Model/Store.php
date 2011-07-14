<?php

namespace CodeMeme\RulesBundle\Tests\Model;

class Store
{
    public $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }
}
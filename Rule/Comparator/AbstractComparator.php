<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

abstract class AbstractComparator implements ComparatorInterface
{

    protected $expected;

    public function __construct($expected)
    {
        $this->expected = $expected;
    }

    public function getValue()
    {
        return $this->expected;
    }

}
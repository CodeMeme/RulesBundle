<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

interface ComparatorInterface
{

    public function __construct($expected);
    public function compare($actual);

}
<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Regex extends AbstractComparator
{
    public function compare($actual)
    {
        return (!preg_match($this->expected, $actual)) ? false : true;
    }
}
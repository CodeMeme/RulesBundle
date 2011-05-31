<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class LessThan extends AbstractComparator
{

    public function compare($actual)
    {
        return $actual < $this->expected;
    }

}
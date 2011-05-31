<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class GreaterThan extends AbstractComparator
{

    public function compare($actual)
    {
        return $actual > $this->expected;
    }

}
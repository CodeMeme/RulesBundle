<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Equals extends AbstractComparator
{

    public function compare($actual)
    {
        return $this->expected == $actual;
    }

}
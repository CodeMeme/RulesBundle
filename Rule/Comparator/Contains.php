<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Contains extends AbstractComparator
{

    public function compare($actual)
    {
        return stripos($actual, $this->expected) !== false;
    }

}
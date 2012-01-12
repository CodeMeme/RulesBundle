<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Matches extends AbstractComparator
{
    public function compare($actual)
    {
	    return (bool) preg_match($this->expected, $actual);
    }
}

<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class In extends AbstractComparator
{

    public function compare($actual)
    {
        return in_array($actual, $this->expected);
    }

}
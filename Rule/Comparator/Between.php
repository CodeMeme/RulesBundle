<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Between extends AbstractComparator
{

    public function compare($actual)
    {
        return in_array($actual, range($this->expected[0], $this->expected[1]));
    }

}

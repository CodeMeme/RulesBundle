<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Contains extends AbstractComparator
{

    public function compare($actual)
    {
        if (is_array($actual) || $actual instanceof \IteratorAggregate) {
            return in_array($this->expected, (array) $actual);
        }

        return stripos($actual, $this->expected) !== false;
    }

}

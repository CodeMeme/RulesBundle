<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Contains extends AbstractComparator
{

    public function compare($actual)
    {
        if(is_array($actual) || $actual instanceof \IteratorAggregate) {
            foreach($actual as $value) {
                if(in_array($this->expected, (array) $value)) {
                    return true;
                }
            }
            return false;
        }

        return stripos($actual, $this->expected) !== false;
    }

}

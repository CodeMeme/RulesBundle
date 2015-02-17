<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Contains extends AbstractComparator
{
    public function compare($actual)
    {
        if (is_array($actual) || $actual instanceof \IteratorAggregate) {
            foreach ($actual as $value) {
                if (stripos((string) $value, $this->expected) !== false) {
                    return true;
                }
            }

            return false;
        }

        return stripos($actual, $this->expected) !== false;
    }
}

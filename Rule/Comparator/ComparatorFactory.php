<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class ComparatorFactory
{

    public function createComparator($name, $value)
    {
        $class = sprintf('%s\\%s', __NAMESPACE__, ucfirst($name));
        
        return new $class($value);
    }

}
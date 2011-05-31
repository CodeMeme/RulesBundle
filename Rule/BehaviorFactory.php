<?php

namespace CodeMeme\RulesBundle\Rule;

use CodeMeme\RulesBundle\Rule\Comparator\ComparatorFactory;

class BehaviorFactory
{

    public function createBehavior($path, $conditions)
    {
        if (is_scalar($conditions)) {
            $conditions = array('equals' => $conditions);
        }
        
        $factory     = new ComparatorFactory;
        $comparators = array();
        
        foreach ($conditions as $type => $value) {
            $comparators[] = $factory->createComparator($type, $value);
        }
        
        return new Behavior($path, $comparators);
    }

}
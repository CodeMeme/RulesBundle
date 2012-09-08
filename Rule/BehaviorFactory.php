<?php

namespace CodeMeme\RulesBundle\Rule;

class BehaviorFactory
{
    private $comparatorClasses = array();

    public function addComparatorClass($typeName, $class)
    {
        $this->comparatorClasses[$typeName] = $class;
    }

    public function createBehavior($path, $conditions)
    {
        if (is_scalar($conditions)) {
            $conditions = array('equals' => $conditions);
        }

        $comparators = array();
        foreach ($conditions as $type => $value) {
            if (isset($this->comparatorClasses[$type])) {
                $class = $this->comparatorClasses[$type];
                $comparators[] = new $class($value);
            }
        }

        // @todo remove the very hard coupling to this single behavior mechanism
        return new Behavior($path, $comparators);

    }

}
<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

use CodeMeme\RulesBundle\Rule\Comparator\ComparatorFactory;
use Doctrine\Common\Collections\ArrayCollection;

class Not extends AbstractComparator
{

    private $comparators;

    public function __construct($expected)
    {
        if (is_scalar($expected)) {
            $expected = array('equals' => $expected);
        }
        
        $factory = new ComparatorFactory;
        
        $this->comparators = new ArrayCollection;
        
        foreach ($expected as $type => $value) {
            $this->comparators->add($factory->createComparator($type, $value));
        }
    }

    public function compare($actual)
    {
        return ! $this->comparators->forAll(function($i, $comparator) use ($actual) {
            return $comparator->compare($actual);
        });
    }

}
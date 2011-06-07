<?php

namespace CodeMeme\RulesBundle\Rule;

use CodeMeme\RulesBundle\Util\PropertyPath;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class Behavior
{

    private $path;

    private $comparators;

    public function __construct($path, $comparators = array())
    {
        $this->path  = is_scalar($path)
                     ? new PropertyPath($path)
                     : $path;
        
        $this->comparators = new ArrayCollection($comparators);
    }

    public function evaluate($target)
    {
        try {
            $actual = $this->path->getValue($target);
        } catch (UnexpectedTypeException $e) {
            return false;
        }
        
        return $this->comparators->forAll(function($i, $comparator) use ($actual) {
            return $comparator->compare($actual);
        });
    }

    public function modify($target)
    {
        foreach ($this->comparators as $comparator) {
            $this->path->setValue($target, $comparator->getValue());
        }
    }

    public function supports($target)
    {
        return $this->path->getElement(0) === current(array_keys($target));
    }

}
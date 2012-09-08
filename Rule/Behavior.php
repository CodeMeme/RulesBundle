<?php

namespace CodeMeme\RulesBundle\Rule;

use CodeMeme\RulesBundle\Util\PropertyPath;
use CodeMeme\RulesBundle\Rule\Comparator\ComparatorInterface;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\InvalidPropertyException;

/**
 * Behavior class, optimized for PropertyPath targets
 */
class Behavior
{

    private $path;

    private $root;

    private $comparators;

    public function __construct($path, $comparators)
    {
        $this->path  = is_scalar($path)
                     ? new PropertyPath($path)
                     : $path;
        $this->root  = new PropertyPath($this->path->getElement(0));

        $this->comparators = new ArrayCollection(is_array($comparators) ? $comparators : array($comparators));
    }

    public function evaluate($target)
    {
        try {
            $actual = $this->path->getValue($target);
        } catch (UnexpectedTypeException $e) {
            return false;
        }

        if (null === $actual) {
            return false;
        }

        return $this->comparators->forAll(function($i, $comparator) use ($actual) {
            return $comparator->compare($actual);
        });
    }

    public function modify($target)
    {
        foreach ($this->comparators as $comparator) {
            $value = ($comparator instanceof ComparatorInterface)
                   ? $comparator->getValue()
                   : $comparator;

            $this->path->setValue($target, $value);
        }
    }

    public function supports($target)
    {
        if (is_array($target)) {
            return array_key_exists($this->root->getElement(0), $target);
        } else {
            try {
                $this->root->getValue($target);
                return true;
            } catch (InvalidPropertyException $e) {
                return false;
            }
        }
    }

}
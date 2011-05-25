<?php

namespace CodeMeme\RulesBundle\RulesEngine;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class Rule
{

    private $name;

    private $aliases;

    private $conditions;

    private $actions;

    public function __construct()
    {
        $this->aliases    = new ArrayCollection;
        $this->conditions = new ArrayCollection;
        $this->actions    = new ArrayCollection;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    public function setAliases($aliases)
    {
        $this->aliases = $aliases;
        
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setCondition($key, $value)
    {
        $this->getConditions()->set($key, $value);
    }

    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
        
        return $this;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function setAction($key, $value)
    {
        $this->getActions()->set($key, $value);
    }

    public function setActions($actions)
    {
        $this->actions = $actions;
        
        return $this;
    }

    public function evaluate($targets)
    {
        if ($supported = $this->supports($targets)) {
            $this->modify($supported);
        }
    }

    public function modify($targets)
    {
        $aliases = $this->alias($targets);
        
        foreach ($this->getActions() as $path => $value) {
            foreach ($aliases as $pair) {
                foreach ($pair as $alias => $target) {
                    $path = new PropertyPath($path);
                    
                    if ($path->getElement(0) === $alias) {
                        $path->setValue($pair, $value);
                    }
                }
            }
        }
    }

    public function supports($targets)
    {
        $aliases = $this->alias($targets);
        
        $passed = array();
        $failed = array();
        
        $supported = $this->getConditions()->forAll(function ($path, $expected) use ($aliases, &$passed, &$failed) {
            // Example: array(array( 'form' => $form ), array( 'lead' => $lead ))
            foreach ($aliases as $pair) {
                // Example: array( 'form' => $form )
                foreach ($pair as $alias => $target) {
                    //  Example: lead.program.id
                    $path   = new PropertyPath($path);
                    
                    try {
                        // Only fully-fleshed objects are parsable
                        $actual = $path->getValue($pair);
                    } catch (UnexpectedTypeException $e) {
                        continue;
                    }
                    
                    // Only test targets that are aliased to the initial path ('form' => 'form.field.value')
                    if ($path->getElement(0) === $alias) {
                        
                        // Convert foo.bar: "baz" -> foo.bar: { 'equals': "baz" }
                        if (is_scalar($expected)) {
                            $expected = array('equals' => $expected);
                        }
                        
                        $allPassed = TRUE;
                        
                        foreach ($expected as $conditional => $value) {
                            switch ($conditional) {
                                case 'greaterThan':
                                    $allPassed = ($actual > $value);
                                    break;
                                case 'lessThan':
                                    $allPassed = ($actual < $value);
                                    break;
                                case 'equals':
                                    $allPassed = ($actual === $value);
                                    break;
                                default: throw new Exception("Bad conditional '$conditional'");
                            }
                            // Short-circuit the foreach once any condition fails
                            if (!$allPassed) {
                                break;
                            }
                        }
                        
                        if ($allPassed) {
                            if (! in_array($target, $passed)) {
                                $passed[] = $target;
                            }
                            
                            return true;
                        } else {
                            if (! in_array($target, $failed)) {
                                $failed[] = $target;
                            }
                        }
                    } else {
                        // Otherwise, continue to next alias + target pair
                        continue;
                    }
                }
            }
        });
        
        // Find any targets that neither failed nor passed & add them to the passed array
        foreach ($targets as $target) {
            if (! in_array($target, $failed) && ! in_array($target, $passed)) {
                $passed[] = $target;
            }
        }
        
        return $supported ? $passed : array();
    }

    private function alias($targets)
    {
        $aliases = array();
        
        foreach ($targets as $target) {
            foreach ($this->aliases as $alias => $expected) {
                if (null === $expected || $target instanceof $expected) {
                    $aliases[] = array($alias => $target);
                }
            }
        }
        
        return $aliases;
    }

}
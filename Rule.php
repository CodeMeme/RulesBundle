<?php

namespace CodeMeme\RulesBundle;

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
        
        return (Boolean) $supported;
    }

    public function modify($targets)
    {
        $aliases = $this->alias($targets);
        
        foreach ($this->getActions() as $action) {
            foreach ($aliases as $pair) {
                if ($action->supports($pair)) {
                    $action->modify($pair);
                }
            }
        }
    }

    public function supports($targets)
    {
        $aliases = $this->alias($targets);
        
        $passed = array();
        $failed = array();
        
        $supported = $this->getConditions()->forAll(function ($i, $condition) use ($aliases, &$passed, &$failed) {
            foreach ($aliases as $pair) {
                if ($condition->supports($pair)) {
                    if ($condition->evaluate($pair)) {
                        $passed += array_values($pair);
                        return true;
                    } else {
                        $failed += array_values($pair);
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
<?php

namespace CodeMeme\RulesBundle\RulesEngine;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Util\PropertyPath;

class Rule
{

    private $name;

    private $aliases;

    private $conditions;

    private $actions;

    public function __construct()
    {
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
        if ($targeted = $this->supports($targets)) {
            $this->modify($targeted);
        }
    }

    public function modify($targets)
    {
        $aliases = $this->alias($targets);
        
        $this->getActions()->forAll(function($path, $value) use ($aliases) {
            foreach ($aliases as $alias) {
                $path = new PropertyPath($path);
                $path->setValue($alias, $value);
            }
        });
        
    }

    public function supports($targets)
    {
        $aliases = $this->alias($targets);
        
        $targeted = array();
        
        $passed = $this->getConditions()->forAll(function ($path, $expected) use (&$targeted, $aliases) {
            foreach ($aliases as $alias) {
                $path = new PropertyPath($path);
                $actual = $path->getValue($alias);
                
                if ($actual === $expected) {
                    $targeted[] = current($alias);
                    return true;
                }
            }
        });
        
        return $passed ? $targeted : array();
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
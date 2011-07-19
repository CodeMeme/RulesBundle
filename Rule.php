<?php

namespace CodeMeme\RulesBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class Rule
{

    private $name;

    private $aliases;

    private $conditions;

    private $actions;

    private $fallbacks;

    public function __construct($conditions = array(), $actions = array(), $fallbacks = array(), $aliases = array())
    {
        $this->conditions = is_array($conditions)   ? new ArrayCollection($conditions)  : $conditions;
        $this->actions    = is_array($actions)      ? new ArrayCollection($actions)     : $actions;
        $this->fallbacks  = is_array($fallbacks)    ? new ArrayCollection($fallbacks)   : $fallbacks;
        $this->aliases    = is_array($aliases)      ? new ArrayCollection($aliases)     : $aliases;
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
        $this->conditions = is_array($conditions) ? new ArrayCollection($conditions) : $conditions;
        
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
        $this->actions = is_array($actions) ? new ArrayCollection($actions) : $actions;
        
        return $this;
    }

    public function getFallbacks()
    {
        return $this->fallbacks;
    }

    public function setFallbacks($fallbacks)
    {
        $this->fallbacks = $fallbacks;
        
        return $this;
    }

    public function evaluate($targets)
    {
        if ($supported = $this->supports($targets)) {
            $this->modify($supported, $this->getActions());
        } else if (! $this->getFallbacks()->isEmpty()) {
            $this->modify($this->alias($targets), $this->getFallbacks());
        }
        
        return (Boolean) $supported;
    }

    public function modify($targets, $actions)
    {
        foreach ($actions as $action) {
            foreach ($targets as $target) {
                if ($action->supports($target)) {
                    $action->modify($target);
                }
            }
        }
    }

    public function supports($targets)
    {
        if (!is_array($targets) && !$targets instanceof \IteratorAggregate) {
            $targets = array($targets);
        }

        if ($this->getConditions()->isEmpty()) {
            throw new \InvalidArgumentException(sprintf('Rule %s has no conditions', $this->getName()));
        } else if ($this->getActions()->isEmpty()) {
            throw new \InvalidArgumentException(sprintf('Rule %s has no actions', $this->getName()));
        }

        $aliased     = $this->alias($targets);
        $supported   = new ArrayCollection;
        $unsupported = $aliased;

        // Find targets that match all conditions or none
        $passed = $this->getConditions()->forAll(function($i, $condition) use ($aliased, &$supported, &$unsupported) {
            $matches = $aliased->filter(function($target) use ($condition, &$unsupported) {
                if ($condition->supports($target)) {
                    $unsupported->removeElement($target);
                }

                return $condition->supports($target) && $condition->evaluate($target);
            });

            foreach ($matches as $match) {
                $supported->add($match);
            }

            return !$matches->isEmpty();
        });

        if ($passed) {
            foreach ($unsupported as $target) {
                $supported->add($target);
            }
        }

        return $passed ? $supported : false;
    }

    private function alias($targets)
    {
        $aliased = new ArrayCollection;

        foreach ($targets as $target) {
            $found = false;

            foreach ($this->getAliases() as $alias => $instance) {
                // Target can match multiple aliases, or an alias that has no specific instance
                if (null === $instance || $target instanceof $instance) {
                    $found = true;
                    $aliased[] = array($alias => $target);
                }
            }

            if (! $found) {
                throw new \InvalidArgumentException(sprintf('No alias found for %s', is_object($target) ? get_class($target) : $target));
            }
        }

        return $aliased;
    }

}
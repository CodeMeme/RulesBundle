<?php

namespace CodeMeme\RulesBundle;

use Doctrine\Common\Collections\ArrayCollection;

class RulesEngine
{

    private $aliases;

    private $rules;

    public function __construct($rules = array())
    {
        $this->aliases  = new ArrayCollection;
        $this->rules    = new ArrayCollection;
    }

    public function setAlias($alias, $value)
    {
        $this->getAliases()->set($alias, $value);
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

    public function addRule($rule)
    {
        // Import existing aliases into the new rule
        foreach ($this->getAliases() as $alias => $value) {
            if (! $rule->getAliases()->containsKey($alias)) {
                $rule->getAliases()->set($alias, $value);
            }
        }
        
        $this->getRules()->add($rule);
    }

    public function addRules($rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function setRules($rules)
    {
        $this->rules = $rules;
        
        return $this;
    }

    public function evaluate($targets)
    {
        foreach ($this->getRules() as $rule) {
            $rule->evaluate($targets);
        }
    }

}
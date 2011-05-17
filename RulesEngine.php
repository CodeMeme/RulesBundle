<?php

namespace CodeMeme\RulesBundle;

use Doctrine\Common\Collections\ArrayCollection;

class RulesEngine
{

    private $rules;

    public function __construct($rules = array())
    {
        $this->rules = new ArrayCollection;
    }

    public function addRule($rule)
    {
        $this->getRules()->add($rule);
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
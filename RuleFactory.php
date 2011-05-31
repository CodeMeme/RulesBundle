<?php

namespace CodeMeme\RulesBundle;

use CodeMeme\RulesBundle\Rule\BehaviorFactory;

class RuleFactory
{

    public function createRule($name, $conditions, $actions)
    {
        $rule = new Rule;
        $rule->setName($name);
        
        $factory = new BehaviorFactory;
        
        foreach ($conditions as $condition => $values) {
            $rule->getConditions()->add(
                $factory->createBehavior($condition, $values)
            );
        }
        
        foreach ($actions as $action => $values) {
            $rule->getActions()->add(
                $factory->createBehavior($action, $values)
            );
        }
        
        return $rule;
    }

    public function createRules($configs)
    {
        $rules = array();
        
        foreach ($configs as $name => $config) {
            $rules[] = $this->createRule($name, $config['if'], $config['then']);
        }
        
        return $rules;
    }

}
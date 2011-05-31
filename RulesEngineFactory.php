<?php

namespace CodeMeme\RulesBundle;

class RulesEngineFactory
{

    public function createEngine(Array $rules = array(), Array $aliases = array())
    {
        $engine = new RulesEngine;
        
        foreach ($aliases as $key => $value) {
            $engine->setAlias($key, $value);
        }
        
        foreach ($rules as $rule) {
            $engine->addRule($rule);
        }
        
        return $engine;
    }

}
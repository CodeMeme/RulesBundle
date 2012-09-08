# Symfony2 Rules Bundle

## Introduction

A smart and extendable rule engine bundle for symfony2 PHP project's

## Requirements

* PHP >= 5.3.2
* Symfony >= 2.0.0 

## Installation

1. Add this bundle into your project *composer.json* file:
    
    ```json
    "require": {
      "ericclemmons/rules-bundle": "dev-master"
    },
    ```
1.1 Run the composer installation. E.g.: composer install

2. Register this bundle in your your *app/AppKernel.php*

    ```php
    <?php
    
    public function registerBundles()
    {
        $bundles = array(
            // ...some other bundles...
            new CodeMeme\RulesBundle\CodeMemeRulesBundle(),
        );
    ```
    
## Sample 

Process a simple PHP Object

### The class to process

For a better abstraction i have use a jedi as class with his force state as member variables.

  ```php
    <?php
    
    namespace [Namespace];
    
    class Jedi
    {
        public $forceSide;
        
        public $aggressivity;
    
        public function setForceSide($side)
        {
            $this->forceSide = $side;
        }
    
        public function getForceSide()
        {
            return $this->forceSide;
        }
        
        public function setAggressivity($value)
        {
            $this->aggressivity = $value;
        }
    
        public function getAggressivity()
        {
            return $this->aggressivity;
        }
    }
  ```

> Don't forget to replace the [Namespace]
  
### Rule Configuration

*app/config/config.yml*

  ```yml
  code_meme_rules:
      aliases:
          jedi: [Namespace]\Jedi
      rules:
          forceSideRule:
              if:
                  jedi.aggressivity: { GreaterThan: 99 }
              then:
                  jedi.forceSide: "dark side"
  ```

> Note: The default comparator is "equals".

### Rule Processing 

  ```php
    <?php
    $ruleEngine = $this->get('rules.engine');

    $lukeSkywalker = new Jedi();
    // setup a jung jedi without aggression
    $lukeSkywalker->setForceSide("light side");
    $lukeSkywalker->setAggressivity("0");

    $ruleEngine->evaluate($lukeSkywalker);
    echo "Luke is on the " . $lukeSkywalker->getForceSide() . " of the force. <br />";
        
    // incrase the aggressivity of the jedi
    $lukeSkywalker->setAggressivity("100");
    $ruleEngine->evaluate($lukeSkywalker);
    echo "Luke is on the " . $lukeSkywalker->getForceSide() . " of the force.";
  ```

The above example will output:

    Luke is on the light side of the force.
    Luke is on the dark side of the force.


Have a try.

    
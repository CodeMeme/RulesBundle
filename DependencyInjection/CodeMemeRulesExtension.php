<?php

namespace CodeMeme\RulesBundle\DependencyInjection;

use CodeMeme\RulesBundle\RulesEngine\Rule;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CodeMemeRulesExtension extends Extension
{

    public function load(Array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $engine = $container->getDefinition('rules.engine');
        
        foreach ($configs as $config) {
            if (isset($config['aliases'])) {
                foreach ($config['aliases'] as $alias => $value) {
                    $engine->addMethodCall('setAlias', array($alias, $value));
                }
            }
            
            if (isset($config['rules'])) {
                foreach ($config['rules'] as $name => $ruleConfig) {
                    $rule = new Definition('%rules.rule.class%');
                    
                    $rule->addMethodCall('setName', array($name));
                    
                    foreach ($ruleConfig['if'] as $path => $value) {
                        $rule->addMethodCall('setCondition', array($path, $value));
                    }
                    
                    foreach ($ruleConfig['then'] as $path => $value) {
                        $rule->addMethodCall('setAction', array($path, $value));
                    }
                    
                    // How the heck can I pass the definition itself, rather than a reference?
                    $key = 'rules.rule.' . md5(serialize($rule));
                    $container->setDefinition($key, $rule);
                    $engine->addMethodCall('addRule', array(new Reference($key)));
                }
            }
        }
    }

}
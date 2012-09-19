<?php

namespace CodeMeme\RulesBundle\DependencyInjection;

use CodeMeme\RulesBundle\RulesEngine\Rule;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CodeMemeRulesExtension extends Extension
{

    public function load(Array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rules.xml');
        
        $rules   = array();
        $aliases = array();
        
        foreach ($configs as $config) {
            if (isset($config['aliases'])) {
                $aliases += $config['aliases'];
            }
            
            if (isset($config['rules'])) {
                foreach ($config['rules'] as $name => $ruleConfig) {
                    $defName = sprintf(
                        'rules.rule.%s_%s',
                        substr(md5(serialize($ruleConfig)), 0, 5),
                        strtolower(preg_replace('/[^\w]/', '_', $name))
                    );
                    
                    $rule = $container->setDefinition($defName, new DefinitionDecorator('rules.rule'));
                    $rule->setArguments(array(
                        $name,
                        $ruleConfig['if'],
                        $ruleConfig['then'],
                    ));
                    
                    $rules[] = new Reference($defName);
                }
            }
        }
        
        $engine = $container->getDefinition('rules.engine');
        $engine->setArguments(array(
            $rules,
            $aliases,
        ));
    }

}
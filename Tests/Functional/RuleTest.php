<?php

namespace CodeMeme\RulesBundle\Tests\Functional;

use CodeMeme\RulesBundle\Rule;
use CodeMeme\RulesBundle\Rule\Behavior;
use CodeMeme\RulesBundle\Rule\Comparator\Equals;

use CodeMeme\RulesBundle\Tests\Model\Person;
use CodeMeme\RulesBundle\Tests\Model\Store;

class RuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider ruleProvider
     * @expectedException InvalidArgumentException
     */
    public function testRuleDoesNotSupportArrays($rule)
    {
        $rule->supports(array('nothing' => 'here'));
    }

    /**
     * @dataProvider ruleProvider
     * @expectedException InvalidArgumentException
     */
    public function testRuleThrowsExceptionWhenNoAliasExists($rule)
    {
        $rule->supports(new Store('CodeMeme'));
    }

    /**
     * @dataProvider ruleProvider
     */
    public function testRuleSupportsTargetsWithAllSupportedConditions($rule)
    {
        $this->assertTrue(!!$rule->supports(new Person('Eric')));
    }

    /**
     * @dataProvider ruleProvider
     */
    public function testRuleEvaluatesTargetsWithSupportedConditions($rule)
    {
        $person = new Person('Eric');

        $this->assertTrue($rule->evaluate($person));
        $this->assertEquals('New Name', $person->name);
    }

    /**
     * @dataProvider ruleProvider
     */
    public function testRuleSupportsSingleTargetWithSupportedConditions($rule)
    {
        $eric = new Person('Eric');
        $evan = new Person('Evan');

        $supported = $rule->supports(array($eric, $evan));

        $this->assertEquals(1, count($supported));
    }

    /**
     * @dataProvider ruleProvider
     */
    public function testRuleEvaluatesSingleTargetWithSupportedConditions($rule)
    {
        $eric = new Person('Eric');
        $evan = new Person('Evan');

        $modified = $rule->evaluate(array($eric, $evan));

        $this->assertEquals(1, count($modified));
        $this->assertEquals('New Name', $eric->name);
    }

    /**
     * @dataProvider storeRuleProvider
     */
    public function testRuleDoesSupportPartialSupportedConditions($rule)
    {
        $this->assertFalse(!!$rule->supports(new Person('John')));
        $this->assertFalse(!!$rule->supports(new Store('CodeMeme')));
    }

    /**
     * @dataProvider storeRuleProvider
     */
    public function testRuleSupportsTargetsWithSomeSupportedConditions($rule)
    {
        $person = new Person('Eric');
        $store  = new Store('CodeMeme');

        $this->assertEquals(2, count($rule->supports(array($person, $store))));
    }

    /**
     * @dataProvider storeRuleProvider
     */
    public function testRuleEvaluatesTargetsWithSomeSupportedConditions($rule)
    {
        $person = new Person('Eric');
        $store  = new Store('CodeMeme');

        $this->assertTrue($rule->evaluate(array($person, $store)));
        $this->assertEquals('New Store Name', $store->name);
    }

    /**
     * @dataProvider storeRuleProvider
     */
    public function testRuleDoesNotEvaluateTargetsWithSomeSupportedConditions($rule)
    {
        $person = new Person('John');
        $store  = new Store('CodeMeme');

        $this->assertFalse($rule->evaluate(array($person, $store)));
    }

    public function ruleProvider()
    {
        $rule = new Rule(
            array(new Behavior('person.name', new Equals('Eric'))),
            array(new Behavior('person.name', 'New Name')),
            array(),
            array('person' => 'CodeMeme\\RulesBundle\\Tests\\Model\\Person')
        );

        return array(
            array($rule)
        );
    }

    public function storeRuleProvider()
    {
        $rule = new Rule(
            array(new Behavior('person.name', new Equals('Eric'))),
            array(new Behavior('store.name', 'New Store Name')),
            array(),
            array(
                'person' => 'CodeMeme\\RulesBundle\\Tests\\Model\\Person',
                'store'  => 'CodeMeme\\RulesBundle\\Tests\\Model\\Store',
            )
        );

        return array(
            array($rule)
        );
    }

}

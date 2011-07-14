<?php

namespace CodeMeme\RulesBundle\Tests\Functional;

use CodeMeme\RulesBundle\Rule\Behavior;
use CodeMeme\RulesBundle\Rule\Comparator\Equals;

use CodeMeme\RulesBundle\Tests\Model\Person;

class BehaviorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorDoesNotSupportEmptyArray($behavior)
    {
        $this->assertFalse($behavior->supports(array()));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorSupportsArrayProperty($behavior)
    {
        $this->assertTrue($behavior->supports(array('name' => 'Anything')));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorEvaluatesArrayProperty($behavior)
    {
        $this->assertFalse($behavior->evaluate(array('name' => 'Nothing')));
        $this->assertTrue($behavior->evaluate(array('name' => 'Eric')));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorDoesNotSupportMissingArrayProperty($behavior)
    {
        $this->assertFalse($behavior->supports(array('nothing' => 'here')));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorSupportsObjectProperty($behavior)
    {
        $this->assertTrue($behavior->supports((Object) array('name' => 'Anything')));
        $this->assertTrue($behavior->supports(new Person('Eric')));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorEvaluatesObjectProperty($behavior)
    {
        $this->assertFalse($behavior->evaluate((Object) array('name' => 'Nothing')));
        $this->assertFalse($behavior->evaluate(new Person('Nothing')));
        $this->assertTrue($behavior->evaluate(new Person('Eric')));
    }

    /**
     * @dataProvider behaviorProvider
     */
    public function testBehaviorDoesNotSupportMissingObjectProperty($behavior)
    {
        $this->assertFalse($behavior->supports((Object) array('nothing' => 'here')));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorSupportsNestedArrayProperty($behavior)
    {
        $this->assertTrue($behavior->supports(array('person' => new Person('Eric'))));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorEvaluatesNestedArrayProperty($behavior)
    {
        $this->assertFalse($behavior->evaluate(array('person' => new Person('Nothing'))));
        $this->assertTrue($behavior->evaluate(array('person' => new Person('Eric'))));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorDoesNotSupportNestedArrayProperty($behavior)
    {
        $this->assertFalse($behavior->supports(array('nothing' => new Person('here'))));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorSupportsNestedObjectProperty($behavior)
    {
        $this->assertTrue($behavior->supports((Object) array('person' => new Person('Eric'))));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorEvaluatesNestedObjectProperty($behavior)
    {
        $this->assertFalse($behavior->evaluate((Object) array('person' => new Person('Nothing'))));
        $this->assertTrue($behavior->evaluate((Object) array('person' => new Person('Eric'))));
    }

    /**
     * @dataProvider nestedBehaviorProvider
     */
    public function testBehaviorDoesNotSupportNestedObjectProperty($behavior)
    {
        $this->assertFalse($behavior->supports((Object) array('nothing' => new Person('here'))));
    }

    public function behaviorProvider()
    {
        return array(
            array(new Behavior('name', new Equals('Eric'))),
        );
    }

    public function nestedBehaviorProvider()
    {
        return array(
            array(new Behavior('person.name', new Equals('Eric'))),
        );
    }

}

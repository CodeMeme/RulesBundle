<?php

namespace CodeMeme\RulesBundle\Tests\Functional\Rule\Comparator;

use CodeMeme\RulesBundle\Rule\Comparator\Contains;
use CodeMeme\RulesBundle\Rule\Comparator\AbstractComparator;
use Doctrine\Common\Collections\ArrayCollection;

class ContainsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider containsProvider
     */
    public function testInheritance($actual, $expected)
    {
        $comparator = new Contains($actual);
        $this->assertTrue($comparator instanceof AbstractComparator);
    }

    /**
     * @dataProvider containsProvider
     * @depends testInheritance
     */
    public function testString($actual, $expected)
    {
        $comparator = new Contains($expected['stringOne']);
        $this->assertTrue($comparator->compare($actual['stringOne']));

        $comparator = new Contains($expected['stringTwo']);
        $this->assertTrue($comparator->compare($actual['stringTwo']));

        // test partial string
        $comparator = new Contains(substr($expected['stringTwo'], -3));
        $this->assertTrue($comparator->compare($actual['stringTwo']));

        // test lowercase
        $comparator = new Contains(strtolower($expected['stringTwo']));
        $this->assertTrue($comparator->compare($actual['stringTwo']));
    }

    /**
     * @dataProvider containsProvider
     * @depends testInheritance
     */
    public function testStringFailure($actual, $expected)
    {
        $comparator = new Contains("No Match");
        $this->assertFalse($comparator->compare($actual['stringTwo']));
    }

    /**
     * @dataProvider containsProvider
     * @depends testInheritance
     */
    public function testArray($actual, $expected)
    {
        $comparator = new Contains($expected['stringOne']);
        $this->assertTrue($comparator->compare($actual));

        $comparator = new Contains($expected['stringTwo']);
        $this->assertTrue($comparator->compare($actual));
    }

    /**
     * @dataProvider containsProvider
     * @depends testInheritance
     */
    public function testArrayFailure($actual, $expected)
    {
        $comparator = new Contains("No Match");
        $this->assertFalse($comparator->compare($actual));
    }

    /**
     * @dataProvider containsProvider
     * @depends testInheritance
     */
    public function testArrayCollection($actual, $expected)
    {
        $ac = new ArrayCollection($actual);

        $this->assertTrue($ac instanceof ArrayCollection);
        $this->assertTrue($ac instanceof \IteratorAggregate);

        $comparator = new Contains($expected['stringOne']);
        $this->assertTrue($comparator->compare($ac));
    }

    public function containsProvider()
    {
        return array(
            array(
                array(
                    'stringOne' => 'Post',
                    'stringTwo' => 'Philadelphia',
                ),
                array(
                    'stringOne' => 'Post',
                    'stringTwo' => 'Philadelphia',
                ),
            ),
        );
    }
}

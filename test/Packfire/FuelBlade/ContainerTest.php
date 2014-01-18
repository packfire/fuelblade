<?php
namespace Packfire\FuelBlade;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-24 at 14:30:39.
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Container;
        $this->object['test.number'] = 5;
        $this->object['test'] = function ($c) {
            return (object) array('value' => $c['test.number']);
        };
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Packfire\FuelBlade\Container::offsetExists
     * @covers Packfire\FuelBlade\Container::loadValue
     */
    public function testOffsetExists()
    {
        $this->assertTrue($this->object->offsetExists('test.number'));
        $this->assertTrue(isset($this->object['test.number']));
    }

    /**
     * @covers Packfire\FuelBlade\Container::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals(5, $this->object->offsetGet('test.number'));
        $this->assertEquals(5, $this->object->offsetGet('test')->value);
        $this->assertEquals(5, $this->object['test.number']);
    }

    public function testOffsetGetContainer()
    {
        $this->object['obj'] = $this->object->share(
            new ConsumerFixture()
        );
        $this->assertEquals($this->object, $this->object['obj']->container());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetGetFail()
    {
        $this->object->offsetGet('none');
    }

    public function testOffsetSet()
    {
        $this->assertEquals(5, $this->object->offsetGet('test')->value);
        $this->object->offsetSet('test.number', 10);
        $this->assertEquals(10, $this->object->offsetGet('test')->value);
    }

    public function testOffsetUnset()
    {
        unset($this->object['test']);
        $this->assertArrayNotHasKey('test', $this->object);
    }

    public function testCopy()
    {
        $obj = (object) array(
            'text' => 'Hello there!'
        );

        $func = $this->object->copy($obj);
        $this->assertInstanceOf('stdClass', $func());
        $value = $func();
        $this->assertEquals($obj, $value);
    }

    public function testFunc()
    {
        $func = function () {
            return 'test';
        };

        $this->object['func'] = $this->object->func($func);
        $this->assertEquals($func, $this->object['func']);
    }

    public function testAlias()
    {
        $this->object['cool'] = $this->object->alias('test.number');
        $this->assertEquals($this->object['test.number'], $this->object['cool']);
    }

    public function testInstance()
    {
        $this->object['obj'] = $this->object->instance('\\stdClass');
        $this->assertInstanceOf('\\stdClass', $this->object['obj']);
        $obj1 = $this->object['obj'];
        $obj2 = $this->object['obj'];
        $this->assertTrue($obj2 !== $obj1);
    }

    public function testInstanceArgs()
    {
        $container = new Container();
        $container['Packfire\\FuelBlade\\ContainerInterface'] = $container;

        $container['fixture'] = $container->instance('Packfire\\FuelBlade\\ConsumerFixture');
        $obj = $container['fixture'];

        $this->assertEquals($container, $obj->container());
    }

    public function testInstantiate()
    {
        $obj = $this->object->instantiate('\\stdClass');
        $this->assertInstanceOf('\\stdClass', $obj);
    }

    public function testInstantiateArgs()
    {
        $container = new Container();
        $container['Packfire\\FuelBlade\\ContainerInterface'] = $container;

        $fixture = $container->instantiate('Packfire\\FuelBlade\\ConsumerFixture');

        $this->assertEquals($container, $fixture->container());
    }

    public function testInstantiateArgsParams()
    {
        $container = new Container();
        $container['Packfire\\FuelBlade\\ContainerInterface'] = $container;
        $fixture = $container->instantiate('Packfire\\FuelBlade\\ConsumerFixture', array('state' => 5));

        $this->assertEquals(5, $fixture->state());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInstanceArgsFail1()
    {
        $container = new Container();
        $container['fixture'] = $container->instance('Packfire\\FuelBlade\\ConsumerFixture');
        $obj = $container['fixture'];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInstanceArgsFail2()
    {
        $container = new Container();
        $container['fixture'] = $container->instance('Packfire\\FuelBlade\\ServiceLoadingException');
        $obj = $container['fixture'];
    }

    public function testShare()
    {
        $this->object['obj'] = $this->object->share(
            function () {
                return new \stdClass();
            }
        );
        $this->assertInstanceOf('\\stdClass', $this->object['obj']);
        $obj1 = $this->object['obj'];
        $obj2 = $this->object['obj'];
        $this->assertTrue($obj2 === $obj1);
    }

    public function testValue()
    {
        $this->assertInstanceOf('\\Closure', $this->object->value('test'));
        $this->assertEquals(5, $this->object->value('test.number'));
    }
}

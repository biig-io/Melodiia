<?php

namespace Biig\Melodiia\Test\Bridge\Symfony\Form;

use Biig\Melodiia\Bridge\Symfony\Form\DomainObjectsDataMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;

class DomainObjectsDataMapperTest extends TestCase
{
    public function testItIsInstanceOfDataMapper()
    {
        $this->assertInstanceOf(DataMapperInterface::class, new DomainObjectsDataMapper());
    }

    public function testItExtendsPropertyPathDataMapper()
    {
        $this->assertInstanceOf(PropertyPathMapper::class, new DomainObjectsDataMapper());
    }

    public function testItBuildObjectFromForm()
    {
        $mapper = new DomainObjectsDataMapper();

        $dispatcher = $this->prophesize(EventDispatcherInterface::class)->reveal();
        $form1 = new Form((new FormConfigBuilder('hello', null, $dispatcher))->setData('world')->getFormConfig());
        $form2 = new Form((new FormConfigBuilder('foo', null, $dispatcher))->setData('bar')->getFormConfig());

        $form = new \ArrayIterator(['hello' => $form1, 'foo' => $form2]);

        $obj = $mapper->createObject($form, FakeValueObject::class);
        $this->assertInstanceOf(FakeValueObject::class, $obj);
        $this->assertEquals('bar', $obj->getFoo());
    }

    public function testItBuildObjectFromFormAndAdditionalData()
    {
        $mapper = new DomainObjectsDataMapper();

        $dispatcher = $this->prophesize(EventDispatcherInterface::class)->reveal();
        $form1 = new Form((new FormConfigBuilder('hello', null, $dispatcher))->setData('world')->getFormConfig());

        $form = new \ArrayIterator(['hello' => $form1]);

        $obj = $mapper->createObject($form, SecondFakeValueObject::class, ['item' => new FakeValueObject('foo', 'bar')]);
        $this->assertInstanceOf(SecondFakeValueObject::class, $obj);
        $this->assertInstanceOf(FakeValueObject::class, $obj->getItem());
    }
}

class FakeValueObject
{
    private $hello;
    private $foo;

    public function __construct($hello, $foo)
    {
        $this->hello = $hello;
        $this->foo = $foo;
    }

    public function getHello()
    {
        return $this->hello;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}

class SecondFakeValueObject
{
    private $hello;
    private $item;

    public function __construct($hello, FakeValueObject $item)
    {
        $this->hello = $hello;
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getHello()
    {
        return $this->hello;
    }

    /**
     * @return FakeValueObject
     */
    public function getItem(): FakeValueObject
    {
        return $this->item;
    }
}

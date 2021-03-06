<?php

namespace Biig\Melodiia\Test\Crud\Event;

use Biig\Melodiia\Crud\Event\CrudEvent;
use Biig\Melodiia\Crud\Event\CustomResponseEvent;
use Biig\Melodiia\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

class CustomResponseEventTest extends TestCase
{
    public function testItImplementsCrudEvent()
    {
        $event = new CustomResponseEvent(new \stdClass());
        $this->assertInstanceOf(CrudEvent::class, $event);
    }

    public function testItReturnsNoResponseByDefault()
    {
        $event = new CustomResponseEvent(new \stdClass());
        $this->assertFalse($event->hasCustomResponse());
        $this->assertNull($event->getResponse());
    }

    public function testItReturnsResponseIfSpeficied()
    {
        $event = new CustomResponseEvent(new \stdClass());
        $event->setResponse($response = $this->prophesize(ApiResponse::class)->reveal());
        $this->assertTrue($event->hasCustomResponse());
        $this->assertEquals($response, $event->getResponse());
    }
}

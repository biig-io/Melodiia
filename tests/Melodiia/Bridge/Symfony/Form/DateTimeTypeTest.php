<?php

namespace Biig\Melodiia\Test\Bridge\Symfony\Form;

use Biig\Melodiia\Bridge\Symfony\Form\Type\DateTimeType;
use Symfony\Component\Form\Test\TypeTestCase;

class DateTimeTypeTest extends TypeTestCase
{
    public function testItChangesDefaultOptions()
    {
        $form = $this->factory->create(DateTimeType::class, null, [
        ]);
        $form->submit('2010-09-30T00:00:00');
        $dateTime = new \DateTimeImmutable('2010-09-30');

        $this->assertEquals($dateTime, $form->getData());
    }

    protected function getTestedType()
    {
        return DateTimeType::class;
    }
}

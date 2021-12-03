<?php

namespace Tests\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Setter;
use PHPUnit\Framework\TestCase;

class HydratorAttributeProcessorTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Setter('bar')]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'azerty']);
        $this->assertSame(['bar' => 'azerty'], $form->value());
    }
}

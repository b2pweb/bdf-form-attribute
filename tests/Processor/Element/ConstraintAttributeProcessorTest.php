<?php

namespace Tests\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotEqualTo;

class ConstraintAttributeProcessorTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Length(min: 5), NotEqualTo('azerty')]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'bar']);

        $this->assertFalse($form->valid());
        $this->assertEquals('This value is too short. It should have 5 characters or more.', $form->foo->error()->global());

        $form->submit(['foo' => 'azerty']);

        $this->assertFalse($form->valid());
        $this->assertEquals('This value should not be equal to "azerty".', $form->foo->error()->global());

        $form->submit(['foo' => 'aqwzsx']);
        $this->assertTrue($form->valid());
    }
}

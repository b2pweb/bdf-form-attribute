<?php

namespace Tests\Form\Annotation\Constraint;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Constraint\CustomConstraint;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class CustomConstraintTest extends TestCase
{
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[CustomConstraint('validateFoo', message: 'Foo length must be a multiple of 2')]
            public StringElement $foo;

            public function validateFoo($value): bool
            {
                return strlen($value) % 2 === 0;
            }
        };

        $form->submit(['foo' => 'a']);

        $this->assertFalse($form->valid());
        $this->assertEquals('Foo length must be a multiple of 2', $form->foo->error()->global());

        $form->submit(['foo' => 'abcd']);

        $this->assertTrue($form->valid());
        $this->assertNull($form->foo->error()->global());
    }
}

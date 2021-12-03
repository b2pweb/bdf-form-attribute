<?php

namespace Tests\Form\Attribute\Constraint;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Constraint\Satisfy;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length;

class SatisfyTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Satisfy(Length::class, ['min' => 3])]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'ab']);
        $this->assertFalse($form->valid());
        $this->assertEquals(['foo' => 'This value is too short. It should have 3 characters or more.'], $form->error()->toArray());

        $form->submit(['foo' => 'abc']);
        $this->assertTrue($form->valid());
    }
}

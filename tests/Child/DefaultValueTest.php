<?php

namespace Tests\Form\Attribute\Child;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\DefaultValue;
use Bdf\Form\Leaf\IntegerElement;
use PHPUnit\Framework\TestCase;

class DefaultValueTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AttributeForm {
            #[DefaultValue(42)]
            public IntegerElement $v;
        };

        $form->submit([]);
        $this->assertSame(42, $form->v->value());
    }
}

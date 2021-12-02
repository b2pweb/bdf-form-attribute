<?php

namespace Tests\Form\Annotation\Child;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Child\DefaultValue;
use Bdf\Form\Leaf\IntegerElement;
use PHPUnit\Framework\TestCase;

class DefaultValueTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[DefaultValue(42)]
            public IntegerElement $v;
        };

        $form->submit([]);
        $this->assertSame(42, $form->v->value());
    }
}

<?php

namespace Tests\Form\Annotation\Button;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Button\Value;
use Bdf\Form\Button\ButtonInterface;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[Value('foo')]
            public ButtonInterface $button;
        };

        $this->assertFalse($form->submit(['button' => 'ok'])->button->clicked());
        $this->assertTrue($form->submit(['button' => 'foo'])->button->clicked());
    }
}

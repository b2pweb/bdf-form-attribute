<?php

namespace Tests\Form\Attribute\Button;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Button\Value;
use Bdf\Form\Button\ButtonInterface;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Value('foo')]
            public ButtonInterface $button;
        };

        $this->assertFalse($form->submit(['button' => 'ok'])->button->clicked());
        $this->assertTrue($form->submit(['button' => 'foo'])->button->clicked());
    }
}

<?php

namespace Tests\Form\Attribute\Child;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\GetSet;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class GetSetTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[GetSet]
            public StringElement $a;

            #[GetSet('c')]
            public StringElement $b;
        };

        $form->submit(['a' => 'z', 'b' => 'e']);
        $this->assertSame(['a' => 'z', 'c' => 'e'], $form->value());

        $form->import(['a' => 'aaa', 'c' => 'ccc']);
        $this->assertSame('aaa', $form->a->value());
        $this->assertSame('ccc', $form->b->value());
    }
}

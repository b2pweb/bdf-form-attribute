<?php

namespace Tests\Form\Attribute\Child;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\Dependencies;
use Bdf\Form\Attribute\Element\CallbackTransformer;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class DependenciesTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            public StringElement $foo;
            #[Dependencies('foo', 'bar'), CallbackTransformer(fromHttp: 'bazTransformer')]
            public StringElement $baz;
            public StringElement $bar;

            public function bazTransformer($value)
            {
                return $this->foo->value() . $value . $this->bar->value();
            }
        };

        $form->submit(['foo' => 'a', 'bar' => 'b', 'baz' => 'c']);
        $this->assertSame('acb', $form->baz->value());
    }
}

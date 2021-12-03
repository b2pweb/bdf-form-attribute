<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\CallbackTransformer;
use Bdf\Form\Attribute\Element\IgnoreTransformerException;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class IgnoreTransformerExceptionTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AttributeForm {
            #[IgnoreTransformerException, CallbackTransformer('transform')]
            public StringElement $foo;

            #[IgnoreTransformerException(false), CallbackTransformer('transform')]
            public StringElement $bar;

            public function transform()
            {
                throw new \Exception('My error');
            }
        };

        $form->submit(['foo' => 'a', 'bar' => 'b']);

        $this->assertFalse($form->valid());
        $this->assertEquals(['bar' => 'My error'], $form->error()->toArray());
    }
}

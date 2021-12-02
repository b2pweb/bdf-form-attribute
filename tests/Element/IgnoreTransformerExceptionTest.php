<?php

namespace Tests\Form\Annotation\Element;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Element\CallbackTransformer;
use Bdf\Form\Annotation\Element\IgnoreTransformerException;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class IgnoreTransformerExceptionTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
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

<?php

namespace Tests\Form\Annotation\Form;

use Bdf\Form\Aggregate\FormInterface;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Form\CallbackGenerator;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Setter;
use PHPUnit\Framework\TestCase;

class CallbackGeneratorTest extends TestCase
{
    public function test()
    {
        $form = new #[CallbackGenerator('generate')] class extends AnnotationForm {
            #[Setter]
            public StringElement $foo;

            public function generate(FormInterface $form)
            {
                return (object) ['foo' => null, 'bar' => 'a'];
            }
        };

        $form->submit(['foo' => 'b']);
        $this->assertEquals((object) ['foo' => 'b', 'bar' => 'a'], $form->value());
    }
}

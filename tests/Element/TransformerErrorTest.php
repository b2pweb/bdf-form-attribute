<?php

namespace Tests\Form\Annotation\Element;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Element\CallbackTransformer;
use Bdf\Form\Annotation\Element\TransformerError;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\Validator\TransformerExceptionConstraint;
use http\Message;
use PHPUnit\Framework\TestCase;

class TransformerErrorTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[CallbackTransformer('transformer'), TransformerError('my message')]
            public StringElement $foo;
            #[CallbackTransformer('transformer'), TransformerError(message: 'bar', code: 'BAR_ERROR')]
            public StringElement $bar;

            public function transformer()
            {
                throw new \Exception('My error');
            }
        };

        $form->submit(['foo' => 'a', 'bar' => 'b']);

        $this->assertEquals(['foo' => 'my message', 'bar' => 'bar'], $form->error()->toArray());
        $this->assertEquals('BAR_ERROR', $form->error()->children()['bar']->code());
    }

    /**
     *
     */
    public function test_with_callback()
    {
        $form = new class extends AnnotationForm {
            #[CallbackTransformer('transformer'), TransformerError(validationCallback: 'handleError')]
            public StringElement $foo;

            public function transformer()
            {
                throw new \Exception('My error');
            }

            public function handleError($value, TransformerExceptionConstraint $constraint)
            {
                if ($value === 'a') {
                    return false;
                }

                $constraint->message = str_repeat($value, 5);
                $constraint->code = 'FOO';

                return true;
            }
        };

        $form->submit(['foo' => 'a']);
        $this->assertTrue($form->valid());

        $form->submit(['foo' => 'b']);
        $this->assertEquals(['foo' => 'bbbbb'], $form->error()->toArray());
        $this->assertEquals('FOO', $form->error()->children()['foo']->code());
    }
}

<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\CallbackTransformer;
use Bdf\Form\Attribute\Element\TransformerError;
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
        $form = new class extends AttributeForm {
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
        $form = new class extends AttributeForm {
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

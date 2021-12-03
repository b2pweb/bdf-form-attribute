<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\CallbackTransformer;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class CallbackTransformerTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AttributeForm {
            #[CallbackTransformer('fooTransformer')]
            public StringElement $foo;

            #[CallbackTransformer(fromHttp: 'inTransformer', toHttp: 'outTransformer')]
            public StringElement $bar;

            public function fooTransformer($value, StringElement $input, bool $toPhp)
            {
                return json_encode([$value, $toPhp]);
            }

            public function inTransformer($value, StringElement $input)
            {
                return json_encode(['in', $value]);
            }

            public function outTransformer($value, StringElement $input)
            {
                return json_encode(['out', $value]);
            }
        };

        $form->submit(['foo' => 'a', 'bar' => 'b']);

        $this->assertEquals('["a",true]', $form->foo->value());
        $this->assertEquals('["in","b"]', $form->bar->value());

        $view = $form->view();

        $this->assertEquals('["[\"a\",true]",false]', $view['foo']->value());
        $this->assertEquals('["out","[\"in\",\"b\"]"]', $view['bar']->value());
    }
}

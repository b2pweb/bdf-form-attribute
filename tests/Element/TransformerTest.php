<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\Transformer;
use Bdf\Form\ElementInterface;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Transformer(ATransformer::class, ['A'])]
            public StringElement $foo;
        };

        $form->submit(['foo' => '_']);
        $this->assertEquals('A_', $form->foo->value());

        $view = $form->view();
        $this->assertEquals('A_A', $view['foo']->value());
    }
}

class ATransformer implements TransformerInterface
{
    public function __construct(
        public string $c
    ) {
    }

    public function transformToHttp($value, ElementInterface $input)
    {
        return $value . $this->c;
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return $this->c . $value;
    }
}

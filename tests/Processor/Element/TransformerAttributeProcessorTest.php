<?php

namespace Tests\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Processor\Element\TransformerAttributeProcessor;
use Bdf\Form\ElementInterface;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class TransformerAttributeProcessorTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[ATransformer, BTransformer]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'azerty']);
        $this->assertEquals('azertyBA', $form->foo->value());
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ATransformer implements TransformerInterface
{
    public function transformToHttp($value, ElementInterface $input)
    {
        // TODO: Implement transformToHttp() method.
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return $value . 'A';
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class BTransformer implements TransformerInterface
{
    public function transformToHttp($value, ElementInterface $input)
    {
        // TODO: Implement transformToHttp() method.
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return $value . 'B';
    }
}

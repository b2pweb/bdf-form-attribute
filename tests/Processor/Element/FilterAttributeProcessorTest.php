<?php

namespace Tests\Form\Attribute\Processor\Element;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Child\ChildInterface;
use Bdf\Form\Filter\FilterInterface;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class FilterAttributeProcessorTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[AFilter, BFilter]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'bar']);
        $this->assertEquals('barAB', $form->foo->value());
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class AFilter implements FilterInterface
{
    public function filter($value, ChildInterface $input, $default)
    {
        return $value . 'A';
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class BFilter implements FilterInterface
{
    public function filter($value, ChildInterface $input, $default)
    {
        return $value . 'B';
    }
}

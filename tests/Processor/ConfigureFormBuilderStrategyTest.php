<?php

namespace Tests\Form\Attribute\Processor;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Processor\ConfigureFormBuilderStrategy;
use Bdf\Form\Attribute\Processor\Element\ElementAttributeProcessorInterface;
use Bdf\Form\Attribute\Processor\ReflectionProcessor;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class ConfigureFormBuilderStrategyTest extends TestCase
{
    public function test_registerElementAttributeProcessor()
    {
        $strategy = new ConfigureFormBuilderStrategy();
        $strategy->registerElementAttributeProcessor(new class implements ElementAttributeProcessorInterface {
            public function type(): string { return Foo::class; }

            public function process(ChildBuilderInterface $builder, object $attribute): void
            {
                $builder->default('Foo');
            }
        });

        $form = new class(null, new ReflectionProcessor($strategy)) extends AttributeForm {
            #[Foo]
            public StringElement $foo;
        };

        $form->submit([]);

        $this->assertEquals('Foo', $form->foo->value());
    }
}

#[\Attribute]
class Foo {}

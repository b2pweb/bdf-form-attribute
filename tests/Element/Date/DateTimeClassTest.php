<?php

namespace Tests\Form\Attribute\Element\Date;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\Date\DateTimeClass;
use Bdf\Form\Attribute\Element\Raw;
use Bdf\Form\Attribute\Processor\AttributesProcessorInterface;
use Bdf\Form\Leaf\Date\DateTimeElement;
use Bdf\Form\Leaf\FloatElement;
use Tests\Form\Attribute\TestCase;

class DateTimeClassTest extends TestCase
{
    /**
     * @dataProvider provideAttributesProcessor
     */
    public function test(AttributesProcessorInterface $processor)
    {
        $form = new class(null, $processor) extends AttributeForm {
            #[DateTimeClass(MyCustomDate::class)]
            public DateTimeElement $foo;
        };

        $form->submit(['foo' => '2020-11-02T15:21:31+0000']);

        $this->assertEquals(new MyCustomDate('2020-11-02T15:21:31'), $form->foo->value());
        $this->assertInstanceOf(MyCustomDate::class, $form->foo->value());
    }

    public function test_code_generator()
    {
        $form = new class extends AttributeForm {
            #[DateTimeClass(MyCustomDate::class)]
            public DateTimeElement $foo;
        };

        $this->assertGenerated(<<<'PHP'
namespace Generated;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Aggregate\FormInterface;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Processor\AttributesProcessorInterface;
use Bdf\Form\Attribute\Processor\PostConfigureInterface;
use Bdf\Form\Leaf\Date\DateTimeElement;
use Tests\Form\Attribute\Element\Date\MyCustomDate;

class GeneratedConfigurator implements AttributesProcessorInterface, PostConfigureInterface
{
    /**
     * {@inheritdoc}
     */
    function configureBuilder(AttributeForm $form, FormBuilderInterface $builder): ?PostConfigureInterface
    {
        $foo = $builder->add('foo', DateTimeElement::class);
        $foo->className(MyCustomDate::class);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    function postConfigure(AttributeForm $form, FormInterface $inner): void
    {
        $form->foo = $inner['foo']->element();
    }
}

PHP
            , $form
        );
    }
}

class MyCustomDate extends \DateTime
{
}

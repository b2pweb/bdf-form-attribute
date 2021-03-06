<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\Transformer;
use Bdf\Form\Attribute\Processor\AttributesProcessorInterface;
use Bdf\Form\ElementInterface;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\Transformer\TransformerInterface;
use Tests\Form\Attribute\TestCase;

class TransformerTest extends TestCase
{
    /**
     * @dataProvider provideAttributesProcessor
     */
    public function test(AttributesProcessorInterface $processor)
    {
        $form = new class(null, $processor) extends AttributeForm {
            #[Transformer(ATransformer::class, ['A'])]
            public StringElement $foo;
        };

        $form->submit(['foo' => '_']);
        $this->assertEquals('A_', $form->foo->value());

        $view = $form->view();
        $this->assertEquals('A_A', $view['foo']->value());
    }

    public function test_code_generator()
    {
        $form = new class extends AttributeForm {
            #[Transformer(ATransformer::class, ['A'])]
            public StringElement $foo;
        };

        $this->assertGenerated(<<<'PHP'
namespace Generated;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Aggregate\FormInterface;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Processor\AttributesProcessorInterface;
use Bdf\Form\Attribute\Processor\PostConfigureInterface;
use Bdf\Form\Leaf\StringElement;
use Tests\Form\Attribute\Element\ATransformer;

class GeneratedConfigurator implements AttributesProcessorInterface, PostConfigureInterface
{
    /**
     * {@inheritdoc}
     */
    function configureBuilder(AttributeForm $form, FormBuilderInterface $builder): ?PostConfigureInterface
    {
        $foo = $builder->add('foo', StringElement::class);
        $foo->transformer(new ATransformer('A'));

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

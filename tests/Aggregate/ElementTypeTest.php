<?php

namespace Tests\Form\Annotation\Aggregate;

use Bdf\Form\Aggregate\ArrayElement;
use Bdf\Form\Annotation\Aggregate\ElementType;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Form\Generates;
use Bdf\Form\Leaf\IntegerElement;
use Bdf\Form\Leaf\IntegerElementBuilder;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Setter;
use PHPUnit\Framework\TestCase;

class ElementTypeTest extends TestCase
{
    /**
     *
     */
    public function test_simple()
    {
        $form = new class extends AnnotationForm {
            #[ElementType(IntegerElement::class), Setter]
            public ArrayElement $values;
        };

        $form->submit(['values' => ['123', '456', '789']]);
        $this->assertTrue($form->valid());

        $this->assertSame(['values' => [123, 456, 789]], $form->value());
    }

    /**
     *
     */
    public function test_with_configurator()
    {
        $form = new class extends AnnotationForm {
            #[ElementType(IntegerElement::class, "configureField"), Setter]
            public ArrayElement $values;

            public function configureField(IntegerElementBuilder $builder): void
            {
                $builder->min(200);
            }
        };

        $form->submit(['values' => ['123', '456', '789']]);
        $this->assertFalse($form->valid());

        $this->assertEquals(['values' => [0 => 'This value should be greater than or equal to 200.']], $form->error()->toArray());
    }

    /**
     *
     */
    public function test_with_embedded()
    {
        $form = new class extends AnnotationForm {
            #[ElementType(EmbeddedForm::class), Setter]
            public ArrayElement $values;
        };

        $form->submit(['values' => [['a' => 'az', 'b' => 'er'], ['a' => 'ty', 'b' => 'ui']]]);
        $this->assertTrue($form->valid());

        $this->assertEquals(['values' => [new Struct('az', 'er'), new Struct('ty', 'ui')]], $form->value());
    }
}

#[Generates(Struct::class)]
class EmbeddedForm extends AnnotationForm
{
    #[Setter]
    public StringElement $a;
    #[Setter]
    public StringElement $b;
}

class Struct
{
    public function __construct(
        public ?string $a = null,
        public ?string $b = null,
    ) {}
}

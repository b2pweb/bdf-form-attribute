<?php

namespace Bdf\Form\Attribute\Aggregate;

use Attribute;
use Bdf\Form\Aggregate\ArrayElementBuilder;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementInterface;

/**
 * Attribute for define the array element type
 * You can also define a configuration method (not required)
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call one of those :
 * <code>
 * $builder->array('values')->element(IntegerElement::class, [$this, 'myConfigurator']);
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AttributeForm
 * {
 *     #[ElementType(IntegerElement::class, 'configureValueItem')]
 *     private ArrayElement $values;
 *
 *     // The method must be public and take the builder as parameter
 *     public function configureValueItem(IntegerElementBuilder $builder)
 *     {
 *         $builder->min(5); // Configure the element
 *     }
 * }
 * </code>
 *
 * @see ArrayElementBuilder::element() The called method
 *
 * @implements ChildBuilderAttributeInterface<ArrayElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ElementType implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * The form element class name
         *
         * @var class-string<ElementInterface>
         */
        public string $elementType,

        /**
         * The element configuration method name
         * This method must be defined on the form class, and with public visibility
         *
         * @var literal-string|null
         */
        public ?string $configurator = null
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $configurator = $this->configurator ? [$form, $this->configurator] : null;
        $builder->element($this->elementType, $configurator);
    }
}

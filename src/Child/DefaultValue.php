<?php

namespace Bdf\Form\Attribute\Child;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Define the input default value
 * The value must be the PHP value (i.e. the parsed HTTP value)
 *
 * Note: this attribute cannot be repeated
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->float('foo')->default(12.3);
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AttributeForm
 * {
 *     #[DefaultValue(12.3)]
 *     private FloatElement $foo;
 * }
 * </code>
 *
 * @see ChildBuilderInterface::default() The called method
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue implements ChildBuilderAttributeInterface
{
    public function __construct(
        public mixed $default
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $builder->default($this->default);
    }
}

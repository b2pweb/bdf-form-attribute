<?php

namespace Bdf\Form\Annotation\Child;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
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
 * class MyForm extends AnnotationForm
 * {
 *     #[DefaultValue(12.3)]
 *     private FloatElement $foo;
 * }
 * </code>
 *
 * @see ChildBuilderInterface::default() The called method
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue implements ChildBuilderAnnotationInterface
{
    public function __construct(
        public mixed $default
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->default($this->default);
    }
}

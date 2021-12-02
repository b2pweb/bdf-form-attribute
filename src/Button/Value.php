<?php

namespace Bdf\Form\Annotation\Button;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Button\ButtonBuilderInterface;

/**
 * Attribute for define the button value, used to check if the button is clicked
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->button('btn')->value('Foo');
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Value('Foo')]
 *     private ButtonInterface $btn;
 * }
 * </code>
 *
 * @see ButtonBuilderInterface::value() The called method
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Value implements ButtonBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The button HTTP value
         */
        public string $value,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnButtonBuilder(AnnotationForm $form, ButtonBuilderInterface $builder): void
    {
        $builder->value($this->value);
    }
}

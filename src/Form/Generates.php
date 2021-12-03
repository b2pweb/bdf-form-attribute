<?php

namespace Bdf\Form\Annotation\Form;

use Attribute;
use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Annotation\AnnotationForm;

/**
 * Define the value generator of the form, using a class name
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->generates(MyEntity::class);
 * </code>
 *
 * Usage:
 * <code>
 * #[Generates(MyEntity::class)]
 * class MyForm extends AnnotationForm
 * {
 *     // ...
 * }
 * </code>
 *
 * @see FormBuilderInterface::generates() The called method
 * @see ValueGenerator
 * @see CallbackGenerator For generate using a custom method
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Generates implements FormBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The entity class name to generate
         *
         * @var class-string
         */
        public string $className,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnFormBuilder(AnnotationForm $form, FormBuilderInterface $builder): void
    {
        $builder->generates($this->className);
    }
}
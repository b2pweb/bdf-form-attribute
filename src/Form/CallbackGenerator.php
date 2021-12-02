<?php

namespace Bdf\Form\Annotation\Form;

use Attribute;
use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Aggregate\Value\ValueGenerator;
use Bdf\Form\Annotation\AnnotationForm;

/**
 * Define the value generator of the form, using a callback method
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->generates([$this, 'generateValue']);
 * </code>
 *
 * Usage:
 * <code>
 * #[CallbackGenerator('generateValue')]
 * class MyForm extends AnnotationForm
 * {
 *     public function generateValue(FormInterface $form)
 *     {
 *         return new Foo();
 *     }
 * }
 * </code>
 *
 * @see FormBuilderInterface::generates() The called method
 * @see ValueGenerator
 * @see Generates For generate with a simple class name
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class CallbackGenerator implements FormBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The method name use for generate the form value
         * This method should be public, and declared on the form class, following the prototype :
         * `public function (FormInterface $form): mixed`
         */
        public string $callback,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnFormBuilder(AnnotationForm $form, FormBuilderInterface $builder): void
    {
        $builder->generates([$form, $this->callback]);
    }
}

<?php

namespace Bdf\Form\Annotation\Constraint;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Define a custom constraint for an element, using a validation method
 *
 * Note: prefer use directly the constraint as attribute
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->integer('foo')->satisfy(MyConstraint::class, $options);
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Satisfy(MyConstraint::class, ['foo' => 'bar'])]
 *     private IntegerElement $foo;
 * }
 * </code>
 *
 * @see ElementBuilderInterface::satisfy() The called method
 * @see Constraint
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Satisfy implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The constraint class name
         *
         * @var class-string<Constraint>
         */
        public string $constraint,

        /**
         * Constraint's constructor options
         *
         * @var mixed|null
         */
        public mixed $options = null
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->satisfy($this->constraint, $this->options);
    }
}

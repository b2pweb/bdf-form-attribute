<?php

namespace Bdf\Form\Annotation\Aggregate;

use Attribute;
use Bdf\Form\Aggregate\ArrayElementBuilder;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Annotation\Constraint\Satisfy;
use Bdf\Form\Child\ChildBuilderInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Add a constraint on the whole array element
 * Use Satisfy, or directly the constraint as attribute for add a constraint on one array item
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->array('values')->arrayConstraints(MyConstraint::class, $options);
 * </code>
 *
 * @todo custom array constraint
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[ArrayConstraint(Unique::class, ['message' => 'My error'])]
 *     private ArrayElement $values;
 * }
 * </code>
 *
 * @see Satisfy Attribute for add constraint for items
 * @see ArrayElementBuilder::arrayConstraint() The called method
 *
 * @implements ChildBuilderAnnotationInterface<ArrayElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ArrayConstraint implements ChildBuilderAnnotationInterface
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
        $builder->arrayConstraint($this->constraint, $this->options);
    }
}

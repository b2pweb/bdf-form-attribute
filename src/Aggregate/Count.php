<?php

namespace Bdf\Form\Annotation\Aggregate;

use Attribute;
use Bdf\Form\Aggregate\ArrayElementBuilder;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Symfony\Component\Validator\Constraints\Count as CountConstraint;

/**
 * Add a Count constraint on the array element
 *
 * This attribute is equivalent to call one of those :
 * <code>
 * $builder->array('values')->count(['min' => 3]);
 * $builder->array('values')->arrayConstraint(new Count(min: 3));
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Count(min: 3, max: 42)]
 *     private ArrayElement $values;
 * }
 * </code>
 *
 * @see CountConstraint The used constraint
 * @see ArrayElementBuilder::arrayConstraint() The called method
 * @see ArrayElementBuilder::count() Equivalent method call
 *
 * @implements ChildBuilderAnnotationInterface<ArrayElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Count extends CountConstraint implements ChildBuilderAnnotationInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->arrayConstraint($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return CountConstraint::class . 'Validator';
    }
}

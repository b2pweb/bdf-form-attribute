<?php

namespace Bdf\Form\Attribute\Aggregate;

use Attribute;
use Bdf\Form\Aggregate\ArrayElementBuilder;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
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
 * class MyForm extends AttributeForm
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
 * @implements ChildBuilderAttributeInterface<ArrayElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Count extends CountConstraint implements ChildBuilderAttributeInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
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

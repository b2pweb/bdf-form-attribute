<?php

namespace Bdf\Form\Attribute\Aggregate;

use Attribute;
use Bdf\Form\Aggregate\ArrayElementBuilder;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Attribute\Constraint\Satisfy;
use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Nette\PhpGenerator\Literal;
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
 * Usage:
 * <code>
 * class MyForm extends AttributeForm
 * {
 *     #[ArrayConstraint(Unique::class, ['message' => 'My error'])]
 *     private ArrayElement $values;
 * }
 * </code>
 *
 * @see Satisfy Attribute for add constraint for items
 * @see ArrayElementBuilder::arrayConstraint() The called method
 * @see CallbackArrayConstraint Use for a custom method validation
 *
 * @implements ChildBuilderAttributeInterface<ArrayElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ArrayConstraint implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * The constraint class name
         *
         * @var class-string<Constraint>
         * @readonly
         */
        private string $constraint,
        /**
         * Constraint's constructor options
         *
         * @var mixed|null
         * @readonly
         */
        private mixed $options = null
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $builder->arrayConstraint($this->constraint, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCodeForChildBuilder(string $name, AttributesProcessorGenerator $generator, AttributeForm $form): void
    {
        $constraint = $generator->useAndSimplifyType($this->constraint);

        $generator->line('$?->arrayConstraint(?::class, ?);', [$name, new Literal($constraint), $this->options]);
    }
}

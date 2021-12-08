<?php

namespace Bdf\Form\Attribute\Constraint;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Nette\PhpGenerator\Literal;
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
 * class MyForm extends AttributeForm
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
class Satisfy implements ChildBuilderAttributeInterface
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
         * @var array|null|string
         */
        public mixed $options = null
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $builder->satisfy($this->constraint, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCodeForChildBuilder(string $name, AttributesProcessorGenerator $generator, AttributeForm $form): void
    {
        $type = $generator->useAndSimplifyType($this->constraint);
        $generator->line('$?->satisfy(?::class, ?);', [$name, new Literal($type), $this->options]);
    }
}

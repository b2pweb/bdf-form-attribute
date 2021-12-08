<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementBuilderInterface;
use Nette\PhpGenerator\Literal;
use ReflectionAttribute;
use Symfony\Component\Validator\Constraint;

/**
 * Add the constraint by calling satisfy
 *
 * @see ElementBuilderInterface::satisfy()
 * @see Constraint
 *
 * @implements ElementAttributeProcessorInterface<Constraint>
 */
final class ConstraintAttributeProcessor implements ElementAttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return Constraint::class;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void
    {
        $builder->satisfy($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCode(string $name, AttributesProcessorGenerator $generator, ReflectionAttribute $attribute): void
    {
        /** @var class-string<Constraint> $constraint */
        $constraint = $attribute->getName();
        $generator->line('$?->satisfy(?);', [$name, $generator->new($constraint, $attribute->getArguments())]);
    }
}

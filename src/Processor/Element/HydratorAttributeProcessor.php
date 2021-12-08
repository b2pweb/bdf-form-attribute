<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\PropertyAccess\HydratorInterface;
use Nette\PhpGenerator\Literal;

/**
 * Define as hydrator by calling hydrator()
 *
 * @see ChildBuilderInterface::hydrator()
 * @see HydratorInterface
 *
 * @implements ElementAttributeProcessorInterface<HydratorInterface>
 */
final class HydratorAttributeProcessor implements ElementAttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return HydratorInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void
    {
        $builder->hydrator($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCode(string $name, AttributesProcessorGenerator $generator, \ReflectionAttribute $attribute): void
    {
        // @todo refactor
        /** @var class-string<HydratorInterface> $constraint */
        $constraint = $attribute->getName();
        $generator->line('$?->hydrator(?);', [$name, $generator->new($constraint, $attribute->getArguments())]);
    }
}

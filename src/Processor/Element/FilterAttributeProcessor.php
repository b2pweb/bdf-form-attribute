<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Filter\FilterInterface;
use Nette\PhpGenerator\Literal;

/**
 * Add the filter by calling filter()
 *
 * @see FilterInterface
 * @see ChildBuilderInterface::filter()
 *
 * @implements ElementAttributeProcessorInterface<FilterInterface>
 */
final class FilterAttributeProcessor implements ElementAttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return FilterInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void
    {
        $builder->filter($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCode(string $name, AttributesProcessorGenerator $generator, \ReflectionAttribute $attribute): void
    {
        // @todo refactor
        /** @var class-string<FilterInterface> $constraint */
        $constraint = $attribute->getName();
        $generator->line('$?->filter(?);', [$name, $generator->new($constraint, $attribute->getArguments())]);
    }
}

<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementBuilderInterface;
use Bdf\Form\Transformer\TransformerInterface;
use Nette\PhpGenerator\Literal;

/**
 * Add the transformer by calling transformer()
 *
 * @see ElementBuilderInterface::transformer()
 * @see TransformerInterface
 *
 * @implements ElementAttributeProcessorInterface<TransformerInterface>
 */
final class TransformerAttributeProcessor implements ElementAttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return TransformerInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void
    {
        $builder->transformer($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCode(string $name, AttributesProcessorGenerator $generator, \ReflectionAttribute $attribute): void
    {
        // @todo refactor
        /** @var class-string<TransformerInterface> $constraint */
        $constraint = $attribute->getName();
        $generator->line('$?->transformer(?);', [$name, $generator->new($constraint, $attribute->getArguments())]);
    }
}

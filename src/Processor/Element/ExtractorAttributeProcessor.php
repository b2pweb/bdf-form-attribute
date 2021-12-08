<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\PropertyAccess\ExtractorInterface;

/**
 * Define the extractor by calling extract()
 *
 * @see ExtractorInterface
 * @see ChildBuilderInterface::extractor()
 *
 * @implements ElementAttributeProcessorInterface<ExtractorInterface>
 */
final class ExtractorAttributeProcessor implements ElementAttributeProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return ExtractorInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void
    {
        $builder->extractor($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCode(string $name, AttributesProcessorGenerator $generator, \ReflectionAttribute $attribute): void
    {
        // @todo refactor
        /** @var class-string<ExtractorInterface> $constraint */
        $constraint = $attribute->getName();
        $generator->line('$?->extractor(?);', [$name, $generator->new($constraint, $attribute->getArguments())]);
    }
}

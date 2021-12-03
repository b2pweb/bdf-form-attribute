<?php

namespace Bdf\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\Processor\ConfigureFormBuilderStrategy;
use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Process an attribute type to configure the child builder
 *
 * Note: if the attribute implements ChildBuilderAttributeInterface, the element attribute process will not be called
 *
 * @template T as object
 *
 * @see ConfigureFormBuilderStrategy::registerElementAttributeProcessor() For register the processor instance
 */
interface ElementAttributeProcessorInterface
{
    /**
     * The handled attribute class name
     *
     * @return class-string<T>
     */
    public function type(): string;

    /**
     * Apply the attribute on the child builder
     *
     * @param ChildBuilderInterface<\Bdf\Form\ElementBuilderInterface> $builder The element builder
     * @param T $attribute The attribute instance
     *
     * @return void
     *
     * @see \ReflectionAttribute::newInstance() $attribute is created using this method
     */
    public function process(ChildBuilderInterface $builder, object $attribute): void;
}

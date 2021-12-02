<?php

namespace Bdf\Form\Annotation\Element;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementBuilderInterface;
use Bdf\Form\Transformer\TransformerInterface;

/**
 * Add a transformer on the element, using a transformer class
 *
 * Note: it's preferred to use directly the transformer as attribute on the element property
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->string('foo')->transformer(new MyTransformer(...$arguments));
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Transformer(MyTransformer::class, ['foo', 'bar'])]
 *     private IntegerElement $foo;
 * }
 * </code>
 *
 * @see ElementBuilderInterface::transformer() The called method
 * @see CallbackTransformer For use custom methods as transformer instead of class
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Transformer implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The transformer class name
         *
         * @var class-string<TransformerInterface>
         */
        public string $transformerClass,

        /**
         * Arguments to provide on the transformer constructor
         *
         * @var array
         */
        public array $constructorArguments = [],
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->transformer(new $this->transformerClass(...$this->constructorArguments));
    }
}

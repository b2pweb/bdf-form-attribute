<?php

namespace Bdf\Form\Annotation\Child;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Transformer\TransformerInterface;

/**
 * Add a model transformer on the child, using a transformer class
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->string('foo')->modelTransformer(new MyTransformer(...$arguments));
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[ModelTransformer(MyTransformer::class, ['foo', 'bar'])]
 *     private IntegerElement $foo;
 * }
 * </code>
 *
 * @see ChildBuilderInterface::modelTransformer() The called method
 * @see CallbackModelTransformer For use custom methods as transformer instead of class
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ModelTransformer implements ChildBuilderAnnotationInterface
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
        $builder->modelTransformer(new $this->transformerClass(...$this->constructorArguments));
    }
}

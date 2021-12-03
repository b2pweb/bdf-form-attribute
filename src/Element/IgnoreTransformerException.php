<?php

namespace Bdf\Form\Annotation\Element;

use Attribute;
use Bdf\Form\AbstractElementBuilder;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Util\ValidatorBuilderTrait;

/**
 * Ignore errors generated by transformers
 *
 * If the error is ignored, when a transformer failed, the original (i.e. raw HTTP) data will be used,
 * and the standard validation process will be performed on it
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->string('foo')->ignoreTransformerException();
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[MyTransformer, IgnoreTransformerException]
 *     private StringElement $foo;
 * }
 * </code>
 *
 * @see ValidatorBuilderTrait::ignoreTransformerException() The called method
 *
 * @implements ChildBuilderAnnotationInterface<AbstractElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class IgnoreTransformerException implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * Ignore or not transformer errors
         */
        public bool $ignore = true,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->ignoreTransformerException($this->ignore);
    }
}
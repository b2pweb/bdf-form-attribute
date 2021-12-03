<?php

namespace Bdf\Form\Attribute\Element;

use Attribute;
use Bdf\Form\AbstractElementBuilder;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
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
 * class MyForm extends AttributeForm
 * {
 *     #[MyTransformer, IgnoreTransformerException]
 *     private StringElement $foo;
 * }
 * </code>
 *
 * @see ValidatorBuilderTrait::ignoreTransformerException() The called method
 *
 * @implements ChildBuilderAttributeInterface<AbstractElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class IgnoreTransformerException implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * Ignore or not transformer errors
         */
        public bool $ignore = true,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $builder->ignoreTransformerException($this->ignore);
    }
}

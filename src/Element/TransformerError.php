<?php

namespace Bdf\Form\Annotation\Element;

use Attribute;
use Bdf\Form\AbstractElementBuilder;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Transformer\TransformerInterface;

/**
 * Fine grain configure error triggered by transformers
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->string('foo')
 *     ->transformerErrorMessage('Foo is in invalid format')
 *     ->transformerErrorCode('FOO_FORMAT_ERROR')
 *     ->transformerExceptionValidation([$this, 'fooTransformerExceptionValidation'])
 * ;
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[MyTransformer, TransformerError(message: 'Foo is in invalid format', code: 'FOO_FORMAT_ERROR')]
 *     private StringElement $foo;
 * }
 * </code>
 *
 * @see ValidatorBuilderTrait::transformerErrorMessage() The called method when message parameter is provided
 * @see ValidatorBuilderTrait::transformerErrorCode() The called method when code parameter is provided
 * @see ValidatorBuilderTrait::transformerExceptionValidation() The called method when validationCallback parameter is provided
 *
 * @implements ChildBuilderAnnotationInterface<AbstractElementBuilder>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class TransformerError implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The error message to show when transformer fail
         */
        public ?string $message = null,

        /**
         * The error code to provide when transformer fail
         */
        public ?string $code = null,

        /**
         * Method name to use for validate the transformer exception
         *
         * This method must be public and declared on the form class, and follow the prototype :
         * `public function ($value, TransformerExceptionConstraint $constraint, ElementInterface $element): bool`
         *
         * If the method return false, the exception will be ignored
         * Else, the method should fill `TransformerExceptionConstraint` with error message and code to provide the custom error
         */
        public ?string $validationCallback = null,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        if ($this->message) {
            $builder->transformerErrorMessage($this->message);
        }

        if ($this->code) {
            $builder->transformerErrorCode($this->code);
        }

        if ($this->validationCallback) {
            $builder->transformerExceptionValidation([$form, $this->validationCallback]);
        }
    }
}

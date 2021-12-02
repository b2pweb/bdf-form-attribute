<?php

namespace Bdf\Form\Annotation\Child;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Annotation\Element\CallbackTransformer;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementInterface;
use Bdf\Form\Transformer\TransformerInterface;

/**
 * Add a model transformer on the child element, by using method
 *
 * Transformation to entity and to input can be separated in two different method.
 * Those methods take the value and the input element as parameters, and should return the transformed value
 * If dedicated methods are not used, but the unified one, the third parameter is provided :
 * - on true the transformation is to the entity
 * - on false the transformation is to the input
 *
 * This attribute is equivalent to call :
 * <code>
 * // For unified callback
 * $builder->string('foo')->modelTransformer([$this, 'myTransformer']);
 *
 * // When using two methods (toEntity: 'transformFooToEntity', toInput: 'transformFooToInput')
 * $builder->string('foo')->modelTransformer(function ($value, ElementInterface $input, bool $toEntity) {
 *     return $toEntity ? $this->transformFooToEntity($value, $input) : $this->transformFooToInput($value, $input);
 * });
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[CallbackModelTransformer(toEntity: 'fooToModel', toInput: 'fooToInput')]
 *     private IntegerElement $foo;
 *
 *     // With unified transformer (same as above)
 *     #[CallbackModelTransformer('barTransformer')]
 *     private IntegerElement $bar;
 *
 *     public function fooToModel(int $value, IntegerElement $input): string
 *     {
 *         return dechex($value);
 *     }
 *
 *     public function fooToInput(string $value, IntegerElement $input): int
 *     {
 *         return hexdec($value);
 *     }
 *
 *     public function barTransformer($value, IntegerElement $input, bool $toEntity)
 *     {
 *         return $toEntity ? dechex($value) : hexdec($value);
 *     }
 * }
 * </code>
 *
 * @see ChildBuilderInterface::modelTransformer() The called method
 * @see ModelTransformer For use a transformer class as model transformer
 * @see CallbackTransformer For use transformer in same way, but for http transformer intead of model one
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class CallbackModelTransformer implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * Method name use to define the unified transformer method
         * If defined, the other parameters will be ignored
         */
        public ?string $callback = null,

        /**
         * Method name use to define the transformation process from input value to the entity
         */
        public ?string $toEntity = null,

        /**
         * Method name use to define the transformation process from entity value to input
         */
        public ?string $toInput = null,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        if ($this->callback) {
            $builder->modelTransformer([$form, $this->callback]);
            return;
        }

        $builder->modelTransformer(new class($form, $this) implements TransformerInterface {
            public function __construct(
                private AnnotationForm           $form,
                private CallbackModelTransformer $annotation,
            ) {}

            /**
             * {@inheritdoc}
             */
            public function transformToHttp($value, ElementInterface $input)
            {
                if (!$this->annotation->toInput) {
                    return $value;
                }

                return $this->form->{$this->annotation->toInput}($value, $input);
            }

            /**
             * {@inheritdoc}
             */
            public function transformFromHttp($value, ElementInterface $input)
            {
                if (!$this->annotation->toEntity) {
                    return $value;
                }

                return $this->form->{$this->annotation->toEntity}($value, $input);
            }
        });
    }
}

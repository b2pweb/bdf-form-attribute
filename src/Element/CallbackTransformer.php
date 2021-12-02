<?php

namespace Bdf\Form\Annotation\Element;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Child\CallbackModelTransformer;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementBuilderInterface;
use Bdf\Form\ElementInterface;
use Bdf\Form\Transformer\TransformerInterface;

/**
 * Add a HTTP transformer on the child element, by using method
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
 * $builder->string('foo')->transformer([$this, 'myTransformer']);
 *
 * // When using two methods (toHttp: 'transformFooToHttp', fromHttp: 'transformFooFromHttp')
 * $builder->string('foo')->transformer(function ($value, ElementInterface $input, bool $toPhp) {
 *     return $toPhp ? $this->transformFooFromHttp($value, $input) : $this->transformFooToHttp($value, $input);
 * });
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[CallbackTransformer(fromHttp: 'fooFromHttp', toHttp: 'fooToHttp')]
 *     private IntegerElement $foo;
 *
 *     // With unified transformer (same as above)
 *     #[CallbackTransformer('barTransformer')]
 *     private IntegerElement $bar;
 *
 *     public function fooFromHttp(string $value, IntegerElement $input): int
 *     {
 *         return hexdec($value);
 *     }
 *
 *     public function fooToHttp(int $value, IntegerElement $input): string
 *     {
 *         return dechex($value);
 *     }
 *
 *     public function barTransformer($value, IntegerElement $input, bool $toPhp)
 *     {
 *         return $toPhp ? hexdec($value) : dechex($value);
 *     }
 * }
 * </code>
 *
 * @see ElementBuilderInterface::transformer() The called method
 * @see Transformer For use a transformer class as transformer
 * @see CallbackModelTransformer For use transformer in same way, but for model transformer intead of http one
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class CallbackTransformer implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * Method name use to define the unified transformer method
         * If defined, the other parameters will be ignored
         */
        public ?string $callback = null,

        /**
         * Method name use to define the transformation process from http value to the input
         */
        public ?string $fromHttp = null,

        /**
         * Method name use to define the transformation process from input value to http format
         */
        public ?string $toHttp = null,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        if ($this->callback) {
            $builder->transformer([$form, $this->callback]);
            return;
        }

        $builder->transformer(new class($form, $this) implements TransformerInterface {
            public function __construct(
                private AnnotationForm $form,
                private CallbackTransformer $annotation,
            ) {}

            /**
             * {@inheritdoc}
             */
            public function transformToHttp($value, ElementInterface $input)
            {
                if (!$this->annotation->toHttp) {
                    return $value;
                }

                return $this->form->{$this->annotation->toHttp}($value, $input);
            }

            /**
             * {@inheritdoc}
             */
            public function transformFromHttp($value, ElementInterface $input)
            {
                if (!$this->annotation->fromHttp) {
                    return $value;
                }

                return $this->form->{$this->annotation->fromHttp}($value, $input);
            }
        });
    }
}

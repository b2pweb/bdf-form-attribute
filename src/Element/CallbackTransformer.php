<?php

namespace Bdf\Form\Attribute\Element;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\CallbackModelTransformer;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
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
 * class MyForm extends AttributeForm
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
final class CallbackTransformer implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * Method name use to define the unified transformer method
         * If defined, the other parameters will be ignored
         *
         * @var literal-string|null
         */
        public ?string $callback = null,
        /**
         * Method name use to define the transformation process from http value to the input
         *
         * @var literal-string|null
         */
        public ?string $fromHttp = null,
        /**
         * Method name use to define the transformation process from input value to http format
         *
         * @var literal-string|null
         */
        public ?string $toHttp = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        if ($this->callback) {
            $builder->transformer([$form, $this->callback]);
            return;
        }

        $builder->transformer(new class ($form, $this) implements TransformerInterface {
            public function __construct(
                private AttributeForm $form,
                private CallbackTransformer $attribute,
            ) {
            }

            /**
             * {@inheritdoc}
             */
            public function transformToHttp($value, ElementInterface $input)
            {
                if (!$this->attribute->toHttp) {
                    return $value;
                }

                return $this->form->{$this->attribute->toHttp}($value, $input);
            }

            /**
             * {@inheritdoc}
             */
            public function transformFromHttp($value, ElementInterface $input)
            {
                if (!$this->attribute->fromHttp) {
                    return $value;
                }

                return $this->form->{$this->attribute->fromHttp}($value, $input);
            }
        });
    }
}

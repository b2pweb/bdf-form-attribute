<?php

namespace Bdf\Form\Attribute\Child;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Attribute\Element\CallbackTransformer;
use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\ElementInterface;
use Bdf\Form\Transformer\TransformerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PsrPrinter;

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
 * class MyForm extends AttributeForm
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
final class CallbackModelTransformer implements ChildBuilderAttributeInterface
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
         * Method name use to define the transformation process from input value to the entity
         *
         * @var literal-string|null
         */
        public ?string $toEntity = null,
        /**
         * Method name use to define the transformation process from entity value to input
         *
         * @var literal-string|null
         */
        public ?string $toInput = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        if ($this->callback) {
            $builder->modelTransformer([$form, $this->callback]);
            return;
        }

        $builder->modelTransformer(new class ($form, $this) implements TransformerInterface {
            public function __construct(
                private AttributeForm $form,
                private CallbackModelTransformer $attribute,
            ) {
            }

            /**
             * {@inheritdoc}
             */
            public function transformToHttp($value, ElementInterface $input)
            {
                if (!$this->attribute->toInput) {
                    return $value;
                }

                return $this->form->{$this->attribute->toInput}($value, $input);
            }

            /**
             * {@inheritdoc}
             */
            public function transformFromHttp($value, ElementInterface $input)
            {
                if (!$this->attribute->toEntity) {
                    return $value;
                }

                return $this->form->{$this->attribute->toEntity}($value, $input);
            }
        });
    }


    /**
     * {@inheritdoc}
     */
    public function generateCodeForChildBuilder(string $name, AttributesProcessorGenerator $generator, AttributeForm $form): void
    {
        // @todo refactor with CallbackTransformer
        if ($this->callback) {
            $generator->line('$?->modelTransformer([$form, ?]);', [$name, $this->callback]);
            return;
        }

        $generator->use(TransformerInterface::class);
        $generator->use(ElementInterface::class);

        $transformer = new ClassType();
        $transformer->addImplement(TransformerInterface::class);

        $transformer->addProperty('form');

        $constructor = $transformer->addMethod('__construct');
        $constructor->addParameter('form');
        $constructor->setBody('$this->form = $form;');

        $toInput = Method::from([TransformerInterface::class, 'transformToHttp']);
        $toEntity = Method::from([TransformerInterface::class, 'transformFromHttp']);

        $toInput->setComment('{@inheritdoc}');
        $toEntity->setComment('{@inheritdoc}');

        $transformer->addMember($toInput);
        $transformer->addMember($toEntity);

        if ($this->toInput) {
            $toInput->setBody('return $this->form->?($value, $input);', [$this->toInput]);
        } else {
            $toInput->setBody('return $value;');
        }

        if ($this->toEntity) {
            $toEntity->setBody('return $this->form->?($value, $input);', [$this->toEntity]);
        } else {
            $toEntity->setBody('return $value;');
        }

        $generator->line(
            '$?->modelTransformer(new class ($form) ?);',
            [$name, new Literal((new PsrPrinter())->printClass($transformer, $generator->namespace()))]
        );
    }
}

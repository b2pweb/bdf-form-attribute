<?php

namespace Bdf\Form\Attribute\Child;

use Attribute;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Attribute\Processor\CodeGenerator\AttributesProcessorGenerator;
use Bdf\Form\Attribute\Processor\GenerateConfiguratorStrategy;
use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Define a custom configuration method for an element
 *
 * Use this attribute when other attributes cannot be used to configure the current element
 * Takes as argument the method name to use. This method must be declared into the form class with public visibility
 *
 * Usage:
 * <code>
 * class MyForm extends AttributeForm
 * {
 *     #[Configure('configureFoo')]
 *     private IntegerElement $foo;
 *
 *     public function configureFoo(ChildBuilderInterface $builder): void
 *     {
 *         $builder->min(5);
 *     }
 * }
 * </code>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Configure implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * The method name to use as configurator
         * The method should follow the prototype `public function (ChildBuilderInterface $builder): void`
         *
         * @var literal-string
         */
        public string $callback
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $form->{$this->callback}($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function generateCodeForChildBuilder(string $name, AttributesProcessorGenerator $generator, AttributeForm $form): void
    {
        $generator->line('$form->?($?);', [$this->callback, $name]);
    }
}

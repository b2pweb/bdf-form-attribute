<?php

namespace Bdf\Form\Attribute\Element;

use Attribute;
use Bdf\Form\AbstractElementBuilder;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Choice\ArrayChoice;
use Bdf\Form\Choice\Choiceable;
use Bdf\Form\Choice\ChoiceBuilderTrait;
use Bdf\Form\Choice\ChoiceInterface;
use Bdf\Form\Choice\LazzyChoice;
use Bdf\Form\ElementBuilderInterface;
use Bdf\Form\Leaf\StringElementBuilder;

/**
 * Define available values choice for the element
 *
 * The choices can be :
 * - a simple array of values (without labels)
 * - an associative array for provide a label (in key), and inner value (in value)
 * - a method name for resolving choices in lazy way
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->string('foo')->choices(['bar', 'baz']);
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AttributeForm
 * {
 *     #[Choices(['bar', 'rab'])]
 *     private StringElement $foo;
 *
 *     #[Choices(['My label' => 'v1', 'Other label' => 'v2'])]
 *     private StringElement $bar;
 *
 *     #[Choices('loadBazValues', 'Invalid value')]
 *     private StringElement $baz;
 *
 *     // For dynamic choices, or with complex logic
 *     public function loadBazValues(): array
 *     {
 *         $values = [];
 *
 *         foreach (BazEntity::all() as $baz) {
 *             $values[$baz->label()] = $baz->id();
 *         }
 *
 *         return $values;
 *     }
 * }
 * </code>
 *
 * @see ChoiceBuilderTrait::choices() The called method
 * @see Choiceable Supported element type
 * @see ArrayChoice Used when an array is given as parameter
 * @see LazzyChoice Used when a method name is given as parameter
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Choices implements ChildBuilderAttributeInterface
{
    public function __construct(
        /**
         * Choice provider
         *
         * Can be a method name for load choices. The method must be public and declared on the form class,
         * with the prototype `public function (): array`
         *
         * If the value is an array, the key will be used as label (displayed value), and the value as real value
         * The label is not required.
         *
         * @var literal-string|array
         */
        public string|array $choices,
        /**
         * The error message
         * If not provided, a default message will be used
         *
         * @var string|null
         */
        public ?string $message = null,
        /**
         * Extra constraint options
         *
         * @var array
         */
        public array $options = [],
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void
    {
        $options = $this->options;

        if ($this->message) {
            $options['message'] = $options['multipleMessage'] = $this->message;
        }

        // Q&D fix for psalm because it does not recognize trait as type
        /** @var StringElementBuilder $builder */
        $builder->choices(
            is_string($this->choices) ? new LazzyChoice([$form, $this->choices]) : $this->choices,
            $options
        );
    }
}

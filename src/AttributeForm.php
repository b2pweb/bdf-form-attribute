<?php

namespace Bdf\Form\Attribute;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Aggregate\FormInterface;
use Bdf\Form\Attribute\Button\ButtonBuilderAttributeInterface;
use Bdf\Form\Attribute\Form\FormBuilderAttributeInterface;
use Bdf\Form\Button\ButtonInterface;
use Bdf\Form\Custom\CustomForm;
use Bdf\Form\ElementInterface;
use Bdf\Form\Filter\FilterInterface;
use Bdf\Form\PropertyAccess\ExtractorInterface;
use Bdf\Form\PropertyAccess\HydratorInterface;
use Bdf\Form\Transformer\TransformerInterface;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\Validator\Constraint;

/**
 * Utility class for declare a form using PHP 8 attributes and declare elements using typed properties
 */
abstract class AttributeForm extends CustomForm
{
    /**
     * @var array<string, \ReflectionProperty>
     */
    private array $elementProperties = [];

    /**
     * @var array<string, \ReflectionProperty>
     */
    private array $buttonProperties = [];

    /**
     * {@inheritdoc}
     */
    protected function configure(FormBuilderInterface $builder): void
    {
        // @todo extraire dans une "attribute processor"

        for ($reflection = new ReflectionClass($this); $reflection->getName() !== AttributeForm::class; $reflection = $reflection->getParentClass()) {
            foreach ($reflection->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), FormBuilderAttributeInterface::class)) {
                    $attribute->newInstance()->applyOnFormBuilder($this, $builder);
                }
            }

            foreach ($reflection->getProperties() as $property) {
                if (!$property->hasType() || isset($this->elementProperties[$property->getName()]) || isset($this->buttonProperties[$property->getName()])) {
                    continue;
                }

                if (!$property->getType() instanceof ReflectionNamedType) {
                    continue;
                }

                $elementType = $property->getType()->getName();

                // @todo is_subclass_of($elementType, ButtonInterface::class) ?
                if ($elementType === ButtonInterface::class) {
                    $property->setAccessible(true);
                    $this->buttonProperties[$property->getName()] = $property;

                    $submitBuilder = $builder->submit($property->getName());

                    foreach ($property->getAttributes() as $attribute) {
                        match (true) {
                            is_subclass_of($attribute->getName(), ButtonBuilderAttributeInterface::class) => $attribute->newInstance()->applyOnButtonBuilder($this, $submitBuilder),
                        };
                    }

                    continue;
                }

                if (!is_subclass_of($elementType, ElementInterface::class)) {
                    continue;
                }

                $property->setAccessible(true);
                $this->elementProperties[$property->getName()] = $property;

                $elementBuilder = $builder->add($property->getName(), $elementType);

                foreach ($property->getAttributes() as $attribute) {
                    match (true) {
                        is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class) => $attribute->newInstance()->applyOnChildBuilder($this, $elementBuilder),

                        is_subclass_of($attribute->getName(), Constraint::class) => $elementBuilder->satisfy($attribute->newInstance()),
                        is_subclass_of($attribute->getName(), FilterInterface::class) => $elementBuilder->filter($attribute->newInstance()),
                        is_subclass_of($attribute->getName(), TransformerInterface::class) => $elementBuilder->transformer($attribute->newInstance()),
                        is_subclass_of($attribute->getName(), HydratorInterface::class) => $elementBuilder->hydrator($attribute->newInstance()),
                        is_subclass_of($attribute->getName(), ExtractorInterface::class) => $elementBuilder->extractor($attribute->newInstance()),
                    };
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postConfigure(FormInterface $form): void
    {
        foreach ($this->elementProperties as $name => $reflection) {
            $reflection->setValue($this, $form[$name]->element());
        }

        foreach ($this->buttonProperties as $name => $reflection) {
            $reflection->setValue($this, $this->root()->button($name));
        }

        unset($this->elementProperties);
    }
}

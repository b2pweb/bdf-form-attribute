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
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\Validator\Constraint;

/**
 * Utility class for declare a form using PHP 8 attributes and declare elements using typed properties
 */
abstract class AttributeForm extends CustomForm
{
    /**
     * @var array<non-empty-string, \ReflectionProperty>
     */
    private array $elementProperties = [];

    /**
     * @var array<non-empty-string, \ReflectionProperty>
     */
    private array $buttonProperties = [];

    /**
     * {@inheritdoc}
     */
    protected function configure(FormBuilderInterface $builder): void
    {
        // @todo extraire dans une "attribute processor"

        for ($reflection = new ReflectionClass($this); $reflection->getName() !== AttributeForm::class; $reflection = $reflection->getParentClass()) {
            foreach ($reflection->getAttributes(FormBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $attribute->newInstance()->applyOnFormBuilder($this, $builder);
            }

            foreach ($reflection->getProperties() as $property) {
                /** @var non-empty-string $name */
                $name = $property->getName();

                if (!$property->hasType() || isset($this->elementProperties[$name]) || isset($this->buttonProperties[$name])) {
                    continue;
                }

                if (!$property->getType() instanceof ReflectionNamedType) {
                    continue;
                }

                $elementType = $property->getType()->getName();

                // @todo is_subclass_of($elementType, ButtonInterface::class) ?
                if ($elementType === ButtonInterface::class) {
                    $property->setAccessible(true);
                    $this->buttonProperties[$name] = $property;

                    $submitBuilder = $builder->submit($name);

                    foreach ($property->getAttributes(ButtonBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                        $attribute->newInstance()->applyOnButtonBuilder($this, $submitBuilder);
                    }

                    continue;
                }

                if (!is_subclass_of($elementType, ElementInterface::class)) {
                    continue;
                }

                $property->setAccessible(true);
                $this->elementProperties[$name] = $property;

                $elementBuilder = $builder->add($name, $elementType);

                foreach ($property->getAttributes(ChildBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    $attribute->newInstance()->applyOnChildBuilder($this, $elementBuilder);
                }

                foreach ($property->getAttributes(Constraint::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                        $elementBuilder->satisfy($attribute->newInstance());
                    }
                }

                foreach ($property->getAttributes(FilterInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                        $elementBuilder->filter($attribute->newInstance());
                    }
                }

                foreach ($property->getAttributes(TransformerInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                        $elementBuilder->transformer($attribute->newInstance());
                    }
                }

                foreach ($property->getAttributes(HydratorInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                        $elementBuilder->hydrator($attribute->newInstance());
                    }
                }

                foreach ($property->getAttributes(ExtractorInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                        $elementBuilder->extractor($attribute->newInstance());
                    }
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

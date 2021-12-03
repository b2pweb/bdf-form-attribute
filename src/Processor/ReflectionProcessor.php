<?php

namespace Bdf\Form\Attribute\Processor;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Button\ButtonInterface;
use Bdf\Form\ElementInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Base processor using reflection for extract properties and attributes
 *
 * The configuration action will be delegated to the ReflectionStrategyInterface
 * This implementation is only responsive of iterate over class hierarchy and properties
 */
final class ReflectionProcessor implements AttributesProcessorInterface
{
    /**
     * Strategy to use on each field / class
     *
     * @var ReflectionStrategyInterface
     */
    private ReflectionStrategyInterface $strategy;

    /**
     * @param ReflectionStrategyInterface $strategy
     */
    public function __construct(ReflectionStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function configureBuilder(AttributeForm $form, FormBuilderInterface $builder): ?PostConfigureInterface
    {
        $elementProperties = [];
        $buttonProperties = [];

        foreach ($this->iterateClassHierarchy($form) as $formClass) {
            $this->strategy->onFormClass($formClass, $form, $builder);

            foreach ($formClass->getProperties() as $property) {
                /** @var non-empty-string $name */
                $name = $property->getName();

                if (
                    !$property->hasType()
                    || !$property->getType() instanceof ReflectionNamedType
                    || isset($elementProperties[$name])
                    || isset($buttonProperties[$name])
                ) {
                    continue;
                }

                $elementType = $property->getType()->getName();
                $property->setAccessible(true);

                if ($elementType === ButtonInterface::class) {
                    $buttonProperties[$name] = $property;
                    $this->strategy->onButtonProperty($property, $name, $form, $builder);
                } elseif (is_subclass_of($elementType, ElementInterface::class)) {
                    $elementProperties[$name] = $property;
                    $this->strategy->onElementProperty($property, $name, $elementType, $form, $builder);
                }
            }
        }

        return new PostConfigureReflectionSetProperties($elementProperties, $buttonProperties);
    }

    /**
     * Iterate over the class hierarchy of the annotation form
     * The iteration will start with the form class, and end with the AttributeForm class (excluded)
     *
     * @param AttributeForm $form
     *
     * @return iterable<ReflectionClass<AttributeForm>>
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    private function iterateClassHierarchy(AttributeForm $form): iterable
    {
        for ($reflection = new ReflectionClass($form); $reflection->getName() !== AttributeForm::class; $reflection = $reflection->getParentClass()) {
            yield $reflection;
        }
    }
}

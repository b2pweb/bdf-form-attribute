<?php

namespace Bdf\Form\Attribute\Processor;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Button\ButtonBuilderAttributeInterface;
use Bdf\Form\Attribute\ChildBuilderAttributeInterface;
use Bdf\Form\Attribute\Form\FormBuilderAttributeInterface;
use Bdf\Form\Attribute\Processor\Element\ConstraintAttributeProcessor;
use Bdf\Form\Attribute\Processor\Element\ElementAttributeProcessorInterface;
use Bdf\Form\Attribute\Processor\Element\ExtractorAttributeProcessor;
use Bdf\Form\Attribute\Processor\Element\FilterAttributeProcessor;
use Bdf\Form\Attribute\Processor\Element\HydratorAttributeProcessor;
use Bdf\Form\Attribute\Processor\Element\TransformerAttributeProcessor;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

/**
 * Strategy for directly configure the form builder using attributes
 */
final class ConfigureFormBuilderStrategy implements ReflectionStrategyInterface
{
    /**
     * @var list<ElementAttributeProcessorInterface>
     */
    private array $elementProcessors = [];

    public function __construct()
    {
        $this->registerElementAttributeProcessor(new ConstraintAttributeProcessor());
        $this->registerElementAttributeProcessor(new FilterAttributeProcessor());
        $this->registerElementAttributeProcessor(new TransformerAttributeProcessor());
        $this->registerElementAttributeProcessor(new HydratorAttributeProcessor());
        $this->registerElementAttributeProcessor(new ExtractorAttributeProcessor());
    }

    /**
     * Register a new processor for element attributes
     *
     * @param ElementAttributeProcessorInterface<T> $processor
     *
     * @return void
     *
     * @template T as object
     */
    public function registerElementAttributeProcessor(ElementAttributeProcessorInterface $processor): void
    {
        $this->elementProcessors[] = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function onFormClass(ReflectionClass $formClass, AttributeForm $form, FormBuilderInterface $builder): void
    {
        foreach ($formClass->getAttributes(FormBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $attribute->newInstance()->applyOnFormBuilder($form, $builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onButtonProperty(ReflectionProperty $property, string $name, AttributeForm $form, FormBuilderInterface $builder): void
    {
        $submitBuilder = $builder->submit($name);

        foreach ($property->getAttributes(ButtonBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $attribute->newInstance()->applyOnButtonBuilder($form, $submitBuilder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onElementProperty(ReflectionProperty $property, string $name, string $elementType, AttributeForm $form, FormBuilderInterface $builder): void
    {
        $elementBuilder = $builder->add($name, $elementType);

        foreach ($property->getAttributes(ChildBuilderAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $attribute->newInstance()->applyOnChildBuilder($form, $elementBuilder);
        }

        foreach ($this->elementProcessors as $configurator) {
            foreach ($property->getAttributes($configurator->type(), ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                if (!is_subclass_of($attribute->getName(), ChildBuilderAttributeInterface::class)) {
                    $configurator->process($elementBuilder, $attribute->newInstance());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onPostConfigure(array $elementProperties, array $buttonProperties, AttributeForm $form): ?PostConfigureInterface
    {
        return new PostConfigureReflectionSetProperties($elementProperties, $buttonProperties);
    }
}

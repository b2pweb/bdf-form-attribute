<?php

namespace Bdf\Form\Attribute;

use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Base attribute type for configure ChildBuilder
 * The attribute should be declared on the element property
 *
 * @see ChildBuilderInterface
 *
 * @template E as \Bdf\Form\ElementBuilderInterface
 */
interface ChildBuilderAttributeInterface
{
    /**
     * Configure the child builder
     *
     * @param AttributeForm $form
     * @param ChildBuilderInterface<E> $builder
     */
    public function applyOnChildBuilder(AttributeForm $form, ChildBuilderInterface $builder): void;
}

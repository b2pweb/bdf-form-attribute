<?php

namespace Bdf\Form\Annotation;

use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Base attribute type for configure ChildBuilder
 * The attribute should be declared on the element property
 *
 * @see ChildBuilderInterface
 *
 * @template E as \Bdf\Form\ElementBuilderInterface
 */
interface ChildBuilderAnnotationInterface
{
    /**
     * Configure the child builder
     *
     * @param AnnotationForm $form
     * @param ChildBuilderInterface<E> $builder
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void;
}

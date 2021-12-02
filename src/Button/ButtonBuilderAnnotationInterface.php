<?php

namespace Bdf\Form\Annotation\Button;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Button\ButtonBuilderInterface;

/**
 * Base type for attributes used for configure buttons
 * The attribute must be defined on the button property
 *
 * @see ButtonBuilderInterface The configured builder
 */
interface ButtonBuilderAnnotationInterface
{
    /**
     * Configure the button builder
     *
     * @param AnnotationForm $form
     * @param ButtonBuilderInterface $builder
     */
    public function applyOnButtonBuilder(AnnotationForm $form, ButtonBuilderInterface $builder): void;
}

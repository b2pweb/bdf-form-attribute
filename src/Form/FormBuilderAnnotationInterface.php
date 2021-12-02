<?php

namespace Bdf\Form\Annotation\Form;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Annotation\AnnotationForm;

/**
 * Annotation type for configure a form builder
 * Those annotations should be declared on the class declaration
 *
 * @see FormBuilderInterface The configured builder
 */
interface FormBuilderAnnotationInterface
{
    /**
     * Configure the given builder
     *
     * @param AnnotationForm $form The form to configure
     * @param FormBuilderInterface $builder The form builder
     */
    public function applyOnFormBuilder(AnnotationForm $form, FormBuilderInterface $builder): void;
}

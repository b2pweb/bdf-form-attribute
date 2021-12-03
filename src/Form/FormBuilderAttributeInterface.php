<?php

namespace Bdf\Form\Attribute\Form;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Attribute\AttributeForm;

/**
 * Attribute type for configure a form builder
 * Those attributes should be declared on the class declaration
 *
 * @see FormBuilderInterface The configured builder
 */
interface FormBuilderAttributeInterface
{
    /**
     * Configure the given builder
     *
     * @param AttributeForm $form The form to configure
     * @param FormBuilderInterface $builder The form builder
     */
    public function applyOnFormBuilder(AttributeForm $form, FormBuilderInterface $builder): void;
}

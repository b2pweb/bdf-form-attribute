<?php

namespace Bdf\Form\Attribute\Button;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Button\ButtonBuilderInterface;

/**
 * Base type for attributes used for configure buttons
 * The attribute must be defined on the button property
 *
 * @see ButtonBuilderInterface The configured builder
 */
interface ButtonBuilderAttributeInterface
{
    /**
     * Configure the button builder
     *
     * @param AttributeForm $form
     * @param ButtonBuilderInterface $builder
     */
    public function applyOnButtonBuilder(AttributeForm $form, ButtonBuilderInterface $builder): void;
}

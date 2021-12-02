<?php

namespace Bdf\Form\Annotation\Button;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Button\ButtonBuilderInterface;
use Bdf\Form\Button\ButtonInterface;
use Bdf\Form\RootElementInterface;

/**
 * Attribute for define the validation group to use when the related button is clicked
 *
 * Note: this attribute is not repeatable
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->button('btn')->groups(['Foo', 'Bar']);
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Groups('Foo', 'Bar')]
 *     private ButtonInterface $btn;
 * }
 * </code>
 *
 * @see ButtonBuilderInterface::groups() The called method
 * @see ButtonInterface::constraintGroups() Modify this value
 * @see RootElementInterface::constraintGroups()
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Groups implements ButtonBuilderAnnotationInterface
{
    /**
     * @var list<string>
     */
    public array $groups;

    /**
     * @param string ...$groups List of validation groups
     */
    public function __construct(string ...$groups)
    {
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnButtonBuilder(AnnotationForm $form, ButtonBuilderInterface $builder): void
    {
        $builder->groups($this->groups);
    }
}

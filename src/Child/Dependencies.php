<?php

namespace Bdf\Form\Annotation\Child;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;

/**
 * Add dependencies on other sibling elements
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->float('foo')->depends('bar', 'baz');
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[Dependencies('bar', 'rab')]
 *     private FloatElement $foo;
 *     private IntegerElement $bar;
 *     private StringElement $rab;
 * }
 * </code>
 *
 * @see ChildBuilderInterface::depends() The called method
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Dependencies implements ChildBuilderAnnotationInterface
{
    /**
     * @var list<string>
     */
    public array $dependencies;

    /**
     * @param string ...$dependencies List of inputs names
     */
    public function __construct(string ...$dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->depends(...$this->dependencies);
    }
}

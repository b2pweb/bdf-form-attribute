<?php

namespace Bdf\Form\Annotation\Child;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\ChildBuilderAnnotationInterface;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\PropertyAccess\Getter;
use Bdf\Form\PropertyAccess\Setter;

/**
 * Define simple hydrator and extractor on the element, using the name property or accessor name
 *
 * This attribute is equivalent to call :
 * <code>
 * $builder->float('foo')->getter('bar')->setter('bar');
 * $builder->float('foo')->hydrator(new Setter('bar'))->extract(new Getter('bar'));
 * </code>
 *
 * Usage:
 * <code>
 * class MyForm extends AnnotationForm
 * {
 *     #[GetSet('bar')]
 *     private FloatElement $foo;
 * }
 * </code>
 *
 * @see ChildBuilder::getter()
 * @see ChildBuilder::setter()
 * @see ChildBuilderInterface::hydrator()
 * @see ChildBuilderInterface::extractor()
 *
 * @see Getter For define only the extractor
 * @see Setter For define only the hydrator
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class GetSet implements ChildBuilderAnnotationInterface
{
    public function __construct(
        /**
         * The property name to use
         * This can be a public property, or public accessor method
         * (optionally starting with get for the getter, and starting with set for the setter)
         *
         * If not provided, the input name will be used as property name
         */
        public ?string $propertyName = null,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function applyOnChildBuilder(AnnotationForm $form, ChildBuilderInterface $builder): void
    {
        $builder->hydrator(new Setter($this->propertyName))->extractor(new Getter($this->propertyName));
    }
}

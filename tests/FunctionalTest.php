<?php

namespace Tests\Form\Annotation;

use Attribute;
use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Button\ButtonInterface;
use Bdf\Form\Button\SubmitButton;
use Bdf\Form\Child\ChildInterface;
use Bdf\Form\ElementInterface;
use Bdf\Form\Filter\FilterVar;
use Bdf\Form\Leaf\IntegerElement;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Getter;
use Bdf\Form\PropertyAccess\Setter;
use Bdf\Form\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FunctionalTest extends TestCase
{
    /**
     *
     */
    public function test_simple()
    {
        $form = new class extends AnnotationForm {
            #[NotBlank, Length(min: 3), Getter, Setter]
            public StringElement $firstName;

            #[NotBlank, Length(min: 3), Getter, Setter]
            public StringElement $lastName;

            #[Getter, Setter, GreaterThan(0)]
            public IntegerElement $age;
        };

        $this->assertInstanceOf(ChildInterface::class, $form['firstName']);
        $this->assertInstanceOf(ChildInterface::class, $form['lastName']);
        $this->assertInstanceOf(ChildInterface::class, $form['age']);

        $this->assertInstanceOf(StringElement::class, $form['firstName']->element());
        $this->assertInstanceOf(StringElement::class, $form['lastName']->element());
        $this->assertInstanceOf(IntegerElement::class, $form['age']->element());

        $this->assertSame($form['firstName']->element(), $form->firstName);
        $this->assertSame($form['lastName']->element(), $form->lastName);
        $this->assertSame($form['age']->element(), $form->age);

        $form->submit(['firstName' => 'John', 'lastName' => 'Doe', 'age' => '35']);
        $this->assertTrue($form->valid());

        $this->assertSame(['firstName' => 'John', 'lastName' => 'Doe', 'age' => 35], $form->value());

        $form->submit(['firstName' => 'Foo', 'lastName' => 'B', 'age' => '-5']);

        $this->assertFalse($form->valid());
        $this->assertEquals([
            'lastName' => 'This value is too short. It should have 3 characters or more.',
            'age' => 'This value should be greater than 0.',
        ], $form->error()->toArray());
    }

    /**
     *
     */
    public function test_setter_with_name()
    {
        $form = new class extends AnnotationForm {
            #[Setter('bar')]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'azerty']);
        $this->assertSame(['bar' => 'azerty'], $form->value());
    }

    /**
     *
     */
    public function test_getter_with_name()
    {
        $form = new class extends AnnotationForm {
            #[Getter('bar')]
            public StringElement $foo;
        };

        $form->import(['bar' => 'aqw']);
        $this->assertSame('aqw', $form->foo->value());
    }

    /**
     *
     */
    public function test_inheritance()
    {
        $form = new ChildForm();

        $this->assertInstanceOf(StringElement::class, $form['foo']->element());
        $this->assertInstanceOf(IntegerElement::class, $form['bar']->element());

        $form->submit([]);

        $this->assertFalse($form->valid());
        $this->assertEquals(['foo' => 'This value should not be blank.', 'bar' => 'This value should not be blank.'], $form->error()->toArray());

        $form->submit(['foo' => 'azerty', 'bar' => '25']);
        $this->assertTrue($form->valid());

        $this->assertSame(['bar' => 25, 'foo' => 'azerty'], $form->value());
    }

    /**
     *
     */
    public function test_buttons()
    {
        $form = new class extends AnnotationForm {
            public ButtonInterface $foo;
            public ButtonInterface $bar;
        };

        $form->submit([]);

        $this->assertInstanceOf(SubmitButton::class, $form->foo);
        $this->assertInstanceOf(SubmitButton::class, $form->bar);

        $this->assertFalse($form->foo->clicked());
        $this->assertFalse($form->bar->clicked());

        $form->submit(['foo' => 'ok']);

        $this->assertTrue($form->foo->clicked());
        $this->assertFalse($form->bar->clicked());

        $form->submit(['bar' => 'ok']);

        $this->assertFalse($form->foo->clicked());
        $this->assertTrue($form->bar->clicked());
    }

    /**
     *
     */
    public function test_filter()
    {
        $form = new class extends AnnotationForm {
            #[FilterVar(FILTER_SANITIZE_FULL_SPECIAL_CHARS), Setter]
            public StringElement $foo;
        };

        $form->submit(['foo' => '<hello>&world']);
        $this->assertSame(['foo' => '&lt;hello&gt;&amp;world'], $form->value());
    }

    /**
     *
     */
    public function test_transformer()
    {
        $form = new class extends AnnotationForm {
            #[MyTransformer, Setter]
            public StringElement $foo;
        };

        $form->submit(['foo' => 'aaa']);
        $this->assertSame(['foo' => 'YWFh'], $form->value());
    }
}

class BaseForm extends AnnotationForm
{
    #[NotBlank, Getter, Setter]
    private StringElement $foo;
}

class ChildForm extends BaseForm
{
    #[NotBlank, Getter, Setter, GreaterThan(5)]
    private IntegerElement $bar;
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class MyTransformer implements TransformerInterface
{
    public function transformToHttp($value, ElementInterface $input)
    {
        return base64_decode($value);
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return base64_encode($value);
    }
}

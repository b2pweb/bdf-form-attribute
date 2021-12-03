<?php

namespace Tests\Form\Attribute\Form;

use Bdf\Form\Aggregate\FormBuilderInterface;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Form\Generates;
use Bdf\Form\Custom\CustomForm;
use Bdf\Form\Leaf\FloatElement;
use Bdf\Form\Leaf\IntegerElement;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Getter;
use Bdf\Form\PropertyAccess\Setter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Positive;

class GeneratesTest extends TestCase
{
    public function test()
    {
        $form = new #[Generates(Person::class)] class extends AttributeForm {
            #[Setter]
            public StringElement $firstName;
            #[Setter]
            public StringElement $lastName;
            #[Setter]
            public IntegerElement $age;
        };

        $form->submit(['firstName' => 'John', 'lastName' => 'Doe', 'age' => '35']);

        $expected = new Person();
        $expected->firstName = 'John';
        $expected->lastName = 'Doe';
        $expected->age = 35;
        $this->assertEquals($expected, $form->value());
    }
}

class Person
{
    public string $firstName;
    public string $lastName;
    public int $age;
}

class MyForm extends CustomForm
{
    /**
     * {@inheritdoc}
     */
    protected function configure(FormBuilderInterface $builder): void
    {
        $builder->add('custom', MyCustomElement::class)->satisfy(new NotEqualTo('15'));
    }
}

// @todo is repetable
class OrderForm extends AttributeForm
{
    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public FloatElement $weight;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public FloatElement $length;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public FloatElement $width;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public FloatElement $height;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public FloatElement $volume;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public IntegerElement $palletsCount;

    #[Positive(message: 'Valeur incorrecte'), Getter, Setter]
    public IntegerElement $parcelsCount;
}

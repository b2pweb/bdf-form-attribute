<?php

namespace Tests\Form\Attribute\Child;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\CallbackModelTransformer;
use Bdf\Form\Attribute\Form\Generates;
use Bdf\Form\Leaf\IntegerElement;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Getter;
use Bdf\Form\PropertyAccess\Setter;
use PHPUnit\Framework\TestCase;

class CallbackModelTransformerTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new #[Generates(Struct::class)] class extends AttributeForm {
            #[CallbackModelTransformer('aTransformer'), Getter, Setter]
            public StringElement $a;
            #[CallbackModelTransformer(toEntity: 'bToEntity', toInput: 'bToInput'), Getter, Setter]
            public IntegerElement $b;

            public function aTransformer($value, StringElement $input, bool $toPhp)
            {
                return $toPhp ? base64_encode($value) : base64_decode($value);
            }

            public function bToEntity($value, IntegerElement $input)
            {
                return dechex($value);
            }

            public function bToInput($value, IntegerElement $input)
            {
                return hexdec($value);
            }
        };

        $form->submit(['a' => 'foo', 'b' => '15']);
        $this->assertEquals(new Struct(a: 'Zm9v', b: 'f'), $form->value());

        $form->import(new Struct(a: 'SGVsbG8gV29ybGQgIQ==', b: 'a'));
        $this->assertEquals('Hello World !', $form->a->value());
        $this->assertEquals(10, $form->b->value());
    }

    /**
     *
     */
    public function test_with_only_one_transformation_method()
    {
        $form = new class extends AttributeForm {
            #[CallbackModelTransformer(toEntity: 't'), Getter, Setter]
            public IntegerElement $foo;
            #[CallbackModelTransformer(toInput: 't'), Getter, Setter]
            public IntegerElement $bar;

            public function t($value, $input)
            {
                return $value + 1;
            }
        };

        $form->submit(['foo' => '5', 'bar' => '5']);
        $this->assertSame([
            'foo' => 6,
            'bar' => 5
        ], $form->value());

        $form->import(['foo' => 5, 'bar' => 5]);
        $this->assertSame(5, $form->foo->value());
        $this->assertSame(6, $form->bar->value());
    }
}

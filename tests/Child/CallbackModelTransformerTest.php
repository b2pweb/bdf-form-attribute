<?php

namespace Tests\Form\Annotation\Child;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Child\CallbackModelTransformer;
use Bdf\Form\Annotation\Form\Generates;
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
        $form = new #[Generates(Struct::class)] class extends AnnotationForm {
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
}

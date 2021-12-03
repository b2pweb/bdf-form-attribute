<?php

namespace Tests\Form\Attribute\Child;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Child\GetSet;
use Bdf\Form\Attribute\Child\ModelTransformer;
use Bdf\Form\Attribute\Form\Generates;
use Bdf\Form\ElementInterface;
use Bdf\Form\Leaf\IntegerElement;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Getter;
use Bdf\Form\PropertyAccess\Setter;
use Bdf\Form\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

// @todo Raw attribute for number elements
class ModelTransformerTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new #[Generates(Struct::class)] class extends AttributeForm {
            #[ModelTransformer(ATransformer::class), Getter, Setter]
            public StringElement $a;
            #[ModelTransformer(BTransformer::class), Getter, Setter]
            public IntegerElement $b;

            #[ModelTransformer(TransformerWithArguments::class, ['foo_']), GetSet]
            public StringElement $c;
        };

        $form->submit(['a' => 'foo', 'b' => '15']);
        $this->assertEquals(new Struct(a: 'Zm9v', b: 'f'), $form->value());

        $form->import(new Struct(a: 'SGVsbG8gV29ybGQgIQ==', b: 'a'));
        $this->assertEquals('Hello World !', $form->a->value());
        $this->assertEquals(10, $form->b->value());

        $form->submit(['c' => 'bar']);
        $this->assertEquals('foo_bar', $form->value()->c);

        $form->import(new Struct(c: 'foo_abc'));
        $this->assertEquals('abc', $form->c->value());
    }
}

class Struct
{
    public function __construct(
        public ?string $a = null,
        public ?string $b = null,
        public ?string $c = 'foo_',
    ) {}
}

class ATransformer implements TransformerInterface
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

class BTransformer implements TransformerInterface
{
    public function transformToHttp($value, ElementInterface $input)
    {
        return hexdec($value);
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return dechex($value);
    }
}

class TransformerWithArguments implements TransformerInterface
{
    public function __construct(public string $prefix) {}

    public function transformToHttp($value, ElementInterface $input)
    {
        return substr($value, strlen($this->prefix));
    }

    public function transformFromHttp($value, ElementInterface $input)
    {
        return $this->prefix . $value;
    }

}

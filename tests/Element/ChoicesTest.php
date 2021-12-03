<?php

namespace Tests\Form\Attribute\Element;

use Bdf\Form\Aggregate\ArrayElement;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Element\Choices;
use Bdf\Form\Choice\ArrayChoice;
use Bdf\Form\Leaf\StringElement;
use PHPUnit\Framework\TestCase;

class ChoicesTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Choices(choices: ['foo', 'bar'], message: 'my error')]
            public StringElement $foo;

            #[Choices(choices: ['foo', 'bar', 'baz'], message: 'my error', options: ['min' => 2])]
            public ArrayElement $bar;

            #[Choices('generateChoices')]
            public StringElement $baz;

            public function generateChoices()
            {
                return ['aaa', 'bbb', 'ccc'];
            }
        };

        $form->submit(['foo' => 'a', 'bar' => ['b'], 'baz' => 'c']);

        $this->assertEquals(new ArrayChoice(['foo', 'bar']), $form->foo->choices());
        $this->assertEquals(new ArrayChoice(['foo', 'bar', 'baz']), $form->bar->choices());
        $this->assertEquals(['aaa', 'bbb', 'ccc'], $form->baz->choices()->values());
        $this->assertEquals(['foo' => 'my error', 'bar' => 'my error', 'baz' => 'The value you selected is not a valid choice.'], $form->error()->toArray());

        $form->submit(['foo' => 'bar', 'bar' => ['bar', 'foo'], 'baz' => 'ccc']);
        $this->assertTrue($form->valid());

        $form->submit(['foo' => 'bar', 'bar' => ['bar'], 'baz' => 'ccc']);
        $this->assertEquals(['bar' => 'You must select at least 2 choices.'], $form->error()->toArray());
    }
}

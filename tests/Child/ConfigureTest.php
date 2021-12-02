<?php

namespace Tests\Form\Annotation\Child;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Child\Configure;
use Bdf\Form\Child\ChildBuilderInterface;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\Leaf\StringElementBuilder;
use PHPUnit\Framework\TestCase;

class ConfigureTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[Configure('configureFoo')]
            public StringElement $foo;

            /**
             * @param ChildBuilderInterface|StringElementBuilder $builder
             */
            public function configureFoo(ChildBuilderInterface $builder): void
            {
                $builder->length(['min' => 3]);
            }
        };

        $form->submit(['foo' => 'a']);
        $this->assertFalse($form->valid());
        $this->assertEquals(['foo' => 'This value is too short. It should have 3 characters or more.'], $form->error()->toArray());

        $form->submit(['foo' => 'abc']);
        $this->assertTrue($form->valid());
    }
}

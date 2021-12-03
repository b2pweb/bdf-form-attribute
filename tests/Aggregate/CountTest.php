<?php

namespace Tests\Form\Annotation\Aggregate;

use Bdf\Form\Aggregate\ArrayElement;
use Bdf\Form\Annotation\Aggregate\Count;
use Bdf\Form\Annotation\AnnotationForm;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    /**
     * 
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[Count(min: 3, max: 5)]
            public ArrayElement $values;
        };

        $form->submit([]);
        $this->assertFalse($form->valid());
        $this->assertEquals(['values' => 'This collection should contain 3 elements or more.'], $form->error()->toArray());

        $form->submit(['values' => ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'fff']]);
        $this->assertFalse($form->valid());
        $this->assertEquals(['values' => 'This collection should contain 5 elements or less.'], $form->error()->toArray());

        $form->submit(['values' => ['aaa', 'bbb', 'ccc']]);
        $this->assertTrue($form->valid());
    }
}
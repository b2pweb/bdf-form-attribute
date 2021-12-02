<?php

namespace Tests\Form\Annotation\Aggregate;

use Bdf\Form\Aggregate\ArrayElement;
use Bdf\Form\Annotation\Aggregate\ArrayConstraint;
use Bdf\Form\Annotation\AnnotationForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Unique;

class ArrayConstraintTest extends TestCase
{
    /**
     *
     */
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[ArrayConstraint(Unique::class, ['message' => 'Not unique'])]
            public ArrayElement $values;
        };

        $form->submit(['values' => ['aaa', 'aaa']]);
        $this->assertFalse($form->valid());
        $this->assertEquals(['values' => 'Not unique'], $form->error()->toArray());

        $form->submit(['values' => ['aaa', 'bbb']]);
        $this->assertTrue($form->valid());
    }
}

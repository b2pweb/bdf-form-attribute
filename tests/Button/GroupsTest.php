<?php

namespace Tests\Form\Annotation\Button;

use Bdf\Form\Annotation\AnnotationForm;
use Bdf\Form\Annotation\Button\Groups;
use Bdf\Form\Button\ButtonInterface;
use PHPUnit\Framework\TestCase;

class GroupsTest extends TestCase
{
    public function test()
    {
        $form = new class extends AnnotationForm {
            #[Groups('foo', 'bar')]
            public ButtonInterface $btn;
        };

        $form->submit([]);
        $this->assertEquals(['Default'], $form->root()->constraintGroups());

        $form->submit(['btn' => 'ok']);
        $this->assertEquals(['foo', 'bar'], $form->root()->constraintGroups());
    }
}

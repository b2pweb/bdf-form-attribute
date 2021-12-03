<?php

namespace Tests\Form\Attribute\Button;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Attribute\Button\Groups;
use Bdf\Form\Button\ButtonInterface;
use PHPUnit\Framework\TestCase;

class GroupsTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Groups('foo', 'bar')]
            public ButtonInterface $btn;
        };

        $form->submit([]);
        $this->assertEquals(['Default'], $form->root()->constraintGroups());

        $form->submit(['btn' => 'ok']);
        $this->assertEquals(['foo', 'bar'], $form->root()->constraintGroups());
    }
}

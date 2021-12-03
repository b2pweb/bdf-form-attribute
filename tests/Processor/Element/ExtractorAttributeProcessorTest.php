<?php

namespace Tests\Form\Attribute\Processor\Element;

use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Leaf\StringElement;
use Bdf\Form\PropertyAccess\Getter;
use PHPUnit\Framework\TestCase;

class ExtractorAttributeProcessorTest extends TestCase
{
    public function test()
    {
        $form = new class extends AttributeForm {
            #[Getter('bar')]
            public StringElement $foo;
        };

        $form->import(['bar' => 'azerty']);
        $this->assertSame('azerty', $form->foo->value());
    }
}

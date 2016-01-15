<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\MarkType;
use Symfony\Component\Form\Test\TypeTestCase;

class MarkTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'mark' => '5',
        );
        $form = $this->factory->create(MarkType::class);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(1, 1); // this isn't a joke
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
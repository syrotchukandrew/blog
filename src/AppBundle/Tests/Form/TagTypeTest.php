<?php
namespace Tests\AppBundle\Form\Type;
use AppBundle\Entity\Tag;
use AppBundle\Form\TagType;
use Symfony\Component\Form\Test\TypeTestCase;
class TagTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'name' => 'test',
        );
        $form = $this->factory->create(TagType::class);
        $object = new Tag();
        $object->setTitle('test');
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
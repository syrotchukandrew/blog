<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Test\TypeTestCase;

class PostTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'test',
            'content' => 'Andrew Syrotchuk',
        );
        $form = $this->factory->create(PostType::class);
        $object = new Post();
        $object->setTitle('test');
        $object->setContent('Andrew Syrotchuk');
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
<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'content' => 'Andrew Syrotchuk',
        );
        $form = $this->factory->create(CommentType::class);
        $object = new Comment();
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
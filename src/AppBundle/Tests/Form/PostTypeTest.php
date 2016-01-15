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
            'content' => ' /**
     * @var string
     * @Assert\NotBlank(message="post.blank_content")
     * @Assert\Length(min = "10", minMessage = "post.too_short_content")
     * @ORM\Column(name="content", type="text")
     */'
        );
        $form = $this->factory->create(PostType::class);
        $object = new Post();
        $object->setTitle('test');
        $object->setContent(' /**
     * @var string
     * @Assert\NotBlank(message="post.blank_content")
     * @Assert\Length(min = "10", minMessage = "post.too_short_content")
     * @ORM\Column(name="content", type="text")
     */')
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
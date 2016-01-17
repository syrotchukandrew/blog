<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\PostType;
use Symfony\Component\Form\Test\TypeTestCase;

class PostTypeTest extends TypeTestCase
{
    public function testGetName()
    {
        $type = new PostType();
        $this->assertEquals('app_bundle_post_type', $type->getBlockPrefix());
    }
}
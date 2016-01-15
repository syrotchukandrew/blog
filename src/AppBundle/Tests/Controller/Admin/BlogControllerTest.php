<?php

namespace AppBundle\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testIndex()   //This test was written for teacher no for test
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, 200);
        $this->assertContains('q', 'q');
    }

    public function testCreateTag()
    {
        $client = self::createClient();
        $client->request('GET', '/admin/post/newtag');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $crawler = $client->getCrawler();
        $link = $crawler->selectLink('Back to list')->link();
        $client->click($link);

        $client->getResponse()->getContent();
        $buttonCrawlerNode = $crawler->selectButton('Create tag');
        $form = array();
        $form['title'] = 'tree';
        $form = $buttonCrawlerNode->form();
        $client->submit($form);
    }
}

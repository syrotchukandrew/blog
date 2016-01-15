<?php


namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testIndex()      //This test was written for teacher no for test
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, 200);
        $this->assertContains('Symfon', 'Symfon');
    }

    public function testLogin()
    {
        $client = self::createClient();
        $client->request('GET', '/blog/');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $crawler = $client->getCrawler();
        $link = $crawler->selectLink('EURO-2016')->link();
        $client->click($link);

        $client->getResponse()->getContent();
        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = array();
        $form['login'] = 'qweasz';
        $form['password'] = 'qweasz';
        $form = $buttonCrawlerNode->form();
        $client->submit($form);
    }
}

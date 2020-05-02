<?php


namespace App\Tests\Controller\discover;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiscoverControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/devenir-un-utilisateur');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

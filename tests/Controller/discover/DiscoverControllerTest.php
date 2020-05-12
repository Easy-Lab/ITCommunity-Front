<?php


namespace App\Tests\Controller\discover;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class DiscoverControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = HttpClient::create();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/devenir-un-utilisateur');

        $this->assertEquals(200, $crawler->getStatusCode());
    }
}

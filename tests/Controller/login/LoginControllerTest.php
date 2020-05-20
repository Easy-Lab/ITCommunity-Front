<?php


namespace App\Tests\Controller\login;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class LoginControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = HttpClient::create();

        $crawler = $client->request('GET', 'https://itcommunity.fr/se-connecter');

        $this->assertEquals(200, $crawler->getStatusCode());
    }
}

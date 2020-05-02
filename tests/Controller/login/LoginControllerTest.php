<?php


namespace App\Tests\Controller\login;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/se-connecter');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

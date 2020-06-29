<?php


namespace App\Tests\Controller\contact;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://itcommunity.fr/user/profile/MatP');

        $this->assertEquals(200, $response->getStatusCode());
    }
}

<?php


namespace App\Tests\Controller\login;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class PasswordControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = HttpClient::create();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/user/request-password');

        $this->assertEquals(200, $crawler->getStatusCode());
    }

    public function testForgotPassword()
    {
        $client = HttpClient::create();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/user/forgot_password/123456789');

        $this->assertEquals(200, $crawler->getStatusCode());
    }
}

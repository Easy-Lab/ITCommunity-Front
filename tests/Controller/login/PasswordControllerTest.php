<?php


namespace App\Tests\Controller\login;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/user/request-password');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testForgotPassword()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/user/forgot_password/123456789');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

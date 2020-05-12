<?php


namespace App\Tests\Controller\contact;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/user/profile/test');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateContact()
    {
        $dataContact = [
            'firstname' => 'test',
            'lastname' => 'test',
            'email' => 'test@gmail.com'
        ];
        $client = HttpClient::create();
        $responseContact = $client->request('POST','https://preprod.api.itcommunity.fr/contacts', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataContact)
        ]);

        $this->assertEquals(409, $responseContact->getStatusCode());
    }

    public function testConseil()
    {
        $client = HttpClient::create();

        $dataMessage = [
            'email' => 'test@gmail.com',
            'username' => 'test',
            'type' => 'conseil',
            'question' => 'test'
        ];
        $responseMessage = $client->request('POST','https://preprod.api.itcommunity.fr/messages', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataMessage)
        ]);

        $this->assertEquals(201, $responseMessage->getStatusCode());
    }

    public function testDemo()
    {
        $client = HttpClient::create();

        $dataMessage = [
            'email' => 'test@gmail.com',
            'username' => 'test',
            'type' => 'demo',
            'question' => 'test'
        ];
        $responseMessage = $client->request('POST','https://preprod.api.itcommunity.fr/messages', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataMessage)
        ]);

        $this->assertEquals(201, $responseMessage->getStatusCode());
    }
}

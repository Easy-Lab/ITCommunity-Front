<?php


namespace App\Tests\Controller\home;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class HomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteMap()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/sitemap');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testSiteMapXml()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/sitemap.xml');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testContactUs()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/contact-us');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testContactUsForm()
    {
        $dataContact =
            [
                'firstname' => 'test',
                'lastname' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '0600000000',
                'subject' => 'test',
                'body' => 'test'
            ];
        $client = HttpClient::create();
        $responseContact = $client->request('POST',  'https://preprod.api.itcommunity.fr/contactforms', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataContact)
        ]);

        $this->assertEquals(201, $responseContact->getStatusCode());

    }
}

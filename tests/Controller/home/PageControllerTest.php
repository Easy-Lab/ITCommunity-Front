<?php


namespace App\Tests\Controller\home;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class PageControllerTest extends WebTestCase
{
    public function testCookie()
    {
        $client = HttpClient::create();

        $crawlerCookie = $client->request('GET', 'https://preprod.itcommunity.fr/page/cookie');

        $this->assertEquals(200, $crawlerCookie->getStatusCode());
    }

    public function testCgu()
    {
        $client = HttpClient::create();

        $crawlerCgu = $client->request('GET', 'https://preprod.itcommunity.fr/page/cgu');

        $this->assertEquals(200, $crawlerCgu->getStatusCode());
    }

    public function testPersonalData()
    {
        $client = HttpClient::create();

        $crawlerData = $client->request('GET', 'https://preprod.itcommunity.fr/page/personal_data');

        $this->assertEquals(200, $crawlerData->getStatusCode());
    }

}

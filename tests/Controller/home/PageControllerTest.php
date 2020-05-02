<?php


namespace App\Tests\Controller\home;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testCookie()
    {
        $clientCookie = static::createClient();

        $crawlerCookie = $clientCookie->request('GET', 'https://preprod.itcommunity.fr/page/cookie');

        $this->assertEquals(200, $clientCookie->getResponse()->getStatusCode());
    }

    public function testCgu()
    {
        $clientCgu = static::createClient();

        $crawlerCgu = $clientCgu->request('GET', 'https://preprod.itcommunity.fr/page/cgu');

        $this->assertEquals(200, $clientCgu->getResponse()->getStatusCode());
    }

    public function testPersonalData()
    {
        $clientData = static::createClient();

        $crawlerData = $clientData->request('GET', 'https://preprod.itcommunity.fr/page/personal_data');

        $this->assertEquals(200, $clientData->getResponse()->getStatusCode());
    }

}

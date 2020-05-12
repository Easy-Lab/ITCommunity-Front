<?php


namespace App\Tests\Controller\home;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class MappingControllerTest extends WebTestCase
{
    public function testData()
    {
        $client = HttpClient::create(['headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $response = $client->request('GET', 'https://preprod.api.itcommunity.fr/users?user_filter[step]=3'
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMoreData()
    {
        $client = HttpClient::create(['headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $response = $client->request('GET', 'https://preprod.api.itcommunity.fr/users?expand=profile,reviews,pictures&user_filter[step]=3&limit=50'
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPopup()
    {
        $client = HttpClient::create(['headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $response = $client->request('GET', 'https://preprod.api.itcommunity.fr/users?expand=profile,reviews,pictures&user_filter[username]=test'
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
}

<?php


namespace App\Tests\Controller\bug;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class BugReportControllerTest extends WebTestCase
{
    public function testForm()
    {
        $dataReport =
            [
                'firstname' => 'test',
                'lastname' => 'test',
                'email' => 'contact@itcommunity.fr',
                'subject' => 'test',
                'body' => 'test',
                'solved'=>false
            ];
        $client = HttpClient::create();
        $responseBug = $client->request('POST', 'https://preprod.api.itcommunity.fr/bugrepports', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataReport)
        ]);
        $this->assertEquals(201, $responseBug->getStatusCode());
    }
}

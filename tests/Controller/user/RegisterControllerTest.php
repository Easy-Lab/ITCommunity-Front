<?php


namespace App\Tests\Controller\user;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class RegisterControllerTest extends WebTestCase
{
    public function testRegister1()
    {
        $data =
            [
                'firstname' => 'test',
                'lastname' => 'test',
                'email' => 'contact@itcommunity.fr',
                'username' => 'test',
                'address' => 'test',
                'address2' => null,
                'city' => 'test',
                'zipcode' => 75000,
                'phone' => '0600000000',
                'step' => 1,
                'informationsEnabled' => true,
                'ip' => '127.0.0.1',
                'plainPassword' => 'azerty',
            ];
        $client = HttpClient::create();
        $response = $client->request('POST','https://preprod.api.itcommunity.fr/users', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($data)
        ]);

        $this->assertEquals(409, $response->getStatusCode());
    }
}

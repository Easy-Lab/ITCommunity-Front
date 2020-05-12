<?php


namespace App\Tests\Controller\evaluation;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class EvaluationControllerTest extends WebTestCase
{
//    public function testIndex()
//    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', 'https://preprod.itcommunity.fr/evaluation/209aa5ed9df573304761a4c7c3198bd78f2b86fe');
//
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }

    public function testForm()
    {
        $client = HttpClient::create();

        $dataEvaluation =
            [
                'hash' => '209aa5ed9df573304761a4c7c3198bd78f2b86fe',
                'rating' => 5,
                'feedback' => 'test',
            ];
        $responseEvaluation = $client->request('POST','https://preprod.api.itcommunity.fr/evaluations', [
            'headers' => ['content_type' => 'application/json'],
            'body' => json_encode($dataEvaluation)
        ]);

        $this->assertEquals(409, $responseEvaluation->getStatusCode());
    }
}

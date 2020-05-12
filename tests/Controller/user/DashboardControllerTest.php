<?php


namespace App\Tests\Controller\user;


use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DashboardControllerTest extends WebTestCase
{
//    public function testIndex()
//    {
//        self::bootKernel();
//        $container = self::$kernel->getContainer();
//        $container = self::$container;
//        $loginService = $container->get(LoginService::class);
//        $dataLogin =
//            [
//                'username' => 'test2',
//                'password' => 'azerty'
//            ];
//        $token = $loginService->getToken($dataLogin);
//        $session = new Session(new MockArraySessionStorage());
//        $session->start();
//        $session->set('email','test@gmail.com');
//        $session->set('username','test2');
//        $session->set('token', $token);
//        $session->set('role', 'ROLE_USER');
//
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/user/dashboard/invitation');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }
}

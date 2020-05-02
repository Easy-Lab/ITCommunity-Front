<?php


namespace App\Controller\login;

use App\Utils\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    /**
     * @Route("/user/request-password", name="user_request_password")
     */
    public function index(Request $request, Validator $validator, LoggerInterface $logger)
    {
        if ($validator->post()) {
            $validator->required('email');
            if ($validator->check()) {
                $client = HttpClient::create();
                $response = $client->request('GET', getenv('API_URL') . '/users/request_password/' . $validator->get('email'));
                if ($response->getStatusCode() == 200) {
                    $validator->success('request_password.success');
                    $logger->info('Lien de reinitialisation du mot de passe envoyé à : '.$validator->get('email'));
                    return $this->redirectToRoute('home');
                }
                if ($response->getStatusCode() == 404) {
                    $validator->keep()->fail('request_password.unknow');
                    $logger->error('Email inconnu pour reinitialiser le mot de passe : '.$validator->get('email'));
                    return $this->redirectToRoute('user_request_password');
                }
                $validator->keep()->fail('request_password.failed');
                return $this->redirectToRoute('user_request_password');
            }
            $validator->keep()->fail('request_password.failed');
            return $this->redirectToRoute('user_request_password');
        }
        $actual_route = $request->get('actual_route', 'user_request_password');
        return $this->render('account/request_password.html.twig', [
            'validator' => $validator,
            'actual_route' => $actual_route,
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
        ]);
    }

    /**
     * @Route("/user/forgot_password/{hash}", name="user_forgot_password")
     */
    public function forgotPassword($hash, Request $request, Validator $validator, LoggerInterface $logger)
    {
        if ($hash) {
            if ($validator->post()) {
                $validator->required('password', 'confirm_password');
                if ($validator->check()) {
                    if ($validator->get('password') != $validator->get('confirm_password')) {
                        $validator->keep()->fail('forgot_password.errorPassword');
                        return $this->redirectToRoute('user_forgot_password', array('hash' => $hash));
                    }
                    $data = [
                        'plainPassword' => $validator->get('password')
                    ];
                    $client = HttpClient::create();
                    $response = $client->request('PATCH', getenv('API_URL') . '/users/forgot_password/' . $hash, [
                        'headers' => ['content_type' => 'application/json'],
                        'body' => json_encode($data)
                    ]);
                    if ($response->getStatusCode() == 200) {
                        $validator->success('forgot_password.success');
                        $logger->info('Mot de passe reinitialiser pour le hash : '.$hash);
                        return $this->redirectToRoute('login');
                    }
                    $validator->keep()->fail('forgot_password.failed');
                    return $this->redirectToRoute('user_forgot_password', array('hash' => $hash));
                }
                $validator->keep()->fail('forgot_password.failed');
                return $this->redirectToRoute('user_forgot_password', array('hash' => $hash));
            }
            $actual_route = $request->get('actual_route', 'user_forgot_password');
            return $this->render('account/reset_password.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'hash'=>$hash,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        $validator->keep()->fail('forgot_password.failed');
        return $this->redirectToRoute('login');
    }
}

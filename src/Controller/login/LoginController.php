<?php


namespace App\Controller\login;

use App\Service\LoginService;
use App\Service\UserService;
use App\Utils\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/se-connecter", name="login")
     */
    public function index(Request $request, Validator $validator, LoginService $loginService, UserService $userService, LoggerInterface $logger)
    {
        $actual_route = $request->get('actual_route', 'login');
        if ($validator->post()) {
            $validator->required('username', 'password');
            if ($validator->check()) {
                $dataLogin =
                    [
                        'username' => $validator->get('username'),
                        'password' => $validator->get('password')
                    ];
                $token = $loginService->getToken($dataLogin);
                if ($token) {
                    if (array_key_exists('token', $token)) {
                        $user = $userService->getUser($token['token'], $validator->get('username'));
                        $data = [
                            'username' => $validator->get('username'),
                            'email' => $user['email'],
                        ];
                        $loginService->createSession($data, $token['token'], $request);
                        $user = $userService->getUser($token['token'], null);
                        if ($user) {
                            if ($user['step'] == 1) {
                                return $this->redirectToRoute('register_step_2');
                            }
                            if ($user['step'] == 2) {
                                return $this->redirectToRoute('register_step_3');
                            }
                            $validator->success('login_success');
                            $logger->info($validator->get('username').' connecté !');
                            return $this->redirectToRoute('user_dashboard_invitation');
                        }
                        $validator->fail('login_failed');
                        $logger->error('Erreur dans getUser() !');
                        return $this->redirectToRoute('login');
                    }
                }
                $validator->fail('login_failed');
                $logger->error('Erreur dans la récupération du token !');
                return $this->redirectToRoute('login');

            }
            $validator->fail();
            return $this->redirectToRoute('login');
        }
        return $this->render('login/index.html.twig', [
            'actual_route' => $actual_route,
            'validator' => $validator,
            'user' => $userService->getUser(),
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
        ]);
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout(Request $request, LoggerInterface $logger)
    {
        $request->getSession()->invalidate();
        $logger->info('Utilisateur déconnecté !');
        return $this->redirectToRoute('home');
    }

}

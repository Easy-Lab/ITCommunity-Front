<?php

namespace App\Controller\ambassador;

use App\Service\LoginService;
use App\Utils\Features;
use App\Utils\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @Route("/ambassador/register")
 */
class RegisterController extends AbstractController
{
    protected $features;

    public function __construct(Features $features)
    {
        $this->features = $features;
    }

    /**
     * @Route("/{hash}", name="ambassador_register")
     */
    public function index($hash = null, Validator $validator, Features $features, Request $request, LoginService $loginService)
    {
        if ($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        $form = [];

        $properties = $features->get("forms.register.step_0");

        if (is_array($properties))
        {
            foreach ($properties as $property)
            {
                $form[] = $property;
            }
        }

        $actual_route = $request->get('actual_route', 'step_0');

        if ($validator->post()) {
            $validator->required('firstname', 'lastname', 'username', 'pseudo', 'password', 'password_confirm', 'address', 'zipcode', 'city', 'terms_of_service');
            $informations = false;

            if ($validator->check()) {
                if ($validator->get('password') !== $validator->get('password_confirm'))
                {
                    $validator->error('password','false');
                    $validator->fail('error_password');
                    return $this->redirectToRoute('ambassador_register');
                }


                if ($validator->get('informations_enabled'))
                {
                    if ($validator->get('informations_enabled') == 'on')
                    {
                        $informations = true;
                    }
                }
                $data =
                    [
                    'firstname' => $validator->get('firstname'),
                    'lastname' => $validator->get('lastname'),
                    'email' => $validator->get('username'),
                    'username' => $validator->get('pseudo'),
                    'plainPassword' => $validator->get('password'),
                    'address' => $validator->get('address'),
                    'address2' => $validator->get('address2'),
                    'zipcode' => $validator->get('zipcode'),
                    'city' => $validator->get('city'),
                    'phone' => $validator->get('phone'),
                    'informationsEnabled' => $informations,
                ];
//                $client = HttpClient::create();
//                $response = $client->request('POST', getenv('API_URL') . '/users', [
//                    'headers' => ['content_type' => 'application/json'],
//                    'body' => json_encode($data)
//                ]);

                $dataLogin =
                    [
                        'username'=>$validator->get('pseudo'),
                        'password'=>$validator->get('password')
                    ];

                $token = $loginService->getToken($dataLogin);
                if ($token){
                    $cookie = new Cookie("token",$token['token'],time()+604800);
                    $res = new Response();
                    $res->headers->setCookie($cookie);
                    $res->sendHeaders();
                }

                return $this->redirectToRoute('home');

            }
        }
        return $this->render('ambassador/register/step_0.html.twig', [
            'validator' => $validator,
            'features' => $features,
            'actual_route' => $actual_route,
            'form_properties' => $form

        ]);
    }

    /**
     * @Route("/step-2", name="ambassador_register_step_2")
     */
    public function step2()
    {

    }

}

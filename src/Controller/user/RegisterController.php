<?php

namespace App\Controller\user;

use App\Service\LoginService;
use App\Service\UserService;
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
 * @Route("/user/register")
 */
class RegisterController extends AbstractController
{
    protected $features;
    protected $session;

    public function __construct(Features $features, SessionInterface $session)
    {
        $this->features = $features;
        $this->session = $session;
    }

    /**
     * @Route("/step-1/{hash}", name="ambassador_register")
     */
    public function index($hash = null, Validator $validator, Features $features, Request $request, LoginService $loginService, UserService $userService)
    {

        $form = [];

        $properties = $features->get("forms.register.step_0");

        if (is_array($properties)) {
            foreach ($properties as $property) {
                $form[] = $property;
            }
        }

        $actual_route = $request->get('actual_route', 'ambassador_register');

        if ($validator->post()) {
            $validator->required('firstname', 'lastname', 'username', 'pseudo', 'password', 'password_confirm', 'address', 'zipcode', 'city', 'terms_of_service');
            $informations = false;

            if ($validator->check()) {
                if ($validator->get('password') !== $validator->get('password_confirm')) {
                    $validator->error('password', 'false');
                    $validator->fail('error_password');
                    return $this->redirectToRoute('ambassador_register');
                }


                if ($validator->get('informations_enabled')) {
                    if ($validator->get('informations_enabled') == 'on') {
                        $informations = true;
                    }
                }
                $data =
                    [
                        'firstname' => $validator->get('firstname'),
                        'lastname' => $validator->get('lastname'),
                        'email' => $validator->get('username'),
                        'username' => $validator->get('pseudo'),
                        'address' => $validator->get('address'),
                        'address2' => $validator->get('address2'),
                        'city' => $validator->get('city'),
                        'zipcode' => $validator->get('zipcode'),
                        'phone' => $validator->get('phone'),
                        'step' => 1,
                        'informationsEnabled' => $informations,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'plainPassword' => $validator->get('password'),
                    ];
                $client = HttpClient::create();
                $response = $client->request('POST', getenv('API_URL') . '/users', [
                    'headers' => ['content_type' => 'application/json'],
                    'body' => json_encode($data)
                ]);

                $dataLogin =
                    [
                        'username' => $validator->get('pseudo'),
                        'password' => $validator->get('password')
                    ];
                if ($response->getStatusCode() == 201) {
                    $token = $loginService->getToken($dataLogin);
                    if ($token) {
                        $loginService->createSession($data, $token['token'], $request);
                    }
                    return $this->redirectToRoute('register_step_2');
                }
                $validator->keep()->fail();
                return $this->redirectToRoute('ambassador_register');
            }
        }
        return $this->render('user/register/step_0.html.twig', [
            'validator' => $validator,
            'features' => $features,
            'actual_route' => $actual_route,
            'form_properties' => $form,
            'user' => $userService->getUser(),
            'hash' => $hash,
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
        ]);
    }

    /**
     * @Route("/step-2", name="register_step_2")
     */
    public function step2(Request $request, Features $features, Validator $validator, UserService $userService)
    {
        if ($request->hasSession() && $this->session) {

            $form = [];

            $properties = $features->get("forms.register.step_2");
            if (is_array($properties)) {
                foreach ($properties as $property) {
                    $form[] = $property;
                }
            }

            $actual_route = $request->get('actual_route', 'register_step_2');

            $client = HttpClient::create();
            $responseGpu = $client->request('GET', 'http://gpu-cpu-api.atcreative.fr/api/gpu');
            $statusCodeGpu = $responseGpu->getStatusCode();
            if ($statusCodeGpu == 200) {
                $contentGpu = $responseGpu->toArray();
            } else {
                $contentGpu = null;
            }

            $responseCpu = $client->request('GET', 'http://gpu-cpu-api.atcreative.fr/api/cpu');
            $statusCodeCpu = $responseCpu->getStatusCode();
            if ($statusCodeCpu == 200) {
                $contentCpu = $responseCpu->toArray();
            } else {
                $contentCpu = null;
            }

            if ($validator->post()) {

                $validator->required('gpu', 'gpu_rating', 'gpu_feedback', 'cpu', 'cpu_rating', 'cpu_feedback');

                if ($validator->get('gpu') == null) {
                    $validator->error('gpu', 'required');
                    $validator->keep()->fail('error_gpu');
                    return $this->redirectToRoute('register_step_2');
                }

                if ($validator->get('cpu') == null) {
                    $validator->error('cpu', 'required');
                    $validator->keep()->fail('error_cpu');
                    return $this->redirectToRoute('register_step_2');
                }

                if ($validator->get('gpu_rating') == null) {
                    $validator->error('gpu_rating', 'required');
                    $validator->keep()->fail('error_gpu_rating');
                    return $this->redirectToRoute('register_step_2');
                }

                if ($validator->get('cpu_rating') == null) {
                    $validator->error('cpu_rating', 'required');
                    $validator->keep()->fail('error_cpu_rating');
                    return $this->redirectToRoute('register_step_2');
                }

                if ($validator->check()) {
                    $urlGpu = 'http://gpu-cpu-api.atcreative.fr/api/gpu/' . $validator->get('gpu');
                    $infoGpu = $client->request('GET', $urlGpu);
                    $httpCodeGpu = $infoGpu->getStatusCode();
                    if ($httpCodeGpu == 200) {
                        $contentInfoGpu = $infoGpu->toArray();
                    } else {
                        $validator->keep()->fail();
                        return $this->redirectToRoute('register_step_2');
                    }
                    $dataGpu =
                        [
                            'body' => $validator->get('gpu_feedback'),
                            'rating' => (int)$validator->get('gpu_rating'),
                            'type' => 'gpu',
                            'name_component' => $contentInfoGpu['product_name'],
                            'company_component' => $contentInfoGpu['company'],
                            'other_information_component' => $contentInfoGpu['gpu_clock']
                        ];

                    $urlCpu = 'http://gpu-cpu-api.atcreative.fr/api/cpu/' . $validator->get('cpu');
                    $infoCpu = $client->request('GET', $urlCpu);
                    $httpCodeCpu = $infoCpu->getStatusCode();
                    if ($httpCodeCpu == 200) {
                        $contentInfoCpu = $infoCpu->toArray();
                    } else {
                        $validator->keep()->fail();
                        return $this->redirectToRoute('register_step_2');
                    }

                    $dataCpu =
                        [
                            'body' => $validator->get('cpu_feedback'),
                            'rating' => (int)$validator->get('cpu_rating'),
                            'type' => 'cpu',
                            'name_component' => $contentInfoCpu['product_name'],
                            'company_component' => $contentInfoCpu['company'],
                            'other_information_component' => $contentInfoCpu['cores'] . ' cores, ' . $contentInfoCpu['clock'] . ', ' . $contentInfoCpu['socket']
                        ];
                    $clientPostGpu = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);

                    $clientPostCpu = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);

                    $responseGpu = $clientPostGpu->request('POST', getenv('API_URL') . '/reviews', [
                        'body' => json_encode($dataGpu)
                    ]);

                    $responseCpu = $clientPostCpu->request('POST', getenv('API_URL') . '/reviews', [
                        'body' => json_encode($dataCpu)
                    ]);
                    $userService->addStep(2);
                    return $this->redirectToRoute('register_step_3');

                }

            }
            return $this->render('user/register/step_2.html.twig', [
                'validator' => $validator,
                'features' => $features,
                'actual_route' => $actual_route,
                'form_properties' => $form,
                'elementGpu' => array_reverse($contentGpu),
                'elementCpu' => array_reverse($contentCpu),
                'user' => $userService->getUser(),
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        $validator->fail('no_session');
        return $this->redirectToRoute('login');

    }

    /**
     * @Route("/step-3", name="register_step_3")
     */
    public function step3(Features $features, Request $request, Validator $validator, UserService $userService)
    {
        $profilePicture = null;
        $environmentPictures = null;
        if ($request->hasSession() && $this->session) {
            if ($validator->post()) {
                $userService->addStep(3);
                return $this->redirectToRoute('user_dashboard_invitation');
            }

            $user = $userService->getUser();
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $environmentPictures = $userService->getEnvironmentPictures();
            }
            $form = [];

            $properties = $features->get("forms.register.step_3");
            if (is_array($properties)) {
                foreach ($properties as $property) {
                    $form[] = $property;
                }
            }

            $actual_route = $request->get('actual_route', 'register_step_3');

            return $this->render('user/register/step_3.html.twig', [
                'validator' => $validator,
                'features' => $features,
                'actual_route' => $actual_route,
                'form_properties' => $form,
                'pictures_count' => $features->get('environment.pictures.count'),
                'token' => $this->session->get('token'),
                'user' => $userService->getUser(),
                'profilePicture' => $profilePicture,
                'environmentPictures' => $environmentPictures,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        $validator->fail('no_session');
        return $this->redirectToRoute('login');
    }
}

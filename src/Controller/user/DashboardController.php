<?php


namespace App\Controller\user;


use App\Service\UserService;
use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class DashboardController extends AbstractController
{
    protected $features;
    protected $session;

    public function __construct(Features $features, SessionInterface $session)
    {
        $this->features = $features;
        $this->session = $session;
    }

    /**
     * @Route("/dashboard/invitation", name="user_dashboard_invitation")
     */
    public function index(Request $request, Validator $validator, UserService $userService)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            if ($user) {

                $profilePicture = null;
                $myPoints = 0;
                $messages = null;
                if ($user) {
                    $profilePicture = $userService->getProfilePicture();
                    $myPoints = $userService->getMyPoints();
                    $messages = $userService->getMessages();
                }
                $actual_route = $request->get('actual_route', 'user_dashboard_invitation');
                return $this->render('user/dashboard/invitation/index.html.twig', [
                    'validator' => $validator,
                    'actual_route' => $actual_route,
                    'profilePicture' => $profilePicture,
                    'user' => $user,
                    'myPoints' => $myPoints,
                    'messages' => $messages,
                    'google_analytics_id' => getenv("ANALYTICS_KEY"),
                ]);
            }
            return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile", name="user_dashboard_profile")
     */
    public function profile(Request $request, Validator $validator, UserService $userService)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $environmentPictures = null;
            $gpu = null;
            $cpu = null;
            $environment0 = null;
            $environment1 = null;
            $environment2 = null;
            $messages = null;
            $evaluations = null;
            $myPoints = 0;

            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $environmentPictures = $userService->getEnvironmentPictures();
                $gpu = $userService->getGpu();
                $cpu = $userService->getCpu();
                $environment0 = $userService->getEnvironmentPictures0();
                $environment1 = $userService->getEnvironmentPictures1();
                $environment2 = $userService->getEnvironmentPictures2();
                $messages = $userService->getMessages();
                $evaluations = $userService->getEvaluations();
                $myPoints = $userService->getMyPoints();

                $actual_route = $request->get('actual_route', 'user_dashboard_profile');
                return $this->render('user/dashboard/profile/index.html.twig', [
                    'validator' => $validator,
                    'actual_route' => $actual_route,
                    'profilePicture' => $profilePicture,
                    'pictures' => $environmentPictures,
                    'user' => $user,
                    'editable' => true,
                    'gpu' => $gpu,
                    'cpu' => $cpu,
                    'environment0' => $environment0,
                    'environment1' => $environment1,
                    'environment2' => $environment2,
                    'structure' => $userService->getStructure(),
                    'messages' => $messages,
                    'evaluations' => $evaluations,
                    'myPoints' => $myPoints,
                    'google_analytics_id' => getenv("ANALYTICS_KEY"),
                ]);
            }
            return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/profile/{username}", name="user_unknow_profile")
     */
    public function unknowProfile($username, Request $request, Validator $validator, UserService $userService)
    {
        $client = HttpClient::create(['headers' => [
            'Content-Type' => 'application/json',
        ]]);
        $response = $client->request('GET', getenv('API_URL') . '/users?expand=profile,reviews,pictures&user_filter[username]=' . $username
        );
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            $data = $response->toArray();
        } else {
            return $this->redirectToRoute('home');
        }

        $profilePicture = null;
        $environmentPictures = null;
        $gpu = null;
        $cpu = null;
        $environment0 = null;
        $environment1 = null;
        $environment2 = null;
        $messages = null;
        $evaluations = null;
        $user = $data['users'][0];
        $tabPictures = [];
        if ($user['pictures']) {
            foreach ($user['pictures'] as $picture) {
                if ($picture['name'] == 'profile_picture') {
                    $profilePicture = $picture;
                } else {
                    if ($picture['name'] == 'environment_0') $environment0 = $picture;
                    if ($picture['name'] == 'environment_1') $environment1 = $picture;
                    if ($picture['name'] == 'environment_2') $environment2 = $picture;
                    $tabPictures[] = $picture;
                }
            }
            $environmentPictures = $tabPictures;

        }
        $gpu = $user['reviews'][0];
        $cpu = $user['reviews'][1];
        $messages = $userService->getMessages($username);
        $evaluations = $userService->getEvaluations($username);

        $actual_route = $request->get('actual_route', 'user_profile');
        return $this->render('user/dashboard/profile/index.html.twig', [
            'validator' => $validator,
            'actual_route' => $actual_route,
            'profilePicture' => $profilePicture,
            'pictures' => $environmentPictures,
            'user' => $user,
            'editable' => false,
            'gpu' => $gpu,
            'cpu' => $cpu,
            'environment0' => $environment0,
            'environment1' => $environment1,
            'environment2' => $environment2,
            'structure' => $userService->getStructure(),
            'messages' => $messages,
            'evaluations' => $evaluations,
            'google_analytics_id' => getenv("ANALYTICS_KEY"),
        ]);

    }

    /**
     * @Route("/dashboard/profile/unsubscribe", name="user_dashboard_unsubscribe")
     */
    public function unsub(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();

                $actual_route = $request->get('actual_route', 'user_dashboard_unsubscribe');
                return $this->render('user/dashboard/profile/unsubscribe.html.twig', [
                    'validator' => $validator,
                    'actual_route' => $actual_route,
                    'profilePicture' => $profilePicture,
                    'user' => $user,
                    'reasons' => $features->get('account.deletion.reasons'),
                    'myPoints' => $myPoints,
                    'google_analytics_id' => getenv("ANALYTICS_KEY"),
                ]);
            }
            return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile/preference", name="user_dashboard_preference")
     */
    public function preference(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            } else {
                return $this->redirectToRoute('login');
            }
            if ($validator->post()) {
                if ($validator->get('informations_enabled')) {
                    $informations = true;
                } else {
                    $informations = false;
                }
                $client = HttpClient::create(['headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]]);
                $data =
                    [
                        'informationsEnabled' => $informations
                    ];
                $response = $client->request('PATCH', getenv('API_URL') . '/users/' . $this->session->get('username')
                    , [
                        'headers' => ['content_type' => 'application/json'],
                        'body' => json_encode($data)
                    ]);
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    $validator->success('update.success');
                    return $this->redirectToRoute('user_dashboard_profile');
                } else {
                    $validator->keep()->fail();
                    return $this->redirectToRoute('user_dashboard_preference');
                }
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_preference');
            return $this->render('user/dashboard/profile/mails.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
                'myPoints' => $myPoints,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile/password", name="user_dashboard_password")
     */
    public function password(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            } else {
                return $this->redirectToRoute('login');
            }
            if ($validator->post()) {
                $validator->required('password', 'passwordConfirm');
                if ($validator->check()) {
                    if ($validator->get('password') == $validator->get('passwordConfirm')) {
                        $client = HttpClient::create(['headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $this->session->get('token')
                        ]]);
                        $data =
                            [
                                'plainPassword' => $validator->get('password')
                            ];
                        $response = $client->request('PATCH', getenv('API_URL') . '/users/' . $this->session->get('username')
                            , [
                                'headers' => ['content_type' => 'application/json'],
                                'body' => json_encode($data)
                            ]);
                        $statusCode = $response->getStatusCode();
                        if ($statusCode == 200) {
                            $request->getSession()->invalidate();
                            $validator->success('update.success');
                            return $this->redirectToRoute('login');
                        } else {
                            $validator->keep()->fail();
                            return $this->redirectToRoute('user_dashboard_password');
                        }
                    }
                }
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_password');
            return $this->render('user/dashboard/profile/password.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
                'myPoints' => $myPoints,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile/informations", name="user_dashboard_informations")
     */
    public function informations(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            } else {
                return $this->redirectToRoute('login');
            }

            $properties = $features->get("forms.profile.informations");
            if (is_array($properties)) {
                foreach ($properties as $property) {
                    $form[] = $property;
                }
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_informations');
            if ($validator->post()) {
                $validator->required('firstname', 'lastname', 'username', 'pseudo');
                if ($validator->check()) {
                    $client = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);
                    $data =
                        [
                            'firstname' => $validator->get('firstname'),
                            'lastname' => $validator->get('lastname'),
                            'email' => $validator->get('username'),
                            'username' => $validator->get('pseudo')
                        ];
                    $response = $client->request('PATCH', getenv('API_URL') . '/users/' . $this->session->get('username')
                        , [
                            'headers' => ['content_type' => 'application/json'],
                            'body' => json_encode($data)
                        ]);
                    $statusCode = $response->getStatusCode();
                    if ($statusCode == 200) {
                        $request->getSession()->invalidate();
                        $validator->success('update.success');
                        return $this->redirectToRoute('login');
                    } else {
                        $validator->keep()->fail();
                        return $this->redirectToRoute('user_dashboard_informations');
                    }
                }
            }
            return $this->render('user/dashboard/profile/informations.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
                'myPoints' => $myPoints,
                'form_properties' => $form,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile/products", name="user_dashboard_products")
     */
    public function products(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
                $gpu = $userService->getGpu();
                $cpu = $userService->getCpu();
            } else {
                return $this->redirectToRoute('login');
            }

            $properties = $features->get("forms.profile.products");
            if (is_array($properties)) {
                foreach ($properties as $property) {
                    $form[] = $property;
                }
            }

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

            $actual_route = $request->get('actual_route', 'user_dashboard_products');
            if ($validator->post()) {
                $validator->required('gpu', 'gpu_rating', 'gpu_feedback', 'cpu', 'cpu_rating', 'cpu_feedback');

                if ($validator->get('gpu') == null) {
                    $validator->error('gpu', 'required');
                    $validator->keep()->fail('error_gpu');
                    return $this->redirectToRoute('user_dashboard_products');
                }

                if ($validator->get('cpu') == null) {
                    $validator->error('cpu', 'required');
                    $validator->keep()->fail('error_cpu');
                    return $this->redirectToRoute('user_dashboard_products');
                }

                if ($validator->get('gpu_rating') == null) {
                    $validator->error('gpu_rating', 'required');
                    $validator->keep()->fail('error_gpu_rating');
                    return $this->redirectToRoute('user_dashboard_products');
                }

                if ($validator->get('cpu_rating') == null) {
                    $validator->error('cpu_rating', 'required');
                    $validator->keep()->fail('error_cpu_rating');
                    return $this->redirectToRoute('user_dashboard_products');
                }

                if ($validator->check()) {
                    $urlGpu = 'http://gpu-cpu-api.atcreative.fr/api/gpu/' . $validator->get('gpu');
                    $infoGpu = $client->request('GET', $urlGpu);
                    $httpCodeGpu = $infoGpu->getStatusCode();
                    if ($httpCodeGpu == 200) {
                        $contentInfoGpu = $infoGpu->toArray();
                    } else {
                        $validator->keep()->fail();
                        return $this->redirectToRoute('user_dashboard_products');
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
                        return $this->redirectToRoute('user_dashboard_products');
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
                    $clientPatchGpu = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);

                    $clientPatchCpu = HttpClient::create(['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]]);
                    $responseGpu = $clientPatchGpu->request('PATCH', getenv('API_URL') . '/reviews/' . $gpu['hash'], [
                        'body' => json_encode($dataGpu)
                    ]);

                    $responseCpu = $clientPatchCpu->request('PATCH', getenv('API_URL') . '/reviews/' . $cpu['hash'], [
                        'body' => json_encode($dataCpu)
                    ]);
                    if ($responseGpu->getStatusCode() == 200 && $responseCpu->getStatusCode() == 200) {
                        $validator->success('reviews.success_update');
                        return $this->redirectToRoute('user_dashboard_profile');
                    }
                }
                $validator->keep()->fail();
                return $this->redirectToRoute('user_dashboard_products');
            }
            return $this->render('user/dashboard/profile/products.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
                'myPoints' => $myPoints,
                'form_properties' => $form,
                'elementGpu' => array_reverse($contentGpu),
                'elementCpu' => array_reverse($contentCpu),
                'gpu' => $gpu,
                'cpu' => $cpu,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/dashboard/profile/pictures", name="user_dashboard_pictures")
     */
    public function pictures(Request $request, Validator $validator, UserService $userService, Features $features)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            $environmentPictures = null;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
                $environmentPictures = $userService->getEnvironmentPictures();

                $actual_route = $request->get('actual_route', 'user_dashboard_pictures');
                return $this->render('user/dashboard/profile/pictures.html.twig', [
                    'validator' => $validator,
                    'actual_route' => $actual_route,
                    'profilePicture' => $profilePicture,
                    'user' => $user,
                    'myPoints' => $myPoints,
                    'pictures' => $environmentPictures,
                    'pictures_count' => $features->get('environment.pictures.count'),
                    'google_analytics_id' => getenv("ANALYTICS_KEY"),
                ]);
            }
            return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('login');
    }

}

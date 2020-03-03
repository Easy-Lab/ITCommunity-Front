<?php


namespace App\Controller\ambassador;


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
            $profilePicture = null;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_invitation');
            return $this->render('ambassador/dashboard/invitation/index.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user
            ]);
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

            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $environmentPictures = $userService->getEnvironmentPictures();
                $gpu = $userService->getGpu();
                $cpu = $userService->getCpu();
                $environment0 = $userService->getEnvironmentPictures0();
                $environment1 = $userService->getEnvironmentPictures1();
                $environment2 = $userService->getEnvironmentPictures2();
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_profile');
            return $this->render('ambassador/dashboard/profile/index.html.twig', [
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

            ]);
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
        $response = $client->request('GET', getenv('API_URL') . '/users?expand=profile&user_filter[username]=' . $username
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

        $actual_route = $request->get('actual_route', 'user_profile');
        return $this->render('ambassador/dashboard/profile/index.html.twig', [
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
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_unsubscribe');
            return $this->render('ambassador/dashboard/profile/unsubscribe.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
                'reasons' => $features->get('account.deletion.reasons')
            ]);
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
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
            }
            $actual_route = $request->get('actual_route', 'user_dashboard_preference');
            return $this->render('ambassador/dashboard/profile/unsubscribe.html.twig', [
                'validator' => $validator,
                'actual_route' => $actual_route,
                'profilePicture' => $profilePicture,
                'user' => $user,
            ]);
        }
        return $this->redirectToRoute('login');
    }

}

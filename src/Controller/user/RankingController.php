<?php


namespace App\Controller\user;


use App\Service\UserService;
use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RankingController extends AbstractController
{
    protected $features;
    protected $session;

    public function __construct(Features $features, SessionInterface $session)
    {
        $this->features = $features;
        $this->session = $session;
    }

    /**
     * @Route("/user/classement", name="user_dashboard_ranking")
     */
    public function index(Validator $validator, Request $request, UserService $userService)
    {
        if ($request->hasSession() && $this->session) {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user) {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            }
            $ranking = $userService->getRanking();
            $actual_route = $request->get('actual_route', 'user_dashboard_ranking');

            return $this->render('user/ranking/index.html.twig', [
                'validator' => $validator,
                'ranking' => $ranking,
                'actual_route' => $actual_route,
                'user' => $user,
                'profilePicture' => $profilePicture,
                'myPoints' => $myPoints,
                'google_analytics_id' => getenv("ANALYTICS_KEY"),
            ]);

        }
        return $this->redirectToRoute('login');
    }
}

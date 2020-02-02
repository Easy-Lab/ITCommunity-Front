<?php


namespace App\Controller\ambassador;


use App\Service\UserService;
use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/dashboard")
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
     * @Route("/invitation", name="user_dashboard_invitation")
     */
    public function index(Request $request, Validator $validator, UserService $userService){
        if ($request->hasSession() && $this->session){
            $user = $userService->getUser();
            $actual_route = $request->get('actual_route', 'user_dashboard_invitation');
            return $this->render('ambassador/dashboard/invitation/index.html.twig', [
                'validator'=>$validator,
                'actual_route'=>$actual_route,
                'user'=>$user
            ]);
        }
        return $this->redirectToRoute('login');
    }

}

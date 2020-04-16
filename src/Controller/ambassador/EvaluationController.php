<?php


namespace App\Controller\ambassador;


use App\Service\UserService;
use App\Utils\Features;
use App\Utils\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends AbstractController
{
    protected $features;
    protected $session;

    public function __construct(Features $features, SessionInterface $session)
    {
        $this->features = $features;
        $this->session = $session;
    }

    /**
     * @Route("/user/evaluation", name="user_dashboard_evaluation")
     */
    public function index(Validator $validator, Request $request, UserService $userService)
    {
        if ($request->hasSession() && $this->session)
        {
            $user = $userService->getUser();
            $profilePicture = null;
            $myPoints = 0;
            if ($user)
            {
                $profilePicture = $userService->getProfilePicture();
                $myPoints = $userService->getMyPoints();
            }
            $evaluations = $userService->getEvaluations();
            $actual_route = $request->get('actual_route', 'user_dashboard_evaluation');

        return $this->render('user/evaluation/index.html.twig', [
            'validator' => $validator,
            'evaluations'=>$evaluations,
            'actual_route'=>$actual_route,
            'user'=>$user,
            'profilePicture'=>$profilePicture,
            'myPoints'=>$myPoints
        ]);

        }




    }
}
